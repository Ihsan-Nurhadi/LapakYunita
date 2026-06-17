<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>KasirPOS</title>
    <style>
        body{font-family:system-ui,Segoe UI,Roboto,Arial;background:#f8fafb;color:#111;margin:0}
        .container{max-width:1500px;margin:20px auto;padding:16px}
        .grid{display:grid;grid-template-columns:1fr 400px;gap:16px}
        .card{background:#fff;padding:12px;border-radius:10px;border:1px solid rgba(0,0,0,0.06)}
        button{cursor:pointer}
    </style>
    @stack('styles')
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
