<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Mail\OrderPlacedMail;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreOrderRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            $cart = Cart::with(['user', 'items.product.pics'])->findOrFail($request->validated('cart_id'));

            $this->authorize('view', $cart);

            if ($cart->items->isEmpty()) {
                abort(422, 'A kosár üres, nem lehet rendelést leadni.');
            }

            $items = $cart->items->map(function ($item) {
                $product = $item->product;

                if (!$product) {
                    abort(422, 'A kosárban lévő egyik termék törlésre került.');
                }

                $quantity = $item->pcs ?? $item->quantity ?? 0;
                $unitPrice = (float) $product->price;
                $imagePath = $this->resolveProductImage($product);

                return [
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'image' => $imagePath,
                    'line_total' => $unitPrice * $quantity,
                ];
            })->values()->all();

            $total = array_sum(array_column($items, 'line_total'));
            $orderCode = 'ORD-' . now()->format('YmdHis') . '-' . $cart->id;

            Mail::to($cart->user->email)->send(
                new OrderPlacedMail($cart->user, $items, $total, $orderCode)
            );

            return [
                'order_code' => $orderCode,
                'email_sent_to' => $cart->user->email,
                'item_count' => count($items),
                'total' => $total,
            ];
        });
    }

    private function resolveProductImage(Product $product): ?string
    {
        $candidates = [];

        // 1) pics tábla
        $pic = $product->relationLoaded('pics')
            ? $product->pics->first()
            : $product->pics()->first();
        if ($pic && $pic->image_path) {
            if (str_starts_with($pic->image_path, 'http://') || str_starts_with($pic->image_path, 'https://')) {
                return $pic->image_path;
            }
            $candidates[] = $this->makePublicPath($pic->image_path);
        }

        // 2) slug alapú keresés (több variáns)
        $slug = Str::slug($product->name, '_');
        foreach ($this->findImagesBySlug($slug) as $match) {
            $candidates[] = $match;
        }

        // 3) pics.csv mapping
        foreach ($this->getPicsCsvFilenames($product->id) as $file) {
            $candidates[] = $this->makePublicPath($file);
        }

        // első létező -> base64 data URI
        foreach ($candidates as $path) {
            $dataUri = $this->fileToDataUri($path);
            if ($dataUri) {
                return $dataUri;
            }
        }

        return null;
    }

    private function findImagesBySlug(string $slug): array
    {
        $exts = ['jpg', 'jpeg', 'png'];
        $variants = [
            $slug,
            str_replace('_m2', '_m_2', $slug),
            str_replace('_m_2', '_m2', $slug),
            str_replace('_', '', $slug),
        ];
        $out = [];
        foreach (array_unique($variants) as $v) {
            foreach ($exts as $ext) {
                $pattern = public_path('images/products/*' . $v . '*.' . $ext);
                $matches = glob($pattern);
                if ($matches) {
                    $out = array_merge($out, $matches);
                }
            }
        }
        return $out;
    }

    private function getPicsCsvFilenames(int $productId): array
    {
        static $map = null;
        if ($map === null) {
            $map = [];
            $csvPath = database_path('csv/pics.csv');
            if (file_exists($csvPath)) {
                foreach (file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    [$pid, $file] = array_pad(explode(';', $line, 2), 2, null);
                    if ($pid && $file) {
                        $map[(int) $pid][] = $file;
                    }
                }
            }
        }
        return $map[$productId] ?? [];
    }

    private function makePublicPath(string $relative): string
    {
        $relative = ltrim($relative, '/\\');
        return public_path('images/products/' . $relative);
    }

    private function fileToDataUri(string $fullPath): ?string
    {
        if (!file_exists($fullPath)) {
            return null;
        }
        $mime = mime_content_type($fullPath) ?: 'image/jpeg';
        $data = base64_encode(file_get_contents($fullPath));
        return "data:{$mime};base64,{$data}";
    }
}
