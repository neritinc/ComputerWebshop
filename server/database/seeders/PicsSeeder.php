<?php

namespace Database\Seeders;

use App\Models\Pic;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PicsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('csv/pics.csv');
        if (!is_file($path)) {
            return;
        }

        $products = Product::query()->get(['id', 'name']);
        if ($products->isEmpty()) {
            return;
        }

        $productMeta = $products->map(function ($p) {
            $name = (string) ($p->name ?? '');
            return [
                'id' => (int) $p->id,
                'slug' => $this->normalizeText($name),
                'model' => $this->extractModelCode($name),
                'tokens' => $this->extractTokens($name),
            ];
        })->all();

        $rows = [];
        $seen = [];
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return;
        }

        while (($cols = fgetcsv($handle, 0, ';')) !== false) {
            if (count($cols) < 2) continue;

            $imagePath = trim((string) $cols[1]);

            if ($imagePath === '') continue;

            // The first CSV column is treated as a picture serial, not a product FK.
            // Product is resolved from file name to avoid wrong FK mappings.
            $productId = $this->resolveProductIdFromImagePath($imagePath, $productMeta);
            if ($productId <= 0) continue;

            $pair = $productId . '|' . $imagePath;
            if (isset($seen[$pair])) continue;
            $seen[$pair] = true;

            $rows[] = [
                'product_id' => $productId,
                'image_path' => $imagePath,
            ];
        }
        fclose($handle);

        if (!empty($rows)) {
            Pic::query()->insert($rows);
        }
    }

    private function resolveProductIdFromImagePath(string $imagePath, array $productMeta): int
    {
        $file = (string) basename($imagePath);
        $fileBase = preg_replace('/_\d+\.[a-z0-9]+$/i', '', Str::lower($file));
        $fileNorm = $this->normalizeText($fileBase);
        $fileTokens = $this->extractTokens($fileBase);
        $fileTokenSet = array_fill_keys($fileTokens, true);

        $bestId = 0;
        $bestScore = 0;

        foreach ($productMeta as $meta) {
            $score = 0;
            $model = (string) ($meta['model'] ?? '');

            if ($model !== '') {
                if (str_contains($fileNorm, $model)) {
                    $score += 12;
                } else {
                    continue;
                }
            }

            foreach (($meta['tokens'] ?? []) as $t) {
                if (!isset($fileTokenSet[$t])) continue;
                if (strlen((string) $t) >= 7) $score += 3;
                elseif (strlen((string) $t) >= 4) $score += 2;
                else $score += 1;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestId = (int) ($meta['id'] ?? 0);
            }
        }

        return $bestScore >= 12 ? $bestId : 0;
    }

    private function normalizeText(string $value): string
    {
        return preg_replace('/[^a-z0-9]+/i', '', Str::lower($value)) ?: '';
    }

    private function extractModelCode(string $name): string
    {
        if (preg_match('/\(([^)]+)\)/', $name, $m)) {
            return $this->normalizeText((string) ($m[1] ?? ''));
        }
        if (preg_match('/([A-Z0-9]{4,}(?:-[A-Z0-9]{2,})+)$/i', trim($name), $m2)) {
            return $this->normalizeText((string) ($m2[1] ?? ''));
        }
        return '';
    }

    private function extractTokens(string $value): array
    {
        preg_match_all('/[a-z0-9]+/i', Str::lower($value), $m);
        $tokens = [];
        foreach (($m[0] ?? []) as $t) {
            $t = (string) $t;
            if ($t === '') continue;
            if (strlen($t) >= 3 || ctype_digit($t)) $tokens[] = $t;
        }
        return array_values(array_unique($tokens));
    }
}
