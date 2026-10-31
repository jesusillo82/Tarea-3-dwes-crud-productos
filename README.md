# CRUD de productos DWES03 con Laravel. Modificacion de la Tarea 3 de la 
Asignatura Desarrollo web en entorno Servidor DAW a la que incormporamos 
framework Laravel

Aplicación Laravel 12 para listar, consultar, crear, editar y eliminar productos de la base de datos `proyecto`.

## Requisitos

- PHP 8.2 o posterior con las extensiones habituales de Laravel y `pdo_mysql`.
- Composer.
- MySQL o MariaDB con la base de datos `proyecto` y sus tablas `productos` y `familias`.

## Instalación

1. Instala las dependencias: `composer install`.
2. Copia `.env.example` a `.env`.
3. Comprueba en `.env` los datos de conexión `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.
4. Genera la clave de la aplicación: `php artisan key:generate`.
5. Inicia el servidor: `php artisan serve`. Es un pequeño servidor que tiene Laravel para poder ejecutarlo sin Laragon Apache. Hay que ejecutarlo en la carpeta del proyecto
6. Abre la URL que indique Artisan, normalmente `http://127.0.0.1:8000`. http://localhost:8000/productos 

Si alojamos en Laragon el proyecto este debe alojarse en la 
carpeta laragon/www y poner en el navegador 

La aplicación reutiliza el esquema de la tarea original: `productos` debe contener `id`, `nombre`, `nombre_corto`, `descripcion`, `pvp` y `familia`; `familias` debe contener `cod` y `nombre`. No ejecutes migraciones para esas tablas en una base que ya las tenga.

## Estructura principal

- `app/Http/Controllers`: lógica de las rutas del CRUD.
- `app/Models`: modelos Eloquent `Producto` y `Familia`.
- `resources/views`: plantillas Blade.
- `routes/web.php`: rutas web con protección CSRF.
- `tests/Feature`: pruebas funcionales del CRUD.

## Documentación del código

Consulta la [guía de funcionalidad del proyecto](docs/funcionalidad.md) para ver el propósito de los archivos, las rutas y vistas, los métodos y variables principales, el esquema de datos y la configuración.

## Pruebas

Ejecuta `php artisan test`. Las pruebas crean un esquema SQLite temporal y no requieren conectarse a MySQL.
