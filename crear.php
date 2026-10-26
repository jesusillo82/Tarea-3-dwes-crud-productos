<?php
require_once __DIR__ . "/conexion.php";

$escape = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
};

$values = [
    "nombre" => "",
    "nombre_corto" => "",
    "pvp" => "",
    "familia" => "",
    "descripcion" => "",
];
$errors = [];
$familias = [];
$familiasCargadas = false;

try {
    $familias = $conProyecto
        ->query("SELECT cod, nombre FROM familias ORDER BY nombre")
        ->fetchAll(PDO::FETCH_ASSOC);
    $familiasCargadas = true;
} catch (PDOException $ex) {
    error_log("Error al consultar las familias en crear.php: " . $ex->getMessage());
    $errors[] = "No se han podido cargar las familias. Inténtalo de nuevo más tarde.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($values as $field => $value) {
        if (isset($_POST[$field]) && is_string($_POST[$field])) {
            $values[$field] = trim($_POST[$field]);
        }
    }

    if ($values["nombre"] === "") {
        $errors[] = "El nombre es obligatorio.";
    }

    if ($values["nombre_corto"] === "") {
        $errors[] = "El nombre corto es obligatorio.";
    }

    if (
        $values["pvp"] === ""
        || !is_numeric($values["pvp"])
        || !is_finite((float) $values["pvp"])
        || (float) $values["pvp"] < 0
    ) {
        $errors[] = "Introduce un precio válido, igual o superior a 0.";
    }

    if ($familiasCargadas) {
        if ($familias === []) {
            $errors[] = "No hay familias disponibles para asignar al producto.";
        } else {
            $familiaValida = false;
            foreach ($familias as $familia) {
                if ((string) $familia["cod"] === $values["familia"]) {
                    $familiaValida = true;
                    break;
                }
            }
            if (!$familiaValida) {
                $errors[] = "Selecciona una familia válida.";
            }
        }
    }

    if ($familiasCargadas && $familias !== [] && $errors === []) {
        try {
            $consulta = $conProyecto->prepare(
                "INSERT INTO productos (nombre, nombre_corto, descripcion, pvp, familia)
                 VALUES (:nombre, :nombre_corto, :descripcion, :pvp, :familia)"
            );
            $consulta->execute([
                ":nombre" => $values["nombre"],
                ":nombre_corto" => $values["nombre_corto"],
                ":descripcion" => $values["descripcion"],
                ":pvp" => $values["pvp"],
                ":familia" => $values["familia"],
            ]);

            header("Location: listado.php", true, 303);
            exit;
        } catch (PDOException $ex) {
            error_log("Error al crear un producto en crear.php: " . $ex->getMessage());
            $errors[] = "No se ha podido crear el producto. Comprueba los datos e inténtalo de nuevo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Crear producto</title>
</head>

<body class="bg-info">
    <main class="container py-3">
        <h1 class="text-center mb-4">Crear producto</h1>

        <?php if ($errors !== []): ?>
            <div class="alert alert-danger" role="alert">
                <p class="fw-bold mb-1">No se ha podido guardar el producto:</p>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $escape($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="crear.php">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="nombre">Nombre</label>
                    <input
                        type="text"
                        class="form-control"
                        name="nombre"
                        id="nombre"
                        value="<?= $escape($values["nombre"]) ?>"
                        maxlength="200"
                        required
                    >
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="nombre_corto">Nombre corto</label>
                    <input
                        type="text"
                        class="form-control"
                        name="nombre_corto"
                        id="nombre_corto"
                        value="<?= $escape($values["nombre_corto"]) ?>"
                        maxlength="50"
                        required
                    >
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="pvp">Precio (€)</label>
                    <input
                        type="number"
                        class="form-control"
                        name="pvp"
                        id="pvp"
                        value="<?= $escape($values["pvp"]) ?>"
                        min="0"
                        step="0.01"
                        required
                    >
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="familia">Familia</label>
                    <select class="form-select" name="familia" id="familia" required>
                        <option value="">Selecciona una familia</option>
                        <?php foreach ($familias as $familia): ?>
                            <option
                                value="<?= $escape($familia["cod"]) ?>"
                                <?= (string) $familia["cod"] === $values["familia"] ? "selected" : "" ?>
                            ><?= $escape($familia["nombre"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold" for="descripcion">Descripción</label>
                <textarea class="form-control" name="descripcion" id="descripcion" rows="6"><?= $escape($values["descripcion"]) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Crear</button>
            <button type="reset" class="btn btn-success">Limpiar</button>
            <a class="btn btn-light" href="listado.php">Volver</a>
        </form>
    </main>
</body>

</html>
