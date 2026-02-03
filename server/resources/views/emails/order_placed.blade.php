<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>DOOMSHOP rendelés visszaigazolás</title>
</head>
<body style="font-family: Arial, sans-serif; background: #0f1115; color: #f1f5f9; padding: 24px;">
    <div style="max-width: 720px; margin: 0 auto; background: #161a1f; border: 1px solid #242b33; border-radius: 12px; overflow: hidden;">
        <div style="padding: 24px; background: linear-gradient(90deg, #111827 0%, #0b1220 60%, #0f172a 100%); border-bottom: 1px solid #1f2937;">
            @if(!empty($logoSrc))
                <div style="text-align: center; margin-bottom: 12px;">
                    <img src="{{ $logoSrc }}" alt="DOOMSHOP" style="max-width: 220px; height: auto; image-rendering: pixelated;">
                </div>
            @endif
            <h1 style="margin: 0; font-size: 24px; letter-spacing: 0.5px;">DOOMSHOP – köszönjük a vásárlást!</h1>
            <p style="margin: 8px 0 0; color: #cbd5e1;">Rendelés azonosító: <strong>{{ $orderCode }}</strong></p>
        </div>

        <div style="padding: 24px;">
            <p style="margin: 0 0 16px;">Kedves {{ $user->name ?? 'Vásárló' }},</p>
            <p style="margin: 0 0 24px;">Megkaptuk a rendelésed. Az alábbi tételeket rögzítettük:</p>

            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; color: #94a3b8;">
                        <th align="left" style="padding: 8px 0;">Termék</th>
                        <th align="left" style="padding: 8px 0;">Kép</th>
                        <th align="left" style="padding: 8px 0;">Mennyiség</th>
                        <th align="left" style="padding: 8px 0;">Egységár</th>
                        <th align="left" style="padding: 8px 0;">Összeg</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    <tr style="border-top: 1px solid #1f2937;">
                        <td style="padding: 12px 0;">{{ $item['name'] }}</td>
                        <td style="padding: 12px 0;">
                            @if (!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 72px; height: 72px; object-fit: cover; border-radius: 8px; border: 1px solid #1f2937;">
                            @else
                                <span style="color: #6b7280;">nincs kép</span>
                            @endif
                        </td>
                        <td style="padding: 12px 0;">{{ $item['quantity'] }} db</td>
                        <td style="padding: 12px 0;">${{ number_format($item['unit_price'], 2) }}</td>
                        <td style="padding: 12px 0; font-weight: bold;">${{ number_format($item['line_total'], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #1f2937;">
                <p style="margin: 0; font-size: 18px; font-weight: bold;">Végösszeg: ${{ number_format($total, 2) }}</p>
                <p style="margin: 8px 0 0; color: #94a3b8;">Minden ár USD-ben kerül feltüntetésre.</p>
            </div>

            <p style="margin: 24px 0 0; color: #94a3b8;">Köszönjük, hogy a DOOMSHOP-ot választottad!</p>
        </div>
    </div>
</body>
</html>
