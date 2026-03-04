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
            $product = CurrentModel::with([
                'category',
                'company',
                'parameters.unit',
                'pics',
                'comments.user:id,name',
            ])->findOrFail($id);
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

        foreach ($products as $product) {
            $relationPaths = collect($product->pics ?? [])
                ->pluck('image_path')
                ->filter()
                ->map(fn ($path) => basename((string) $path))
                ->filter(fn ($path) => isset($fileSet[(string) $path]))
                ->sortBy(fn ($path) => preg_match('/_1\.[a-z0-9]+$/i', (string) $path) ? 0 : 1)
                ->values()
                ->all();

            if (!$relationPaths) {
                $fallback = $this->resolveImageFromModelCode((string) $product->name, $files);
                $relationPaths = $fallback ? [$fallback] : [];
            }

            $resolved = $relationPaths[0] ?? null;

            $product->setAttribute('primary_image_path', $resolved ?: null);
            $product->setAttribute(
                'primary_image_url',
                $resolved ? url('images/products/' . $resolved) : null
            );
            $product->setAttribute('resolved_image_paths', $relationPaths);
            $product->setAttribute(
                'resolved_image_urls',
                array_map(fn ($file) => url('images/products/' . $file), $relationPaths)
            );
        }

        return $products;
    }

    private function productImageFiles(): array
    {
        $paths = glob(public_path('images/products/*.*')) ?: [];
        return array_map(static fn ($path) => basename($path), $paths);
    }

    private function resolveImageFromModelCode(string $productName, array $files): ?string
    {
        if (!$files) return null;

        $tokens = $this->extractSearchTokens($productName);
        if (!$tokens) return null;

        $bestFile = null;
        $bestScore = -1;

        foreach ($files as $file) {
            $fileTokens = $this->extractSearchTokens((string) $file);
            if (!$fileTokens) continue;

            $fileTokenSet = array_fill_keys($fileTokens, true);
            $score = 0;

            foreach ($tokens as $token) {
                if (!isset($fileTokenSet[$token])) continue;
                $score += ctype_digit($token) ? 3 : 2;
            }

            if (preg_match('/_1\.[a-z0-9]+$/i', (string) $file)) {
                $score += 1;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestFile = $file;
            }
        }

        return $bestScore >= 3 ? $bestFile : null;
    }

    private function extractSearchTokens(string $value): array
    {
        preg_match_all('/[a-z0-9]+/i', Str::lower($value), $matches);
        $rawTokens = $matches[0] ?? [];
        $tokens = [];

        foreach ($rawTokens as $token) {
            $token = (string) $token;
            if ($token === '') continue;

            if (ctype_digit($token)) {
                $trimmed = ltrim($token, '0');
                if ($trimmed !== '') {
                    $tokens[] = $trimmed;
                }
                if (strlen($token) >= 6) {
                    $tokens[] = $token;
                }
                continue;
            }

            if (strlen($token) >= 4) {
                $tokens[] = $token;
            }
        }

        return array_values(array_unique($tokens));
    }
}
