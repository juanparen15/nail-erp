<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $headerSubtitle ?? 'Kate Nails' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f4f4f5; color: #18181b; }
        .wrapper { max-width: 560px; margin: 32px auto; padding: 0 16px; }
        .card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.12); }
        .header { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); padding: 32px 32px 28px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px; }
        .header p { color: rgba(255,255,255,.9); font-size: 14px; margin-top: 6px; }
        .body { padding: 28px 32px; }
        .greeting { color: #18181b; font-size: 17px; font-weight: 700; margin-bottom: 12px; }
        .body p { color: #52525b; font-size: 15px; line-height: 1.6; margin-bottom: 20px; }
        .detail-card { background: #fdf4ff; border: 1px solid #f0abfc; border-radius: 8px; padding: 20px; margin: 0 0 24px; }
        .detail-card .badge { display: inline-block; color: #fff; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; padding: 4px 12px; border-radius: 999px; margin-bottom: 14px; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0abfc; font-size: 14px; }
        .detail-row:last-child { border-bottom: none; padding-bottom: 0; }
        .detail-row .label { color: #71717a; font-weight: 500; }
        .detail-row .value { color: #18181b; font-weight: 600; text-align: right; }
        .btn { display: block; text-align: center; background: #ec4899; color: #ffffff !important; text-decoration: none; font-size: 14px; font-weight: 600; padding: 13px 28px; border-radius: 8px; margin: 0 0 24px; }
        .btn:hover { background: #db2777; }
        .closing { color: #52525b; font-size: 15px; line-height: 1.6; margin-bottom: 0; }
        .footer { background: #f9fafb; border-top: 1px solid #e4e4e7; padding: 20px 32px; text-align: center; }
        .footer p { color: #a1a1aa; font-size: 12px; line-height: 1.6; }
        @media (max-width: 600px) {
            .header, .body { padding: 24px 20px; }
            .detail-row { flex-direction: column; gap: 2px; }
            .detail-row .value { text-align: left; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">

        {{-- Header --}}
        <div class="header">
            <h1>💅 Kate Nails</h1>
            <p>{{ $headerSubtitle ?? '' }}</p>
        </div>

        {{-- Body --}}
        <div class="body">
            @if(!empty($greeting))
                <p class="greeting">{{ $greeting }}</p>
            @endif

            @if(!empty($intro))
                <p>{{ $intro }}</p>
            @endif

            <div class="detail-card">
                <span class="badge" style="background: {{ $badgeColor ?? '#ec4899' }}">{{ $badge ?? 'Cita' }}</span>
                <div class="detail-row">
                    <span class="label">Servicio</span>
                    <span class="value">{{ $appointment->service->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Fecha</span>
                    <span class="value">{{ $date }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Hora</span>
                    <span class="value">{{ $time }} hrs</span>
                </div>
                <div class="detail-row">
                    <span class="label">Total</span>
                    <span class="value">${{ number_format($appointment->total_price, 0, ',', '.') }} COP</span>
                </div>
            </div>

            @if(!empty($buttonUrl))
                <a href="{{ $buttonUrl }}" class="btn">{{ $buttonText ?? 'Ver más' }}</a>
            @endif

            @if(!empty($closing))
                <p class="closing">{{ $closing }}</p>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Este mensaje fue enviado automáticamente por Kate Nails.<br>No respondas a este correo.</p>
        </div>

    </div>
</div>
</body>
</html>
