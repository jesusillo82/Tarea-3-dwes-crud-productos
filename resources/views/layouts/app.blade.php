<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>@yield('title', 'Productos') - DWES03</title>
</head>

<body>
    <main class="container py-4">
        <header class="d-flex flex-wrap justify-content-between align-items-center gap-3 bg-dark text-white rounded-3 shadow-sm px-4 py-3 mb-4">
            <a class="d-flex align-items-center gap-3 text-decoration-none text-white" href="{{ route('productos.index') }}">
                <span class="d-inline-flex align-items-center justify-content-center bg-primary rounded-3 fw-bold" style="width: 44px; height: 44px;" aria-hidden="true">GP</span>
                <span>
                    <span class="d-block h4 fw-semibold mb-0">Gestión de productos</span>
                    <span class="small text-white-50">Administración del catálogo</span>
                </span>
            </a>
            @unless (request()->routeIs('productos.show', 'productos.edit', 'productos.create'))
                <a class="btn btn-light text-primary fw-semibold px-3" href="{{ route('productos.create') }}">Crear producto</a>
            @endunless
        </header>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="container border-top py-3 mt-4 text-center text-secondary small">
        &copy; {{ now()->year }} Tarea 3 DWES. Jesús Bernabé Imedio Perea. Todos los derechos reservados.
    </footer>
</body>

</html>
