# Guía de funcionalidad del proyecto

Esta guía describe el código y la configuración propios del CRUD de productos. Abarca los archivos versionados del repositorio, incluidos los archivos de soporte de Git y los marcadores de directorio. No intenta documentar el contenido interno de `vendor/`, que son dependencias externas, ni archivos locales generados que no están versionados, como `.env`, `database/database.sqlite`, `public/build/` o las cachés.

## 1. Qué hace la aplicación

Es una aplicación Laravel 12 para consultar y mantener el catálogo de productos. Cada producto guarda nombre, nombre corto, descripción, precio y código de familia. El catálogo de familias se consulta en la tabla `familias`; su código `cod` coincide con el campo `productos.familia`.

Las operaciones disponibles son:

| Operación | Ruta REST | Método HTTP | Nombre de ruta | Resultado |
|---|---|---|---|---|
| Listar | `/productos` | GET | `productos.index` | Lista paginada de 10 productos, ordenada por `id`, con familia cargada. |
| Formulario de alta | `/productos/create` | GET | `productos.create` | Formulario y familias ordenadas por nombre. |
| Guardar alta | `/productos` | POST | `productos.store` | Valida, crea el producto y redirige al detalle. |
| Ver detalle | `/productos/{producto}` | GET | `productos.show` | Muestra los datos y la familia. |
| Formulario de edición | `/productos/{producto}/edit` | GET | `productos.edit` | Formulario precargado y familias disponibles. |
| Guardar edición | `/productos/{producto}` | PUT/PATCH | `productos.update` | Valida, actualiza y redirige al detalle. |
| Eliminar | `/productos/{producto}` | DELETE | `productos.destroy` | Elimina y redirige al listado. |

La ruta `/` redirige al listado. La protección CSRF de formularios y el enlace del modelo por identificador los proporciona Laravel.

## 2. Flujo de datos y validación

1. `routes/web.php` enlaza las URL del recurso con `ProductoController`.
2. Laravel obtiene el producto de la URL mediante el *route model binding* cuando una acción recibe `Producto $producto`.
3. El controlador obtiene o modifica registros mediante los modelos Eloquent.
4. `validatedData()` es la única definición de las reglas para alta y edición: nombre obligatorio de hasta 200 caracteres; nombre corto obligatorio de hasta 50; descripción opcional; precio numérico y no negativo; familia obligatoria y existente en `familias.cod`.
5. Las plantillas Blade presentan la respuesta, los errores de validación y los mensajes de sesión. Los valores dinámicos se imprimen con el escape HTML de Blade.

El esquema de `productos` y `familias` pertenece al proyecto de datos original. Las migraciones incluidas son las predeterminadas de Laravel para usuarios, caché y colas; **no crean las tablas del catálogo**. La aplicación requiere que las tablas del catálogo ya existan.

## 3. Archivos de la aplicación y símbolos

### Controlador

#### `app/Http/Controllers/ProductoController.php`

Orquesta el CRUD y traduce solicitudes HTTP a operaciones Eloquent y respuestas Blade o redirecciones.

| Símbolo | Función |
|---|---|
| `index(): View` | Carga productos con `familiaRelacion`, los ordena por `id`, pagina de diez en diez y pasa `$productos` a `productos.index`. La carga anticipada evita una consulta por cada familia listada. |
| `create(): View` | Obtiene `$familias` ordenadas por nombre y las pasa a `productos.create`. |
| `store(Request $request): RedirectResponse` | Valida los datos, crea `$producto` y redirige a su ruta `show` con un mensaje `status`. |
| `show(Producto $producto): View` | Carga la relación de familia del producto y lo pasa a `productos.show`. |
| `edit(Producto $producto): View` | Obtiene las familias y pasa `$producto` y `$familias` a `productos.edit`. |
| `update(Request $request, Producto $producto): RedirectResponse` | Valida y aplica los campos permitidos; redirige al detalle con el mensaje correspondiente. |
| `destroy(Producto $producto): RedirectResponse` | Elimina el producto; redirige al listado con el mensaje correspondiente. |
| `validatedData(Request $request): array` | Método privado común a alta y edición. Devuelve únicamente los campos que superan las reglas de validación y asigna mensajes de error en español. |

