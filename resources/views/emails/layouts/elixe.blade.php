<!DOCTYPE html>
<html lang="{{ $locale ?? 'es' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;background:#f1f5f9;color:#0f172a;font-family:Arial,sans-serif">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1f5f9;padding:32px 16px">
        <tr><td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #dbeafe;border-radius:14px;overflow:hidden">
                <tr><td style="background:#082f49;padding:24px 32px;color:#ffffff">
                    <div style="font-size:22px;font-weight:700;color:#67e8f9">ELIXE</div>
                    <div style="margin-top:5px;font-size:13px">Publicidad local en pantallas reales.</div>
                </td></tr>
                <tr><td style="padding:32px;line-height:1.6">
                    <h1 style="margin:0 0 20px;font-size:25px;color:#0f172a">{{ $title }}</h1>
                    {{ $slot }}
                </td></tr>
                <tr><td style="padding:20px 32px;background:#f8fafc;color:#64748b;font-size:12px">Equipo Elixe · Gestión de pantallas digitales y publicidad local en Galicia.</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
