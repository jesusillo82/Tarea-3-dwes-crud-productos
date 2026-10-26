<!-- Tarea 3 Desarrollo web en entorno servidor 
 Jesús Bernabé Imedio `Perea

    detalle.php
        - Mostrará todo los detalles del producto seleccionado.
        - Pasaremos el código de producto por "get" tanto para "detalle.php" como para "update.php". Utilizando en el enlace "detalle.php?id=cod" .En ambas páginas comprobaremos que esta variable existe, 
        si no redireccionaremos a "listado.php" para esto podemos usar "header('Location:listado.php')" 
-->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- importamos el CSS de bootstrap via CDN https://getbootstrap.com/ si necesitaramos javaScript seria otro archiv version 5.3.3o-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Detalles</title>
</head>

<!-- esta clase bootstrap pone el fondo en azul-->

<body class="bg-info">
    <div class="container">
        <h2 class="text-center pt-3">Detalle Producto</h2>

        <?php

        # se ha clickeado boton volver
        if (isset($_POST['volver'])) {

            # destruyo objeto.Cierro conexión
            $conProyecto = null;
            # volvemos al listado
            header('location: listado.php');
        }


        #comprobamos que id se ha enviado con get "detalle.php?id=cod"
        if (!isset($_GET['id'])) { # no existe id
            # cerramos conexion
            $conProyecto = null;
            #redireccionamos a listado
            header('location: listado.php');
        }

        # si existe el id pasamos a crear la conexión

        #creamos conexion
        require_once("conexion.php");
        #recupero variable con id del producto
        $get_id = $_GET['id'];
        #selecciono fila segun identificador
        $consulta = "SELECT * FROM productos WHERE id= $get_id";


        try {
            #ejecuto consulta sobre objeto conexion y guardo en resultado
            $resultado = $conProyecto->query($consulta);

        } catch (PDOException $ex) {
            # en este caso he optado por no mostrar mensaje sino por colver a listado
            #destruyo conexion
            $conProyecto = null;
            #vuelvo al listado
            header("location:listado.php");
        }


        #convierto a objeto y almacenos en registro
        $registro = $resultado->fetch(PDO::FETCH_OBJ);

        /*controla que el id devuelva un producto ( id existe en base de datos)
            por ejemplo si marcamos un id desde navegador que no existe 
            ejemplo: http://localhost/DWES/Tareas/imedio_perea_jesusbernabe_DWES03_Tarea/detalle.php?id=50
        */

        if ($registro == null) {
            #cierro conexión. Destruyo objeto
            $conProyecto = null;

            #redireccionamos a listado
            header('location: listado.php');
        }

        #muestro el card con los detalles
        ?>

        <!-- bootstrap cards. A card is a flexible and extensible content container.
          - bg-info background color info es azul
        
        -->

        <div class="card bg-info">
            <div class="card-header text-center">
                <?php echo "<b>" . $registro->nombre . "</b>"; ?>
            </div>
            <div class="card-body">
                <h5 class="card-title text-center"><?php echo $registro->id; ?></h5>
                <p class="card-text"><b>Nombre:</b> <?php echo $registro->nombre; ?></p>
                <p class="card-text"><b>Nombre Corto:</b><?php echo $registro->nombre_corto; ?></p>
                <p class="card-text"><b>Codigo Familia:</b><?php echo $registro->familia; ?></p>
                <p class="card-text"><b>PVP(€):</b> <?php echo $registro->pvp; ?></p>
                <p class="card-text"><b>Descripcion:</b> <?php echo $registro->descripcion; ?></p>

            </div>

        </div>
        <!-- recogere si se ha pulsado botón volver
            --otra forma
                <form action="listado.php">
                    boton volver
                    <input type="submit" class="btn btn-light item-center" name="volver" value="volver">
                </form>
        -->
        <form method="post">
            <input type="submit" class="btn btn-light" name="volver" value="volver">

        </form>

    </div>

</body>

</html>