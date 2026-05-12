<?php
// 1. CONEXIÓN Y LÓGICA (Siempre al principio)
include("conexion.php");

// --- BLOQUE PARA ELIMINAR ---
if (isset($_GET['id_borrar'])) {
    $id = $_GET['id_borrar'];
    try {
        $sentencia_borrar = $conexion->prepare("DELETE FROM inventario WHERE id = :id");
        $sentencia_borrar->bindParam(':id', $id);
        $sentencia_borrar->execute();
        // Redireccionamos para limpiar la URL y evitar errores
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        echo "Error al eliminar: " . $e->getMessage();
    }
}

// --- BLOQUE PARA GUARDAR ---
if ($_POST) {
    $prenda = $_POST['prenda'];
    $talla  = $_POST['talla'];
    $color  = $_POST['color'];
    $precio = $_POST['precio'];
    $stock  = $_POST['stock'];

    try {
        $sql = "INSERT INTO inventario (prenda, talla, color, precio, stock) 
                VALUES (:prenda, :talla, :color, :precio, :stock)";
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute([
            ':prenda' => $prenda,
            ':talla'  => $talla,
            ':color'  => $color,
            ':precio' => $precio,
            ':stock'  => $stock
        ]);
        // Redireccionamos tras guardar para que al refrescar NO se duplique la prenda
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        echo "Error al guardar: " . $e->getMessage();
    }
}

// --- BLOQUE PARA LEER (Consultar datos) ---
$sentencia_leer = $conexion->prepare("SELECT * FROM inventario");
$sentencia_leer->execute();
$lista_ropa = $sentencia_leer->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario UMC</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 40px; background-color: #f8f9fa; color: #333; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #0056b3; text-align: center; }
        form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .full-width { grid-column: span 2; }
        label { font-weight: bold; margin-bottom: -10px; }
        input, select { padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        button.btn-guardar { grid-column: span 2; background-color: #28a745; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; font-size: 16px; }
        button.btn-guardar:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background-color: #0056b3; color: white; }
        .btn-delete { background-color: #dc3545; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; }
        .btn-edit { background-color: #ffc107; color: black; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; margin-right: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h2>👕 Sistema de Inventario de Ropa</h2>
    
    <form action="index.php" method="post">
        <div class="full-width">
            <label>Nombre de la Prenda:</label>
            <input type="text" name="prenda" required placeholder="Ej. Franela Deportiva">
        </div>
        <div>
            <label>Talla:</label>
            <select name="talla">
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            </select>
        </div>
        <div>
            <label>Color:</label>
            <input type="text" name="color" required placeholder="Azul, Rojo...">
        </div>
        <div>
            <label>Precio ($):</label>
            <input type="number" step="0.01" name="precio" required placeholder="0.00">
        </div>
        <div>
            <label>Stock:</label>
            <input type="number" name="stock" required placeholder="Cantidad">
        </div>
        <button type="submit" class="btn-guardar">Guardar Prenda en Inventario</button>
    </form>

    <hr>

    <h3>📦 Existencias en Almacén</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Prenda</th>
                <th>Talla</th>
                <th>Color</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lista_ropa as $ropa) { ?>
            <tr>
                <td><?php echo $ropa['id']; ?></td>
                <td><?php echo $ropa['prenda']; ?></td>
                <td><?php echo $ropa['talla']; ?></td>
                <td><?php echo $ropa['color']; ?></td>
                <td>$<?php echo number_format($ropa['precio'], 2); ?></td>
                <td><?php echo $ropa['stock']; ?></td>
                <td>
                    <a href="#" class="btn-edit">Editar</a>
                    <a href="index.php?id_borrar=<?php echo $ropa['id']; ?>" 
                       class="btn-delete" 
                       onclick="return confirm('¿Estás seguro de eliminar esta prenda?')">
                       Eliminar
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
// Extraemos solo los precios y los stocks en listas separadas
$todos_los_precios = array_column($lista_ropa, 'precio');
$todos_los_stocks = array_column($lista_ropa, 'stock');

// Sumamos los valores
$total_dinero = array_sum($todos_los_precios);
$total_prendas = array_sum($todos_los_stocks);
?>

<div style="margin-top: 20px; padding: 15px; background-color: #e9ecef; border-radius: 8px; display: flex; justify-content: space-around; border: 1px solid #ddd;">
    <div style="text-align: center;">
        <span style="display: block; font-size: 14px; color: #666;">Total en Inventario</span>
        <strong style="font-size: 20px; color: #28a745;"><?php echo $total_prendas; ?> piezas</strong>
    </div>
    <div style="text-align: center;">
        <span style="display: block; font-size: 14px; color: #666;">Valor Total del Stock</span>
        <strong style="font-size: 20px; color: #0056b3;">$<?php echo number_format($total_dinero, 2); ?></strong>
    </div>
</div>
</body>
</html>
