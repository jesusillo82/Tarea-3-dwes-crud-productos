<?php
/*Para no repetir el código de la conexión a la base de datos en cada archivo cro este archivo
conexion con PDO PHP Data Object
- apartado 3.2.1 del Temario

    -Consideraciones:

        - Para establecer una conexión con una base de datos utilizando PDO, debes instanciar un objeto de la clase PDO pasándole 
            los siguientes parámetros (solo el primero es obligatorio):

            -Origen de datos (DSN). Es una cadena de texto que indica qué controlador se va a utilizar y a continuación, 
                separadas por el carácter dos puntos, los parámetros específicos necesarios por el controlador, 
                como por ejemplo el nombre o dirección IP del servidor y el nombre de la base de datos.
            -Nombre de usuario con permisos para establecer la conexión.
            -Contraseña del usuario.
            -Opciones de conexión, almacenadas en forma de array.

        - Para cerrar la conexión hay que saber que la misma permanecerá activa durante el tiempo de vida del objeto PDO. 
            Para cerrarla, es necesario destruir el objeto asegurándose de que todas las referencias a él existentes sean eliminadas; esto se puede hacer asignando null a la variable que contiene el objeto.
            $conProyecto = null;


*/

//parámetros
$host = "localhost"; //nombre o direccion IP del servidor
$db = "proyecto"; //base de datos
$user = "root"; //usuario
$pass = ""; //contraseña
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4"; /*data source name,indica el controlador y 
los parámetros que va a utilizar, utf8mb4 es utf8 con soporte para emojis */


#establezco conexion
try {
    //instancio objeto de clase PHP Data Object
    $conProyecto = new PDO($dsn, $user, $pass);
    // defnimmos atributo ATTR_ERRMODE para controlar los errores
    /* opciones
    ERRMODE_SILENT: El modo por defecto, no muestra errores (se recomienda en entornos en producción).
    ERRMODE_WARNING: Además de establecer el código de error, emitirá un mensaje E_WARNING, 
        es el modo empleado para depurar o hacer pruebas para ver errores sin interrumpir el flujo de la aplicación.
    ERRMODE_EXCEPTION: Además de establecer el código de error, lanzará una PDOException que podemos capturar en un bloque try catch(). 
*/
    /*metodo setAttribute para modificar parametros
Establezco ERRMODE_EXCEPTION-> Cuando se produce un error lanza una excepción utilizando el manejador propio PDOException.
posteriormente las gestionare como me indica el enunciado */
    $conProyecto->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $ex) {
    die("EXCEPCIÓN CAPTURADA - Error en la conexion, mensaje de error:  " . $ex->getMessage());
}






