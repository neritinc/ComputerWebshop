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
    // Teszt cím a .env-ből, ha a kosár userének nincs emailje
    $fallbackTo = env('MAIL_TEST_TO', 'szu.pet99@gmail.com');

    // Opcionális: konkrét kosár ID a .env-ben
    $cartId = env('MAIL_TEST_CART_ID');

    $cartQuery = Cart::with(['user', 'items.product.pics'])->whereHas('items')->latest('id');

    if ($cartId) {
        $cartQuery->where('id', $cartId);
    }

    // Ha nincs fix ID, keressük a legutóbbi olyan kosarat, ahol legalább egy tételhez sikerül képet rendelni
    $cart = $cartQuery->get()->first(function ($cart) {
        foreach ($cart->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }
            if (resolveProductImage($product)) {
                return true;
            }
        }
        return false;
    });

    if (!$cart) {
        $this->error('Nincs olyan kosár, amihez tartoznak tételek és legalább egy kép. Töltsd fel seederrel vagy add meg a MAIL_TEST_CART_ID-t.');
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
    $pic = $product->relationLoaded('pics')
        ? $product->pics->first()
        : $product->pics()->first();

    if ($pic && $pic->image_path) {
        if (str_starts_with($pic->image_path, 'http://') || str_starts_with($pic->image_path, 'https://')) {
            return $pic->image_path;
        }
        $path = $pic->image_path;
        if (!str_starts_with($path, '/')) {
            $path = 'images/products/' . ltrim($path, '/');
        }
        return rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
    }

    $slug = Str::slug($product->name, '_');
    $pattern = public_path('images/products/' . $slug . '*.{jpg,jpeg,png}');
    $matches = glob($pattern, GLOB_BRACE);
    if ($matches && count($matches) > 0) {
        $relative = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $matches[0]);
        return rtrim(config('app.url'), '/') . '/' . str_replace('\\', '/', $relative);
    }

    // Fallback: database/csv/pics.csv mapping (product_id;filename)
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
            if (file_exists($full)) {
                $relative = 'images/products/' . ltrim($file, '/');
                return rtrim(config('app.url'), '/') . '/' . $relative;
            }
        }
    }
    return null;
}
