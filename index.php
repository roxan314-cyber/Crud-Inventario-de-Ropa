<?php
// 1. EL CEREBRO: CONEXIÓN Y LÓGICA DE DATOS
include("conexion.php");

// --- ELIMINAR ---
if (isset($_GET['id_borrar'])) {
    $id = $_GET['id_borrar'];
    $sentencia = $conexion->prepare("DELETE FROM inventario WHERE id = :id");
    $sentencia->execute([':id' => $id]);
    header("Location: index.php");
    exit;
}

// --- GUARDAR ---
if ($_POST && isset($_POST['prenda']) && !isset($_POST['id_editar'])) {
    $sql = "INSERT INTO inventario (prenda, talla, color, precio, stock) VALUES (?, ?, ?, ?, ?)";
    $conexion->prepare($sql)->execute([$_POST['prenda'], $_POST['talla'], $_POST['color'], $_POST['precio'], $_POST['stock']]);
    header("Location: index.php");
    exit;
}

// --- ACTUALIZAR (UPDATE) ---
if (isset($_POST['id_editar'])) {
    $sql = "UPDATE inventario SET prenda=?, talla=?, color=?, precio=?, stock=? WHERE id=?";
    $conexion->prepare($sql)->execute([$_POST['prenda'], $_POST['talla'], $_POST['color'], $_POST['precio'], $_POST['stock'], $_POST['id_editar']]);
    header("Location: index.php");
    exit;
}

// --- CONSULTA ---
$lista_ropa = $conexion->query("SELECT * FROM inventario ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Totales para el resumen
$total_prendas = array_sum(array_column($lista_ropa, 'stock'));
$total_dinero = array_sum(array_column($lista_ropa, 'precio'));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MultiStock - UMC</title>
    <style>
        /* CSS ORIGINAL DE TU COMPAÑERO */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body{ font-family: Arial, sans-serif; background: linear-gradient(135deg, blue, cyan); display: flex; justify-content: center; align-items: center; min-height: 100vh; color: #444; padding: 20px; }
        .container{ background: #ffffff; box-shadow: 0 8px 15px rgba(0,0,0,0.2); border-radius: 12px; padding: 30px; width: 100%; max-width: 500px; text-align: center; animation: fadeIn 0.5s ease-in-out; }
        h1{ font-size: 1.8rem; color: #333; margin-bottom: 20px; font-weight: bold; }
        .todo-input{ width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; font-size: 16px; transition: border-color 0.3s; }
        .todo-input:focus{ outline: none; border-color: #6a11cb; box-shadow: 0 0 5px rgba(106, 17, 203, 0.2); }
        .add-btn{ width: 100%; padding: 12px; background: linear-gradient(135deg, blue, cyan); color: #fff; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: bold; transition: transform 0.2s; }
        .add-btn:hover{ transform: scale(1.02); }
        .error-message{ color: #e63946; font-size: 14px; margin-bottom: 15px; background: #ffe6e6; padding: 8px; border-radius: 4px; display: none; }
        .todo-list{ list-style: none; padding: 0; margin-top: 20px; }
        .todo-item{ display: flex; justify-content: space-between; align-items: center; background: #f5f5f5; padding: 12px; border: 1px solid #ddd; border-radius: 6px; margin: 10px 0; transition: box-shadow 0.3s; }
        .todo-item:hover{ box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .todo-text{ flex-grow: 1; text-align: left; font-size: 14px; }
        .edit-btn, .delete-btn, .save-btn{ border: none; border-radius: 6px; padding: 6px 12px; font-size: 13px; cursor: pointer; color: white; transition: transform 0.2s; margin-left: 5px; }
        .edit-btn{ background-color: #6c66ff; }
        .delete-btn{ background-color: #e63946; text-decoration: none; display: inline-block; }
        .save-btn{ background-color: #2ecc71; }
        .edit-btn:hover, .delete-btn:hover, .save-btn:hover { transform: scale(1.1); }
        .resumen { margin-top: 20px; display: flex; justify-content: space-between; font-weight: bold; background: #e0f7fa; padding: 10px; border-radius: 6px; font-size: 14px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container">
    <h1>Inventario</h1>
    <div id="errorMessage" class="error-message"></div>

    <!-- ENTRADA DE DATOS -->
    <form action="index.php" method="post">
        <input type="text" name="prenda" class="todo-input" placeholder="Prenda..." required>
        <div style="display: flex; gap: 5px;">
            <input type="text" name="talla" class="todo-input" placeholder="Talla" required>
            <input type="text" name="color" class="todo-input" placeholder="Color" required>
        </div>
        <div style="display: flex; gap: 5px;">
            <input type="number" step="0.01" name="precio" class="todo-input" placeholder="Precio $" required>
            <input type="number" name="stock" class="todo-input" placeholder="Stock" required>
        </div>
        <button type="submit" class="add-btn">Enter</button>
    </form>

    <!-- LISTA DE PRENDAS -->
    <ul class="todo-list">
        <?php foreach($lista_ropa as $ropa): ?>
            <li class="todo-item" id="item-<?php echo $ropa['id']; ?>">
                <div class="todo-text">
                    <strong><?php echo $ropa['prenda']; ?></strong><br>
                    <small><?php echo "T: {$ropa['talla']} | {$ropa['color']} | \${$ropa['precio']} | Stock: {$ropa['stock']}"; ?></small>
                </div>
                <div>
                    <button class="edit-btn" onclick='activarEdicion(<?php echo json_encode($ropa); ?>)'>Edit</button>
                    <a href="index.php?id_borrar=<?php echo $ropa['id']; ?>" class="delete-btn" onclick="return confirm('¿Eliminar?')">Delete</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- RESUMEN AUTOMÁTICO -->
    <div class="resumen">
        <span>Prendas: <?php echo $total_prendas; ?></span>
        <span>Valor: $<?php echo number_format($total_dinero, 2); ?></span>
    </div>
</div>

<script>
    function showErrorMessage(msg) {
        const div = document.getElementById("errorMessage");
        div.textContent = msg;
        div.style.display = "block";
        setTimeout(() => div.style.display = "none", 3000);
    }

    function activarEdicion(datos) {
        const li = document.getElementById('item-' + datos.id);
        li.style.flexDirection = "column";
        li.innerHTML = `
            <form action="index.php" method="post" style="width: 100%;">
                <input type="hidden" name="id_editar" value="${datos.id}">
                <input type="text" name="prenda" value="${datos.prenda}" class="todo-input" style="margin-bottom:5px">
                <div style="display:flex; gap:5px; margin-bottom:10px">
                    <input type="number" name="precio" value="${datos.precio}" class="todo-input" style="margin:0">
                    <input type="number" name="stock" value="${datos.stock}" class="todo-input" style="margin:0">
                    <input type="hidden" name="talla" value="${datos.talla}">
                    <input type="hidden" name="color" value="${datos.color}">
                </div>
                <button type="submit" class="save-btn">Save</button>
                <button type="button" class="delete-btn" onclick="location.reload()">X</button>
            </form>
        `;
    }
</script>

</body>
</html>
