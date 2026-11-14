<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyNet - Gestión de Clientes y Cuentas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('clientes') }}">SkyNet</a>
            <div class="navbar-nav">
                <a class="nav-link {{ request()->routeIs('clientes') ? 'active' : '' }}" href="{{ route('clientes') }}">Clientes</a>
                <a class="nav-link {{ request()->routeIs('cuentas') ? 'active' : '' }}" href="{{ route('cuentas') }}">Cuentas</a>
            </div>
        </div>
    </nav>

    <main class="container">
        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>