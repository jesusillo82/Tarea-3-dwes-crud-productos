<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>@yield('title', 'Productos') - DWES03</title>
</head>

<body class="bg-info-subtle">
    <main class="container py-4">
        <header class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <a class="h3 text-decoration-none text-dark mb-0" href="{{ route('productos.index') }}">Gestión de productos</a>
            <a class="btn btn-success" href="{{ route('productos.create') }}">Crear producto</a>
        </header>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>

</html>