Variables locales relevantes: `$productos` es la página de resultados; `$familias`, la colección para los selectores; `$producto`, el modelo recién creado o resuelto desde la URL; `$request`, la solicitud y sus datos; el arreglo devuelto por `validatedData()` contiene solo `nombre`, `nombre_corto`, `descripcion`, `pvp` y `familia`.

#### `app/Http/Controllers/Controller.php`

Clase base abstracta de controladores de la aplicación. En este proyecto no añade métodos ni propiedades; permite que `ProductoController` siga la estructura convencional de Laravel.

### Modelos

#### `app/Models/Producto.php`

Representa la tabla `productos`.

| Propiedad o método | Función |
|---|---|
| `$table` | Fija el nombre explícito de la tabla: `productos`. |
| `$timestamps` | Desactiva `created_at` y `updated_at`, ausentes en el esquema de la tarea. |
| `$fillable` | Lista los campos asignables de forma masiva: `nombre`, `nombre_corto`, `descripcion`, `pvp` y `familia`. |
| `casts(): array` | Convierte `pvp` a decimal con dos posiciones. |
| `familiaRelacion(): BelongsTo` | Relación muchos-a-uno: enlaza la clave foránea `familia` con la clave `cod` de `Familia`. |

Los demás atributos (`id`, `nombre`, etc.) son atributos Eloquent de la fila y no propiedades PHP declaradas en la clase.

#### `app/Models/Familia.php`

Representa la tabla `familias`.

| Propiedad o método | Función |
|---|---|
| `$table` | Fija la tabla `familias`. |
| `$primaryKey` | Usa `cod` como clave primaria, en vez de `id`. |
| `$incrementing` | Indica que el código no es autoincremental. |
| `$keyType` | Declara el tipo de la clave como `string`. |
| `$timestamps` | Desactiva marcas temporales que no existen en la tabla. |
| `$fillable` | Permite asignar masivamente `cod` y `nombre`. |
| `productos(): HasMany` | Devuelve los productos enlazados por `productos.familia = familias.cod`. |

#### `app/Models/User.php`

Modelo de autenticación estándar de Laravel, con la tabla `users`. Aunque el CRUD no implementa inicio de sesión, el modelo lo usan la configuración de autenticación, las migraciones, la factoría y el seeder.

| Símbolo | Función |
|---|---|
| `HasFactory` y `Notifiable` | Habilitan la creación de usuarios de prueba y las notificaciones. |
| `$fillable` | Permite asignar `name`, `email` y `password`. |
| `$hidden` | Evita exponer `password` y `remember_token` en serializaciones. |
| `casts(): array` | Convierte `email_verified_at` en fecha y cifra la contraseña con el cast `hashed`. |

### Vistas Blade

Las vistas de `resources/views/productos/` extienden el diseño `layouts.app`. `@csrf` protege formularios; `@method` emula métodos HTTP que HTML no envía directamente; `@error` muestra errores de validación; `old()` recupera la entrada anterior.

| Archivo | Funcionalidad y datos usados |
|---|---|
| `resources/views/layouts/app.blade.php` | Plantilla HTML en español, incluye Bootstrap 5.3.3 desde CDN, encabezado, navegación, mensaje de sesión `status`, sección `content` y pie con el año actual. Oculta el acceso de alta en vistas de detalle, edición y creación. |
| `resources/views/productos/index.blade.php` | Recorre `$productos`, muestra `id`, `nombre`, `familiaRelacion.nombre` y `pvp`; ofrece detalle, edición y eliminación. `@forelse` presenta el estado vacío y `links()` renderiza la paginación Bootstrap 5. |
| `resources/views/productos/create.blade.php` | Presenta el formulario de alta. Usa `$familias` para avisar cuando no hay opciones y carga el formulario compartido con `$producto = null` y `$submitLabel = 'Crear'`. |
| `resources/views/productos/edit.blade.php` | Presenta el formulario de edición del `$producto`, agrega el método `PUT` y carga el formulario compartido con la etiqueta «Guardar cambios». |
| `resources/views/productos/_form.blade.php` | Parcial común de alta y edición. Recibe `$producto`, `$familias` y `$submitLabel`; contiene los controles `nombre`, `nombre_corto`, `pvp`, `familia` y `descripcion`. En edición obtiene los valores del modelo y en ambos casos conserva la entrada anterior. |
| `resources/views/productos/show.blade.php` | Presenta código, nombres, familia, precio y descripción del `$producto`. Divide la descripción en líneas; si una línea contiene `:`, pone en negrita el texto anterior a los dos puntos. `$linea` es cada línea y `$partes` el resultado limitado de dividirla. |
| `resources/views/welcome.blade.php` | Página de bienvenida predeterminada de Laravel, independiente de las rutas del CRUD. Usa `config('app.name')`, `app()->getLocale()` y `@vite` cuando existe el manifiesto o el marcador de desarrollo. |

