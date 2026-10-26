<!-- Tarea 3 Desarrollo web en entorno servidor 
 Jesús Bernabé Imedio `Perea

borrar.php
    Será una página php con el código necesario para borrar el producto seleccionado desde 
    "listado.php" un mensaje de información y un botón volver para volver a"listado.php". .Ver imagen
-->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- importamos el CSS de bootstrap via CDN https://getbootstrap.com/ si necesitaramos javaScript seria otro archiv version 5.3.3o-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>borrar</title>
</head>


<body>
    <?php

    #variable control
    $excepcion = false;
    $mensaje;

    #conexion
    require_once("conexion.php");

    # se ha pulsado boton volver
    if (isset($_POST['volver'])) {
        #destruyo objeto-conexión
        $conProyecto = null;
        # vuelvo al listado
        header('location: listado.php');
    }

    #comprobamos el id que recibe mediante POST
    if (isset($_POST['id'])) {
        # recuperamos
        #recupero variable con id 
        $get_id = $_POST['id'];
        #almaceno borrado
        $consulta = "DELETE FROM productos WHERE id= $get_id";

        try {
            #elimino el producto sobre objeto conexion y guardo resultado
            $conProyecto->exec($consulta);
        } catch (PDOException $ex) {

            $mensaje = "EXCEPCION CAPTURADA: Error consulta DELETE para eliminar producto:   " . "\n" . $ex->getMessage();
            $excepcion = true;
            #destruyo conexion
            $conProyecto = null;
        }
    } else {
        #destruyo objeto-conexión
        $conProyecto = null;
        # vuelvo al listado
        header('location: listado.php');
    }


    # si no hay excepcion indico  el registro borrado
    if ($excepcion) {

        echo "<div class='container mt-3'><p class='text-danger font-weight-
        bold'>$mensaje</p></div>";
    } else {
        #muestro resultado
        echo "<div>" . "´<b>El producto de Código: </b>" . $get_id . " <b>Borrado correctamente.</b> </div>";
        #destruyo objeto-conexión
        $conProyecto = null;
    }

    ?>
    <!--muestro botón volver-->
    <form method="post">
        <input type="submit" class="btn btn-light" name="volver" value="volver">
    </form>



</body>

</html>