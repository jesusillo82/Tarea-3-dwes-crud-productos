<!-- Tarea 3 Desarrollo web en entorno servidor 
 Jesús Bernabé Imedio `Perea

update.php
    - Será similar a crear.php pero en este caso nos aparecerá un formulario con los campos rellenos con los valores del producto seleccionado 
    desde "listado.php" incluido el select donde seleccionamos la familia Ver imagen

    -Pasaremos el código de producto por "get" tanto para "detalle.php" como para "update.php". Utilizando en el enlace "detalle.php?id=cod" .
    En ambas páginas comprobaremos que esta variable existe, si no redireccionaremos a "listado.php" para esto podemos usar "header('Location:listado.php')"

    - La lista de selección familia de la página "update.php" tiene seleccionada la familia del producto.
    
    
    - $conProyecto->exec($consulta); EXEC se usa para ejecutar sentencias SQL que no devuelven datos
            
        - Devuelve un número entero con la cantidad de filas afectadas por la consulta.

        - Si la consulta falla, lanza una excepción de tipo PDOexception si está dentro de un try-catch.

        - ejemplo de USO        $filas_afectadas = $conProyecto->exec($consulta);
                            echo "Se actualizaron $filas_afectadas registros.";

    - Para consultas que devuelvan datos SELECT usamos metodo query

    -->
<?php
//variable de control y mensaje a mostrar
$error = false; 
$mensaje = "";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- importamos el CSS de bootstrap via CDN https://getbootstrap.com/ si necesitaramos javaScript seria otro archiv version 5.3.3o-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>actualizar</title>
</head>

<!-- esta clase bootstrap "bg-info" pone el fondo en azul, pt-3 es pading top aplico al titulo-->

<body class="bg-info">
    <div class="container">
        <h2 class="text-center pt-3">Modificar producto</h2>
        <?php
        #establecemos conexion
        require_once("conexion.php");

        # logica de negocio

        # si hemos dado a boton crear existira la variable
        if (isset($_POST['modificar'])) {

            # recojo los datos para insertar en la base de datos segun atributo name
            $nombre_post = $_POST['nombre'];
            $nombreCorto_post = $_POST['nombre_corto'];
            $precio_post = $_POST['pvp'];
            $familia_post = $_POST['familia'];
            $descripcion_post = $_POST['descripcion'];

            #id enviado por el campo oculto
            $post_id = $_POST['id'];

             #creo la consulta update
             $consulta = "UPDATE productos SET nombre = '$nombre_post', nombre_corto ='$nombreCorto_post', descripcion ='$descripcion_post',pvp = $precio_post, familia='$familia_post' WHERE id = $post_id";

            try {
            
                #ejecuto la consulta sobre la conexion
                $conProyecto->exec($consulta);

            } catch (PDOException $ex) {
                
                $mensaje = "EXCEPCION CAPTURADA: Error consulta UPDATE al introducir nuevos datos en la base de datos:   " . "\n" . $ex->getMessage();
                $error = true;
            } 


            /* FORMA MAS SEGURA DE HACERLO

                - la forma anterior es vulnerable a inyeccion de SQL puesto que los valores
                    se inyectan directamente en la consulta

                    - Usar PDO con BINDPARAM() en consultas preparadas PREPARED STATEMENNTS


                    try {
                            $consulta = "UPDATE productos SET nombre = :nombre, nombre_corto = :nombre_corto, descripcion = :descripcion, pvp = :pvp, familia = :familia WHERE id = :id";

                            $stmt = $conProyecto->prepare($consulta);
                            $stmt->bindParam(':nombre', $nombre_post, PDO::PARAM_STR);
                            $stmt->bindParam(':nombre_corto', $nombreCorto_post, PDO::PARAM_STR);
                            $stmt->bindParam(':descripcion', $descripcion_post, PDO::PARAM_STR);
                            $stmt->bindParam(':pvp', $precio_post, PDO::PARAM_INT);
                            $stmt->bindParam(':familia', $familia_post, PDO::PARAM_STR);
                            $stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
                            
                            $stmt->execute();

                    } catch (PDOException $ex) {
                        $mensaje = "EXCEPCIÓN CAPTURADA: Error al ejecutar la consulta UPDATE: " . $ex->getMessage();
                        $error = true;
                    }
            
            
            
            
            */

            # se ha clickeado boton volver
        } else if (isset($_POST['Volver'])) {

            # destruyo objeto-conexión
            $conProyecto = null;

            # volvemos al listado
            header('location: listado.php');
        }

        #comprobamos que id se ha enviado con get desde listado.php con formato "update.php?id=cod"
        if (isset($_GET["id"])) {
            #recupero id
            $get_id = $_GET['id'];
             # selecciono los elementos
            $consulta = "SELECT * FROM productos WHERE id = $get_id";
            
            try {
                #ejecuto consulta ssobre conexión
                $resultado = $conProyecto->query($consulta);
                 #obtengo objeto con los datos del producto desde tabla PRODUCTOS
                 $registro = $resultado->fetch(PDO::FETCH_OBJ);

            } catch (PDOException $ex) {

                $mensaje = "EXCEPCION CAPTURADA: Error consulta SELECT para mostrar datos del producto:   " . "\n" . $ex->getMessage();
                $error = true;
            }
           
        } else {
            # destruyo objeto-conexión
            $conProyecto = null;
            # volvemos al listado
            header('location:listado.php');
        }

        ?>
        <!-- creamos formulario-->
        <form method="post">
            <!-- el formulario mostrará por defecto los valores del producto que queremos modificar salvo el id
         por lo que lo enviaremos mediante campo oculto-->
            <input type="hidden" name="id" value="<?php echo $registro->id; ?>">

            <!-- defino dos columnas del mismo tamaño
             alineadas al comienzo, con bootstrap row mb-3 agrega margen a las filas para separar elementos
             
             - aparece en 2 columnas porque estgoy creando una fila row y dentro de esta
             2 div con clase columna col

             - La clase .form-control en Bootstrap se usa para aplicar estilos a los elementos de formulario, como <input>, <textarea> y <select>.
                 Esta clase les da un diseño uniforme y responsivo.
             
            -La clase .form-select en Bootstrap se usa para dar estilo a los menús desplegables (<select>), 
             
             -->
            <div class="row mb-3">
                <div class=" col">
                    <!--nombre fw-bold para que el texto este en negrita-->
                    <label class="fw-bold" for="nombre">Nombre</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" value="<?php echo $registro->nombre; ?>">

                </div>
                <div class="col">
                    <!-- nombre corto-->
                    <label class="fw-bold" for="nombre_corto">Nombre_corto</label>
                    <input type="text" class="form-control" name="nombre_corto" id="nombre_corto" placeholder="Nombre corto" value="<?php echo $registro->nombre_corto; ?>">

                </div>
            </div>

            <div class="row mb-3">
                <div class=" col">
                    <!--precio con formao dos decimales como indica la imagen enunciado-->
                    <label class="fw-bold" for="pvp">Precio (€)</label>
                    <input type="number" min="1" step="0.01" class="form-control" name="pvp" id="pvp" placeholder="Precio (€)" value="<?php echo $registro->pvp; ?>">

                </div>
                <div class=" col">
                    <!--familia será un select option-->
                    <label class="fw-bold" for="familia">Familia</label>
                    <select class="form-select" class="fw-bolder" id="familia" name="familia">
                        <?php
                        
                        try {
                            #guardamos resultado de la consulta ( todos los registros de la tabla familias)
                            $resultado = $conProyecto->query("SELECT * FROM familias");
                        } catch (PDOException $ex) {
                            $mensaje = "EXCEPCION CAPTURADA: Error consulta SELECT para mostrar datos del producto:   " . "\n" . $ex->getMessage();
                            $error = true;
                        }
                        # IMPORTANTE según evaluación debe tener seleccionada la familia del producto. 

                        /* generamos el desplegable de opciones 
                            recuperamos fila por fila en formato objeto ( cod nombre)
                        */

                        // regis contiene objeto de tabla FAMILIAS cod, nombre
                        // registro contiene objeto d etabbla PRODUCTOS id,mombre, nombre_corto, descripcion, pvp, familia
                        while ($regis = $resultado->fetch(PDO::FETCH_OBJ)) {
                            # genero el select option con la propiedad nombre del objeto producto
                            # value recogera el codigo (cod) que se enviara por el formulario

                            # Si el codigo devuelto del while coincida con la familia del producto
                            if ($regis->cod == $registro->familia) {
                                # selecciono según enunciado conn selected
                                echo "<option selected value = '$regis->cod'> $regis->nombre </option>"; //si separo -> a veces da error
                            } else {

                                echo "<option value = '$regis->cod'> $regis->nombre </option>";
                            }
                        }

                        ?>

                    </select>

                </div>

            </div>


            <!--  TEXT AREA con descpripcion del producto-->
            <div class="row mb-3">
                <!--col-12 ocupa todo ancho en una sola columna  si pongo la mitad divido etc-->
                <div class="col-12">
                    <label class="fw-bold" for="descripcion">Descripcion</label>
                    <textarea class="form-control" name="descripcion" id="descripcion" cols="10" rows="10"><?php echo $registro->descripcion; ?></textarea>

                </div>


            </div>

            <!-- defino los botones -->
            <input type="submit" class="btn btn-primary" name="modificar" value="modificar">
            <input type="submit" class="btn btn-success" name="Volver" value="Volver">

        </form>

    </div>

    <?php

    # BLOQUE COMUNICACION EXCEPCIONES
                        
    # este bloque muestra mensaje de error si ha sido capturado por la excepcion
    if ($error) {
        echo "<div class='container mt-3'><p class='text-danger font-weight-
        bold'>$mensaje</p></div>";

        #cierro conexion
        $conProyecto = null;
    }

    ?>


</body>

</html>