En las vistas, `$familia` es cada opción del bucle de familias; `$message` es el texto del error actual; `old(...)` es el valor enviado previamente; `session('status')` es la confirmación de una operación. Los valores de descripción se tratan como texto y no como HTML confiable.

### Rutas y arranque

| Archivo | Responsabilidad |
|---|---|
| `routes/web.php` | Declara la redirección de `/` a `/productos` y las siete rutas de recurso generadas por `Route::resource`, todas asociadas a `ProductoController`. |
| `routes/console.php` | Define el comando Artisan `inspire`, que imprime una cita de Laravel. `$this` es el comando Artisan dentro de la función de cierre. |
| `artisan` | Punto de entrada de la consola: carga Composer, arranca la aplicación, entrega los argumentos a Artisan y termina con el código de salida devuelto. `$app` es la instancia Laravel y `$status` su resultado. |
| `bootstrap/app.php` | Construye la aplicación, registra los archivos de rutas web y consola y expone el chequeo de salud en `/up`. Los cierres de middleware y excepciones reciben `$middleware` y `$exceptions`; están disponibles para configuración adicional y vacíos actualmente. |
| `bootstrap/providers.php` | Lista los proveedores de servicios cargados por la aplicación; incluye `AppServiceProvider`. |
| `app/Providers/AppServiceProvider.php` | Punto de extensión para registrar servicios (`register`) y configurar la aplicación al arrancar (`boot`); ambos métodos están vacíos actualmente. |
| `public/index.php` | Punto de entrada HTTP. Define la hora de inicio, carga el autoloader y el arranque, captura la petición y la entrega a Laravel. `$maintenance` es el archivo opcional que informa del modo de mantenimiento; `$app` es la aplicación y `Request::capture()` obtiene la petición actual. |
| `public/.htaccess` | Configura Apache para no listar directorios, preservar cabeceras de autorización y XSRF, normalizar barras finales y dirigir las URL no correspondientes a archivos al front controller. |
| `public/robots.txt` | Permite el rastreo a todos los agentes (`Disallow:` vacío). |
| `public/favicon.ico` | Icono del sitio servido estáticamente por el servidor web; no contiene código ejecutable. |

## 4. Datos y persistencia

### `database/datosProyecto.sql`

Script de carga del conjunto de datos original. Selecciona la base `proyecto` e inserta el catálogo de familias y los productos de ejemplo. Presupone que las tablas `familias` y `productos` ya existen con el orden de columnas esperado. No crea el esquema ni forma parte de una migración Laravel.

Campos utilizados por el CRUD:

| Tabla | Campo | Papel |
|---|---|---|
| `familias` | `cod` | Código de texto y clave primaria de familia. |
| `familias` | `nombre` | Etiqueta legible en los selectores y el catálogo. |
| `productos` | `id` | Identificador del producto. |
| `productos` | `nombre` | Nombre visible, obligatorio, máximo 200 caracteres. |
| `productos` | `nombre_corto` | Nombre breve, obligatorio, máximo 50 caracteres. |
| `productos` | `descripcion` | Texto opcional; puede contener saltos de línea y pares `etiqueta: valor`. |
| `productos` | `pvp` | Precio decimal mostrado en euros; se valida como numérico y no negativo. |
| `productos` | `familia` | Código que referencia `familias.cod`; debe existir antes de guardar. |

### Migraciones incluidas

Todas devuelven una migración anónima con `up()` para crear tablas y `down()` para eliminarlas. El método `Schema::create` recibe un `$table` (`Blueprint`) que define columnas e índices.

