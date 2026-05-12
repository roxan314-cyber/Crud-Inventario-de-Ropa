<?php
$servidor   = "localhost";  
$usuario    = "root";         
$contrasena = "";            
$nombre_bd  = "tienda_ropa";  

try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$nombre_bd", $usuario, $contrasena);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅¡Felicidades! La conexión funciona perfectamente";
} catch (PDOException $error) {
    echo "❌ Error de conexión: " . $error->getMessage();
}
?>