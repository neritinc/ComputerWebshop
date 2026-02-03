<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('send-mail', function () {
    $fallbackTo = env('MAIL_TEST_TO', 'szu.pet99@gmail.com');
    $cartId = env('MAIL_TEST_CART_ID');

    $cartQuery = Cart::with(['user', 'items.product.pics'])->whereHas('items')->latest('id');
    if ($cartId) {
        $cartQuery->where('id', $cartId);
    }

    $cart = $cartQuery->get()->first(function ($cart) {
        foreach ($cart->items as $item) {
            $product = $item->product;
            if ($product && resolveProductImage($product)) {
                return true;
            }
        }
        return false;
    });

    if (!$cart) {
        $this->error('Nincs olyan kosár, amihez tartoznak tételek és legalább egy kép.');
        return 1;
    }

    $user = $cart->user ?? new User([
        'name' => 'Ismeretlen vásárló',
        'email' => $fallbackTo,
    ]);

    $items = [];
    foreach ($cart->items as $item) {
        $product = $item->product;
        if (!$product) {
            continue;
        }
        $qty = $item->pcs ?? $item->quantity ?? 1;
        $unit = (float) $product->price;
        $img = resolveProductImage($product);
        $items[] = [
            'name' => $product->name,
            'quantity' => $qty,
            'unit_price' => $unit,
            'image' => $img,
            'line_total' => $unit * $qty,
        ];
    }

    if (empty($items)) {
        $this->error('A kiválasztott kosárban nincs érvényes termék tétel.');
        return 1;
    }

    $total = array_sum(array_column($items, 'line_total'));
    $orderCode = 'CART-' . $cart->id . '-' . now()->format('YmdHis');
    $to = $user->email ?? $fallbackTo;

    Mail::to($to)->send(new OrderPlacedMail($user, $items, $total, $orderCode));

    $this->info("Rendelés email kiküldve. Cart #{$cart->id}, címzett: {$to}, tételek: " . count($items) . ", összeg: {$total}");
})->purpose('Send Mail');

function resolveProductImage($product): ?string {
    $candidates = [];

    // pics tábla
    $pic = $product->relationLoaded('pics') ? $product->pics->first() : $product->pics()->first();
    if ($pic && $pic->image_path) {
        if (str_starts_with($pic->image_path, 'http://') || str_starts_with($pic->image_path, 'https://')) {
            return $pic->image_path;
        }
        $candidates[] = makePublicPath($pic->image_path);
    }

    // slug variánsok
    $slug = Str::slug($product->name, '_');
    foreach (findImagesBySlug($slug) as $match) {
        $candidates[] = $match;
    }

    // pics.csv mapping
    foreach (getPicsCsvFilenames($product->id) as $file) {
        $candidates[] = makePublicPath($file);
    }

    foreach ($candidates as $path) {
        $dataUri = fileToDataUri($path);
        if ($dataUri) {
            return $dataUri;
        }
    }
    return null;
}

function findImagesBySlug(string $slug): array {
    $exts = ['jpg', 'jpeg', 'png'];
    $out = [];
    foreach ($exts as $ext) {
        $pattern = public_path('images/products/*' . $slug . '*.' . $ext);
        $matches = glob($pattern);
        if ($matches) {
            $out = array_merge($out, $matches);
        }
    }
    return $out;
}

function getPicsCsvFilenames(int $productId): array {
    static $map = null;
    if ($map === null) {
        $map = [];
        $csvPath = database_path('csv/pics.csv');
        if (file_exists($csvPath)) {
            foreach (file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                [$pid, $file] = array_pad(explode(';', $line, 2), 2, null);
                if ($pid && $file) {
                    $map[(int)$pid][] = $file;
                }
            }
        }
    }
    return $map[$productId] ?? [];
}

function makePublicPath(string $relative): string {
    $relative = ltrim($relative, '/\\');
    return public_path('images/products/' . $relative);
}

function fileToDataUri(string $fullPath): ?string {
    if (!file_exists($fullPath)) {
        return null;
    }
    $mime = mime_content_type($fullPath) ?: 'image/jpeg';
    $data = base64_encode(file_get_contents($fullPath));
    return "data:{$mime};base64,{$data}";
}