| Archivo | Tablas y columnas relevantes |
|---|---|
| `database/migrations/0001_01_01_000000_create_users_table.php` | `users` (nombre, correo único, verificación, contraseña, token y marcas temporales), `password_reset_tokens` (correo, token y creación) y `sessions` (sesión, usuario opcional, IP, agente, carga y actividad). |
| `database/migrations/0001_01_01_000001_create_cache_table.php` | `cache` (clave, valor y expiración) y `cache_locks` (clave, propietario y expiración). |
| `database/migrations/0001_01_01_000002_create_jobs_table.php` | `jobs` (cola, carga, intentos y tiempos), `job_batches` (estado de lotes) y `failed_jobs` (trabajos fallidos, excepción y fecha). |

### Factoría y seeder

| Archivo / símbolo | Funcionalidad |
|---|---|
| `database/factories/UserFactory.php` | Genera usuarios ficticios con Faker. `$password` es un hash compartido y calculado una sola vez; `definition()` devuelve nombre, correo único seguro, fecha de verificación, contraseña y token aleatorio; `unverified()` devuelve un estado con `email_verified_at` nulo. `$attributes` representa el estado actual al aplicar ese cambio. |
| `database/seeders/DatabaseSeeder.php` | `run()` crea un usuario fijo de ejemplo (`Test User`, `test@example.com`) usando la factoría. El ejemplo de diez usuarios está comentado. No crea familias ni productos. `WithoutModelEvents` evita disparar eventos de modelos durante el sembrado. |

## 5. Configuración de Laravel

Los archivos de `config/` devuelven arreglos de opciones consultados por el framework mediante `config()`. Las llamadas `env()` conectan esas opciones con variables del entorno. Las configuraciones cubren tanto valores usados ahora como alternativas estándar disponibles para Laravel.

| Archivo | Opciones que define |
|---|---|
| `config/app.php` | `name`, `env`, `debug`, `url`, `timezone`, idiomas `locale`/`fallback_locale`/`faker_locale`, cifrado `cipher`/`key`/`previous_keys` y modo de mantenimiento `maintenance.driver`/`store`. |
| `config/auth.php` | Predeterminados `guard` y `passwords`; guardas de sesión y sus proveedores; modelo `User`; tabla, proveedor, caducidad y límite de emisión de tokens de restablecimiento; tiempo de confirmación de contraseña. |
| `config/cache.php` | Almacén `default`; opciones de los almacenes `array`, `database`, `file`, `memcached`, `redis`, `dynamodb`, `octane` y `failover`; `prefix` evita colisiones de claves. |
| `config/database.php` | Conexión `default`; conexiones SQLite, MySQL, MariaDB, PostgreSQL y SQL Server con URL, host, puerto, credenciales, juego de caracteres y opciones; tabla de control de migraciones; cliente, prefijo y conexiones Redis. |
| `config/filesystems.php` | Disco `default`; discos locales `local`/`public` y remoto `s3`; raíces, URL, visibilidad y manejo de errores; enlace simbólico `public/storage` hacia `storage/app/public`. |
| `config/logging.php` | Canal `default`, canal y traza para deprecaciones; canales `stack`, `single`, `daily`, `slack`, `papertrail`, `stderr`, `syslog`, `errorlog`, `null` y `emergency`, sus handlers, niveles y destinos. |
| `config/mail.php` | Mailer `default`; mailers SMTP, SES, Postmark, Resend, sendmail, log, array, failover y round-robin; dirección y nombre globales de remitente (`from`). |
| `config/queue.php` | Conexión `default`; controladores `sync`, `database`, Beanstalkd, SQS, Redis, diferido, segundo plano y failover; opciones de reintento; almacenamiento de lotes (`batching`) y trabajos fallidos (`failed`). |
| `config/services.php` | Credenciales de servicios externos: Postmark, Resend, SES/AWS y notificaciones Slack. Los secretos se leen del entorno, no se escriben aquí. |
| `config/session.php` | Controlador, duración y cierre de sesión; cifrado; rutas/tabla/conexión/almacén; limpieza probabilística; nombre, ruta, dominio y seguridad de cookie; política `same_site` y partición. |

