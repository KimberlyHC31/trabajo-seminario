<?php
session_start();

// Verificar inicio de sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Ruta JSON
$archivo = 'inventario.json';
if (!file_exists($archivo)) {
    file_put_contents($archivo, json_encode([]));
}

// Cargar inventario
$inventario = json_decode(file_get_contents($archivo), true);

// Agregar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registrar'])) {
        $nuevo = [
            'nombre' => $_POST['nombre'],
            'cantidad' => (int)$_POST['cantidad'],
            'precio' => (float)$_POST['precio']
        ];
        $inventario[] = $nuevo;
        file_put_contents($archivo, json_encode($inventario, JSON_PRETTY_PRINT));
        header("Location: inventario.php");
        exit();
    }

    // Editar producto
    if (isset($_POST['editar']) && isset($_POST['index'])) {
        $index = (int)$_POST['index'];
        $inventario[$index] = [
            'nombre' => $_POST['nombre'],
            'cantidad' => (int)$_POST['cantidad'],
            'precio' => (float)$_POST['precio']
        ];
        file_put_contents($archivo, json_encode($inventario, JSON_PRETTY_PRINT));
        header("Location: inventario.php");
        exit();
    }

    // Eliminar producto
    if (isset($_POST['eliminar']) && isset($_POST['index'])) {
        $index = (int)$_POST['index'];
        array_splice($inventario, $index, 1);
        file_put_contents($archivo, json_encode($inventario, JSON_PRETTY_PRINT));
        header("Location: inventario.php");
        exit();
    }

    // Cerrar sesión
    if (isset($_POST['cerrar_sesion'])) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
}

// Modo edición
$modoEdicion = false;
$productoEditar = ['nombre' => '', 'cantidad' => '', 'precio' => ''];
$indiceEditar = null;

if (isset($_GET['editar'])) {
    $indiceEditar = (int)$_GET['editar'];
    if (isset($inventario[$indiceEditar])) {
        $productoEditar = $inventario[$indiceEditar];
        $modoEdicion = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            padding: 30px;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        input, button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            font-size: 16px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            background: white;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        .acciones form {
            display: inline;
        }

        .acciones button {
            background-color: #28a745;
            margin: 0 2px;
            padding: 8px 12px;
            font-size: 14px;
        }

        .acciones .eliminar {
            background-color: #dc3545;
        }

        .cerrar {
            text-align: center;
            margin-top: 20px;
        }

        .cerrar button {
            background-color: #6c757d;
        }
    </style>
</head>
<body>

<h1><?php echo $modoEdicion ? 'Editar Producto' : 'Registro de Inventario'; ?></h1>

<div class="form-container">
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del producto" required value="<?php echo htmlspecialchars($productoEditar['nombre']); ?>">
        <input type="number" name="cantidad" placeholder="Cantidad" required min="0" value="<?php echo $productoEditar['cantidad']; ?>">
        <input type="number" name="precio" step="0.01" placeholder="Precio S/" required min="0" value="<?php echo $productoEditar['precio']; ?>">

        <?php if ($modoEdicion): ?>
            <input type="hidden" name="index" value="<?php echo $indiceEditar; ?>">
            <button type="submit" name="editar">Guardar Cambios</button>
        <?php else: ?>
            <button type="submit" name="registrar">Agregar Producto</button>
        <?php endif; ?>
    </form>
</div>

<?php if (!empty($inventario)): ?>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio (S/)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventario as $i => $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                    <td><?php echo $item['cantidad']; ?></td>
                    <td><?php echo number_format($item['precio'], 2); ?></td>
                    <td class="acciones">
                        <a href="?editar=<?php echo $i; ?>"><button>Editar</button></a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="index" value="<?php echo $i; ?>">
                            <button class="eliminar" type="submit" name="eliminar" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align:center;">No hay productos registrados aún.</p>
<?php endif; ?>

<div class="cerrar">
    <form method="POST">
        <button type="submit" name="cerrar_sesion">Cerrar sesión</button>
    </form>
</div>

</body>
</html>
