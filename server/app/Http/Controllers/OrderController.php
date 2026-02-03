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
                    abort(422, 'A kosárban lévõ egyik termék törlésre került.');
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
        // 1) pics tábla
        $pic = $product->relationLoaded('pics')
            ? $product->pics->first()
            : $product->pics()->first();

        if ($pic && $pic->image_path) {
            $dataUri = $this->localImageToDataUri($pic->image_path);
            if ($dataUri) {
                return $dataUri;
            }
            if (str_starts_with($pic->image_path, 'http://') || str_starts_with($pic->image_path, 'https://')) {
                return $pic->image_path;
            }
        }

        // 2) slug alapú keresés a public/images/products-ben
        $slug = Str::slug($product->name, '_');
        $pattern = public_path('images/products/' . $slug . '*.{jpg,jpeg,png}');
        $matches = glob($pattern, GLOB_BRACE);
        if ($matches && count($matches) > 0) {
            $dataUri = $this->fileToDataUri($matches[0]);
            if ($dataUri) {
                return $dataUri;
            }
        }

        // 3) pics.csv mapping (product_id;filename)
        static $picsMap = null;
        if ($picsMap === null) {
            $picsMap = [];
            $csvPath = database_path('csv/pics.csv');
            if (file_exists($csvPath)) {
                foreach (file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    [$pid, $file] = array_pad(explode(';', $line, 2), 2, null);
                    if ($pid && $file) {
                        $picsMap[(int)$pid][] = $file;
                    }
                }
            }
        }
        $pid = (int) $product->id;
        if (!empty($picsMap[$pid])) {
            foreach ($picsMap[$pid] as $file) {
                $full = public_path('images/products/' . ltrim($file, '/'));
                $dataUri = $this->fileToDataUri($full);
                if ($dataUri) {
                    return $dataUri;
                }
            }
        }

        return null;
    }

    private function localImageToDataUri(string $path): ?string
    {
        $full = $path;
        if (!str_starts_with($full, '/')) {
            $full = public_path('images/products/' . ltrim($path, '/'));
        }
        return $this->fileToDataUri($full);
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