El detalle de las claves específicas y valores predeterminados se conserva en cada archivo de configuración junto a sus secciones. En el entorno de ejemplo la base predeterminada es MySQL (`proyecto`), la sesión usa archivos, la cola es síncrona y la caché usa archivos. En pruebas, `phpunit.xml` cambia la base a SQLite en memoria y varios servicios a controladores aislados.

## 6. Variables de entorno

`.env.example` es una plantilla, no un archivo con credenciales válidas. Debe copiarse a `.env`, que está excluido de Git.

| Grupo | Variables | Significado |
|---|---|---|
| Aplicación | `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL` | Nombre, entorno, clave de cifrado, detalle de errores y URL base. `APP_KEY` se genera con Artisan. |
| Idioma y seguridad | `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE`, `APP_MAINTENANCE_DRIVER`, `BCRYPT_ROUNDS` | Idiomas de aplicación y Faker, controlador de mantenimiento y coste de hash de contraseñas. |
| Registro | `LOG_CHANNEL`, `LOG_STACK`, `LOG_DEPRECATIONS_CHANNEL`, `LOG_LEVEL` | Canal, composición de la pila, destino de avisos deprecados y nivel mínimo. |
| Base de datos | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Motor y parámetros de conexión; el ejemplo apunta a MySQL y a la base `proyecto`. |
| Sesión y servicios Laravel | `SESSION_DRIVER`, `SESSION_LIFETIME`, `SESSION_ENCRYPT`, `SESSION_PATH`, `SESSION_DOMAIN`, `BROADCAST_CONNECTION`, `FILESYSTEM_DISK`, `QUEUE_CONNECTION`, `CACHE_STORE` | Persistencia de sesiones, vida útil y cookies, transporte de eventos, disco, colas y caché. |
| Cachés y Redis | `MEMCACHED_HOST`, `REDIS_CLIENT`, `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT` | Conexión opcional a Memcached o Redis. |
| Correo | `MAIL_MAILER`, `MAIL_SCHEME`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | Transporte, servidor SMTP y remitente. El ejemplo registra el correo en logs. |
| AWS y frontend | `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_USE_PATH_STYLE_ENDPOINT`, `VITE_APP_NAME` | Acceso opcional a AWS/S3 y valor de nombre disponible para Vite. |

Las variables opcionales comentadas en el archivo muestran configuraciones alternativas (por ejemplo, almacenamiento de mantenimiento o prefijo de caché); no están activas por sí solas.

## 7. Frontend y compilación

| Archivo | Funcionalidad |
|---|---|
| `resources/css/app.css` | Entrada de Tailwind CSS 4. Importa Tailwind, registra fuentes Blade/JavaScript a escanear y declara la familia tipográfica `--font-sans`. |
| `resources/js/app.js` | Entrada JavaScript; importa `bootstrap.js`. |
| `resources/js/bootstrap.js` | Configura Axios como `window.axios` y añade la cabecera `X-Requested-With: XMLHttpRequest`. |
| `vite.config.js` | Registra el plugin Laravel con las entradas CSS y JS, recarga vistas modificadas, habilita Tailwind y excluye las vistas compiladas de almacenamiento del watcher. |
| `package.json` | Declara módulos ES, los comandos `npm run dev` y `npm run build`, y dependencias de desarrollo Vite, Tailwind, plugin Laravel, Axios y ejecución concurrente. |
| `composer.json` | Declara versión de PHP/Laravel, dependencias PHP, autoload PSR-4 y configuración de plugins. `setup` instala dependencias, prepara `.env`, crea la clave, migra, instala npm y compila; `dev` inicia servidor, cola, visor de logs y Vite en paralelo; `test` limpia configuración y ejecuta pruebas. Los hooks realizan descubrimiento de paquetes y tareas estándar al instalar o actualizar. |
| `composer.lock` | Fija las versiones concretas de Composer y sus dependencias transitivas para instalaciones repetibles. Es generado por Composer; no se edita manualmente. |

El diseño principal del CRUD usa Bootstrap desde CDN en `layouts/app.blade.php`; no carga el CSS/JS de Vite en ese layout. La página de bienvenida sí puede usar las entradas de Vite. El build configura los recursos para la aplicación y para esa vista.

## 8. Pruebas

