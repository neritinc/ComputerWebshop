<?php

namespace App\Http\Controllers;

use App\Models\Product as CurrentModel;
use App\Models\ProductParameter;
use App\Http\Requests\StoreProductRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateProductRequest as UpdateCurrentModelRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Termékek listázása szűréssel és kereséssel
     */
    public function index(Request $request)
    {
        return $this->apiResponse(function () use ($request) {
            $query = CurrentModel::with(['category', 'company', 'parameters.unit', 'pics']);

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('description', 'like', "%$search%");
                });
            }

            $products = $query->get();
            return $this->attachPrimaryImagePath($products);
        });
    }

    /**
     * Új termék mentése
     */
    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            $this->authorize('create', CurrentModel::class);
            return CurrentModel::create($request->validated());
        });
    }

    /**
     * Egy termék részletei
     */
    public function show(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            $product = CurrentModel::with(['category', 'company', 'parameters.unit', 'pics'])->findOrFail($id);
            return $this->attachPrimaryImagePath(collect([$product]))->first();
        });
    }

    /**
     * Termék frissítése
     */
    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $row = CurrentModel::findOrFail($id);
            $this->authorize('update', $row);
            
            $row->update($request->validated());

            // Paraméterek frissítése (ha érkezik 'params' tömb)
            if ($request->has('params')) {
                foreach ($request->params as $paramId => $value) {
                    ProductParameter::updateOrCreate(
                        ['product_id' => $row->id, 'parameter_id' => $paramId],
                        ['value' => $value]
                    );
                }
            }

            return $row->load(['category', 'company', 'parameters.unit', 'pics']);
        });
    }

    /**
     * Termék törlése
     */
    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            $row = CurrentModel::findOrFail($id);
            $this->authorize('delete', $row);
            $row->delete();
            return ['id' => $id];
        });
    }

    private function attachPrimaryImagePath($products)
    {
        $files = $this->productImageFiles();
        $fileSet = array_fill_keys($files, true);
        $usedGuessedFiles = [];

        foreach ($products as $product) {
            $modelCodes = $this->extractModelCodes((string) $product->name);

            $primaryFromRelation = collect($product->pics ?? [])
                ->pluck('image_path')
                ->filter()
                ->sortBy(fn ($path) => preg_match('/_1\.[a-z0-9]+$/i', (string) $path) ? 0 : 1)
                ->first(function ($path) use ($fileSet, $modelCodes) {
                    $file = (string) $path;
                    if (!isset($fileSet[$file])) return false;
                    if ($modelCodes && !$this->pathContainsModelCode($file, $modelCodes)) return false;
                    return true;
                });

            $primaryFromAnyRelation = collect($product->pics ?? [])
                ->pluck('image_path')
                ->filter()
                ->sortBy(fn ($path) => preg_match('/_1\.[a-z0-9]+$/i', (string) $path) ? 0 : 1)
                ->first(fn ($path) => $modelCodes ? $this->pathContainsModelCode((string) $path, $modelCodes) : true);

            $resolved = $primaryFromRelation;

            if (!$resolved) {
                $resolved = $this->guessImageFromFiles((string) $product->name, $files, $usedGuessedFiles);
                if ($resolved) {
                    $usedGuessedFiles[$resolved] = true;
                } elseif (isset($fileSet[(string) $primaryFromAnyRelation])) {
                    $resolved = $primaryFromAnyRelation;
                }
            }

            $product->setAttribute('primary_image_path', $resolved ?: null);
            $product->setAttribute(
                'primary_image_url',
                $resolved ? url('images/products/' . $resolved) : null
            );
        }

        return $products;
    }

    private function productImageFiles(): array
    {
        $paths = glob(public_path('images/products/*.*')) ?: [];
        return array_map(static fn ($path) => basename($path), $paths);
    }

    private function guessImageFromFiles(string $productName, array $files, array $excluded = []): ?string
    {
        if (!$files) return null;

        $name = Str::lower($productName);
        $modelCodes = $this->extractModelCodes($productName);
        preg_match_all('/\d{6,}/', $name, $codeMatches);
        $codes = $codeMatches[0] ?? [];
        $tokens = preg_split('/[^a-z0-9]+/i', $name) ?: [];
        $tokens = array_values(array_filter($tokens, function ($token) {
            return strlen($token) >= 3 && !in_array($token, ['core', 'ghz', 'box', 'tray', 'plus'], true);
        }));

        $bestScore = -1;
        $bestFile = null;

        foreach ($files as $file) {
            if (isset($excluded[$file])) continue;

            $fileLower = Str::lower($file);
            $score = 0;

            foreach ($modelCodes as $modelCode) {
                if (str_contains($fileLower, Str::lower($modelCode))) {
                    $score += 10;
                }
            }

            foreach ($codes as $code) {
                $trimmed = ltrim($code, '0');
                if (str_contains($fileLower, $code)) $score += 8;
                if ($trimmed !== '' && str_contains($fileLower, $trimmed)) $score += 6;
            }

            foreach ($tokens as $token) {
                if (str_contains($fileLower, $token)) {
                    // Model tokens with both letters and numbers are stronger identifiers (e.g. 27g4x).
                    $score += preg_match('/[a-z]/i', $token) && preg_match('/\d/', $token) ? 2 : 1;
                }
            }

            if (preg_match('/_1\.[a-z0-9]+$/i', $fileLower)) {
                $score += 1;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestFile = $file;
            }
        }

        // If a product name includes a specific model code, don't allow fuzzy mismatches.
        if ($modelCodes && $bestFile) {
            $bestFileLower = Str::lower($bestFile);
            $codeMatched = collect($modelCodes)->contains(
                fn ($code) => str_contains($bestFileLower, Str::lower($code))
            );
            // Keep strict matching by default, but allow close family fallback
            // when textual similarity is already high (prevents empty image cards).
            if (!$codeMatched && $bestScore < 8) return null;
        }

        return $bestScore >= 2 ? $bestFile : null;
    }

    private function extractModelCodes(string $productName): array
    {
        preg_match_all('/[a-z]+\d+[a-z0-9]*/i', Str::lower($productName), $modelCodeMatches);
        return array_values(array_filter($modelCodeMatches[0] ?? [], fn ($v) => strlen($v) >= 6));
    }

    private function pathContainsModelCode(string $imagePath, array $modelCodes): bool
    {
        $pathLower = Str::lower($imagePath);
        foreach ($modelCodes as $code) {
            if (str_contains($pathLower, Str::lower($code))) {
                return true;
            }
        }
        return false;
    }
}
