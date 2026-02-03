<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public array $items;
    public float $total;
    public string $orderCode;
    public ?string $logoSrc = null;

    public function __construct(User $user, array $items, float $total, string $orderCode)
    {
        $this->user = $user;
        $this->items = $items;
        $this->total = $total;
        $this->orderCode = $orderCode;

        // Prefer explicit URL from env, else embed local resource as base64 if it exists
        $envUrl = config('mail.logo_url') ?? env('MAIL_LOGO_URL');
        if (!empty($envUrl)) {
            $this->logoSrc = $envUrl;
        } else {
            $logoPath = resource_path('images/doomshop-logo.png');
            if (file_exists($logoPath)) {
                $this->logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }
        }
    }

    public function build()
    {
        return $this->subject('DOOMSHOP rendelés visszaigazolás')
            ->view('emails.order_placed')
            ->with([
                'user' => $this->user,
                'items' => $this->items,
                'total' => $this->total,
                'orderCode' => $this->orderCode,
                'logoSrc' => $this->logoSrc,
            ]);
    }
}