| Archivo | Responsabilidad |
|---|---|
| `phpunit.xml` | Configura PHPUnit, el autoloader, suites `Unit`/`Feature`, cobertura de `app` y variables aisladas: `APP_ENV`, `APP_KEY`, `APP_MAINTENANCE_DRIVER`, `BCRYPT_ROUNDS`, `BROADCAST_CONNECTION`, `CACHE_STORE`, `DB_CONNECTION`, `DB_DATABASE`, `DB_URL`, `MAIL_MAILER`, `QUEUE_CONNECTION`, `SESSION_DRIVER`, `PULSE_ENABLED`, `TELESCOPE_ENABLED` y `NIGHTWATCH_ENABLED`. La base usa SQLite en memoria; caché y sesión, memoria; cola, modo síncrono. |
| `tests/TestCase.php` | Clase base abstracta de pruebas de aplicación; extiende el caso de Laravel. |
| `tests/Feature/ProductoCrudTest.php` | Crea el esquema mínimo de catálogo por prueba y una familia. `setUp()` prepara los datos; `test_product_list_is_displayed()` verifica el listado y la familia; `test_product_description_displays_characteristic_names_in_bold()` verifica el formato de la descripción; `test_edit_product_shows_description_in_styled_editable_field_without_preview()` revisa la edición; `test_create_product_button_is_hidden_on_product_detail()` y `test_create_product_button_is_hidden_on_create_page()` comprueban la visibilidad de botones; `test_product_can_be_created_updated_and_deleted()` recorre el CRUD; `test_invalid_product_data_is_rejected()` comprueba validación; `createProduct()` inserta el producto auxiliar. |
| `tests/Feature/ExampleTest.php` | Verifica que la página raíz redirige a `productos.index`. |
| `tests/Unit/ExampleTest.php` | Prueba unitaria mínima que comprueba la aserción básica de PHPUnit. |

Para ejecutar: `php artisan test` o `composer test`.

## 9. Archivos auxiliares versionados

| Archivo | Funcionalidad |
|---|---|
| `README.md` | Resumen del CRUD, requisitos, instalación, conexión a la base de datos, estructura principal, ejecución del servidor y pruebas; enlaza esta guía para el detalle por archivo. |
| `docs/funcionalidad.md` | Esta referencia funcional: inventario del código, rutas, modelos, vistas, datos, configuración, entorno y pruebas. |
| `.editorconfig` | Convenciones de edición: UTF-8, finales LF, espacios, salto final y excepciones de tamaño de sangría por tipo de archivo. |
| `.gitattributes` | Normaliza archivos de texto a LF y selecciona reglas de diff por tipo; excluye metadatos indicados de exportaciones de archivo. |
| `.gitignore` | Excluye entorno local, dependencias, cachés, compilados, logs, archivos de IDE y artefactos generados. |
| `bootstrap/cache/.gitignore` | Mantiene versionado el directorio, pero ignora su contenido generado salvo el propio marcador. |
| `database/.gitignore` | Evita versionar archivos SQLite locales. |
| `storage/app/.gitignore` | Ignora archivos de almacenamiento salvo los directorios `private/`, `public/` y el marcador. |
| `storage/app/private/.gitignore` y `storage/app/public/.gitignore` | Evitan versionar contenido cargado o generado en los discos privado y público. |
| `storage/framework/.gitignore` y los marcadores de `cache/`, `cache/data/`, `sessions/`, `testing/` y `views/` | Conservan los directorios requeridos por Laravel y excluyen sesiones, cachés, pruebas y vistas compiladas. |
| `storage/logs/.gitignore` | Conserva el directorio de logs sin incluir los archivos de registro. |

## 10. Archivos generados o externos fuera del alcance

- `vendor/`: paquetes PHP instalados por Composer; su descripción de funcionalidad corresponde a cada proyecto externo.
- `node_modules/`: paquetes JavaScript instalados por npm.
- `.env`, `database/database.sqlite`, archivos de `storage/` y resultados de pruebas: datos locales, sensibles o temporales; no deben incluirse en la documentación ni en Git.
- `public/build/` y `public/hot`: salida de compilación o marcador del servidor Vite.
- `composer.lock`: aunque se documenta como archivo versionado, sus dependencias se administran con Composer, no editando sus entradas.
