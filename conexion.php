<?php
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$baseDeDatos = "tienda_ropa";

try {
    // Esta es la línea que crea la conexión
    $conexion = new PDO("mysql:host=$servidor;dbname=$baseDeDatos", $usuario, $contrasena);
    
    // Aquí es donde estaba el mensaje. Lo dejamos vacío o comentado para que no estorbe.
    // echo "La conexión funciona perfectamente"; 

} catch (PDOException $error) {
    // Si hay un error, lo mostramos para saber qué pasó
    echo "Error de conexión: " . $error->getMessage();
}
?>
