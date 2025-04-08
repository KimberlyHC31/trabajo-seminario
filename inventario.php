<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

$usuario = $_SESSION["usuario"];
$archivoInventario = "inventarios/{$usuario}.json";
if (!file_exists($archivoInventario)) {
    file_put_contents($archivoInventario, json_encode([]));
}

$productos = json_decode(file_get_contents($archivoInventario), true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevoProducto = [
        "nombre" => $_POST["nombre"],
        "cantidad" => (int)$_POST["cantidad"],
        "precio" => (float)$_POST["precio"]
    ];

    $productos[] = $nuevoProducto;
    file_put_contents($archivoInventario, json_encode($productos, JSON_PRETTY_PRINT));
    header("Location: inventario.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Inventario de <?php echo htmlspecialchars($usuario); ?></h2>

  <form method="POST">
    <input type="text" name="nombre" placeholder="Producto" required>
    <input type="number" name="cantidad" placeholder="Cantidad" required>
    <input type="number" step="0.01" name="precio" placeholder="Precio" required>
    <button type="submit">Agregar</button>
  </form>

  <h3>Lista de productos:</h3>
  <table border="1">
    <tr>
      <th>Nombre</th>
      <th>Cantidad</th>
      <th>Precio</th>
    </tr>
    <?php foreach ($productos as $p): ?>
    <tr>
      <td><?= htmlspecialchars($p["nombre"]) ?></td>
      <td><?= $p["cantidad"] ?></td>
      <td>$<?= number_format($p["precio"], 2) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>
