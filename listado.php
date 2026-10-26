<!-- Tarea 3 Desarrollo web en entorno servidor 
 Jesús Bernabé Imedio `Perea

Enunciado:

Partiendo de la base de datos 'proyecto' usada en los ejemplos y ejercicios de la unidad ,
 se trata de programar un CRUD (create, read, update, delete) que permita gestionar 
 los registros de la tabla 'productos'. La aplicación se dividirá en 5 páginas:

 listado.php
 crear.php
 detalle.php
 update.php
 borrar.php

Consideraciones:

- Para acceder a la base de datos se debe usar PDO. Controlaremos y mostraremos los posible errores. Para los estilos se recomienda usar Bootstrap.

- Pasaremos el código de producto por "get" tanto para "detalle.php" como para "update.php". Utilizando en el enlace "detalle.php?id=cod" .
En ambas páginas comprobaremos que esta variable existe, si no redireccionaremos a "listado.php" 
para esto podemos usar "header('Location:listado.php')"

Recomendaciones


- Además del manual online de PHP, se recomienda dar libre acceso a Internet para la búsqueda de información.
- Para no repetir el código de la conexión a la base de datos en cada archivo, se recomienda crear el archivo conexion.php y utilizar require o require_once cada vez que lo necesitamos.
- Para borrar un producto, crea un formulario con el action apuntando a "borrar.php" y pasa por un campo oculto el código del producto a borrar.
- Recuerda que en el "option" del HTML "<option value="v">Nombre</option>",  "v" es el valor que pasamos, "Nombre" es lo que mostramos en la lista desplegable.

-CONSIDERACIONES 

  - excepciones:

    -atributo: ATTR_ERRMODE  con los posible valores:

        - ERRMODE_SILENT: El modo por defecto, no muestra errores (se recomienda en entornos en producción).
        - ERRMODE_WARNING: Además de establecer el código de error, emitirá un mensaje E_WARNING, es el modo empleado para depurar o hacer pruebas para ver errores sin interrumpir el flujo de la aplicación.
        - ERRMODE_EXCEPTION: Además de establecer el código de error, lanzará una PDOException que podemos capturar en un bloque try catch(). Lo veremos en el apartado 4.1.

    -conexión: 
        - permanecerá activa durante el tiempo de vida del objeto PDO. 
        - Para cerrarla, es necesario destruir el objeto asegurándose de que todas las referencias a él existentes sean eliminadas; 
            esto se puede hacer asignando null a la variable que contiene el objeto.  $conProyecto = null;

    -consultas: 
        - En el caso de las consultas de acción, como INSERT, DELETE o UPDATE, el método -- exec --devuelve el número de registros afectados.
        - Si la consulta genera un conjunto de datos, como es el caso de SELECT, debes utilizar el método -- query --, que devuelve un objeto de la clase PDOStatement.

    -Transacciones: 
        - Por defecto trabaja en "autocommit". confirma de forma automática cada sentencia que ejecuta el servidor.
        - 3 formas:
            
            - beginTransaction. Deshabilita el modo "autocommit" y comienza una nueva transacción, que finalizará cuando ejecutes uno de los dos métodos siguientes.
            - commit. Confirma la transacción actual.
            - rollback. Revierte los cambios llevados a cabo en la transacción actual.



--------------  listado.php ---------------
    Mostrará en una tabla los datos código y nombre 
    y los botones para crear un nuevo registro, actualizar uno existente, 
    borrarlo o ver todos sus detalles. Ver imagen. 
-->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- importamos el CSS de bootstrap via CDN https://getbootstrap.com/ si necesitaramos javaScript seria otro archiv version 5.3.3o-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Listado</title>
</head>


<?php
    /*
    según enunciado:
        - Pasaremos el código de producto por "get" tanto para "detalle.php" como para "update.php".
    */

    # importamos para establecer conexion con BD para rellenar los campos
    require_once("conexion.php"); // si ya esta cargado no vuelve a cargarlo esta en cache

    try {
        #guardamos en resultado lo que nos devuelve la consulta
        $resultado = $conProyecto->query('SELECT * FROM productos'); //selecciona todos los elementos de la tabla productos
    } catch (PDOException $ex) {

        $mensaje = $ex->getMessage();
        #cierro conexion
        $conProyecto = null;
        die("<p class='text-danger'>EXCEPCIÓN CAPTURADA - Error en la consulta SELECT con método query, imposible mostrar listado con productos.</p><b> mensaje de error:</b>  " . $ex->getMessage());
      
    }

?>
<!--  esta clase bootstrap pone el fondo en azul -->
<body class="bg-info">
    <div class="container">
        <h2 class="text-center pt-3">Gestión de productos</h2>

        <!--boton crear con bootstrap class="mb-1" -->
        <form action="crear.php" class="mb-2">
            <button class="btn btn-success">Crear</button>
        </form>

        <!-- tabla con los productos, copiamos codigo de bootstrap-->
        <table class="table table-dark table-striped text-center">
            <thead>
                <tr>
                    <th scope="col">Detalle</th>
                    <th scope="col">Codigo</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php
                # recorro cada fila (registro) recuperando mediante metodo fetch
                # uso FETCH_OBJ para que me devuelva un objeto cuyas propiedades con los campos del registro
                #  $registro contendra un objeto con ( id,nombre,nombre_corto,descripcion,pvp,familia )
                # cuando no haya registro por recorrer fetch devolvera FALSE y dentendremos la ejecucion del bucle
                while ($registro = $resultado->fetch(PDO::FETCH_OBJ)) {
                    #finalizo PHP porque voy a mostrar en html
                ?>
                    <!-- muestro el contenido en la tabla-->
                    <tr>
                        <!-- columna boton detalle-->
                        <td>
                            <!-- Primera columna tiene un button detalle para acceder a detalle.php-->
                            <form action="detalle.php" class="mb-1" method="get">
                                <!-- value sera la propiedad id del objeto registro-->
                                <input type="hidden" name="id" value="<?php echo $registro->id; ?>">
                                <button class="btn btn-info">Detalle</button>
                            </form>
                        </td>
                        <!-- columna Codigo-->
                        <td>
                            <!-- esta columna esta asociada al id de la tabla prodcutos-->
                            <?php
                            echo $registro->id; #propiedad id de la fila recuperada
                            ?>
                        </td>
                        <!-- columna Nombre-->
                        <td>
                            <!-- esta columna esta asociada al nombre de la tabla prodcutos-->
                            <?php
                            echo $registro->nombre; #propiedad nombre del objeto recuperado
                            ?>
                        </td>
                        <!-- columna Acciones con dos botones-->
                        <td>
                            <!-- esta columna esta asociada a los archivos actualizar y borrar-->

                            <!-- boton Actualizar vinculado a update.php, class="btn btn-warning" para que sea amarillo el boton
                                 uso clase d-inline-block para que los dos form salgan alineados
                                 - envia a página update.php mediante metodo GET el id del producto a actualizar mediante value en campo oculto
                                 
                            -->
                            <form action="update.php" class="d-inline-block" class="mb-1" method="GET">
                                <input type="hidden" name="id" value="<?php echo $registro->id; ?>">
                                <button class="btn btn-warning">Actualizar</button>
                            </form>
                            <!-- boton Borrar vinculado a borrar.php

                                SEGUN ENUNCIADO:
                                 - el botón borrar será el submit de un formulario cuyo action debe ser el indicado 
                                 - Para borrar un producto, crea un formulario con el action apuntando a "borrar.php" y pasa por un campo oculto el código del producto a borrar.
                            -->


                            <!-- botón borrar enviar a la página borrar.php el value con id del producto a borrar mediante POST-->
                            <form action="borrar.php" class="d-inline-block" class="mb-1" method="post">
                                <!-- paso el id como campo oculto a la página borrar.php-->
                                <input type="hidden" name="id" value="<?php echo $registro->id; ?>">
                                <!-- clase bootstrap boton rojo-->
                                <input type="submit" class="btn btn-danger" value="Borrar">
                            </form>
                        </td>

                    </tr>

                <?php
                    echo "</tr>";
                }

                //una vez generada la tabla cierro la conexión poniendo objeto a null
                $conProyecto = null;
                ?>

            </tbody>
        </table>

    </div>

</body>

</html>