<?php
session_start();

// Si ya inició sesión, redirigir
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}

$mensaje = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuarioIngresado = $_POST['usuario'] ?? '';
    $claveIngresada = $_POST['clave'] ?? '';

    // Leer archivo JSON
    $usuarios = json_decode(file_get_contents("usuarios.json"), true);

    // Verificar credenciales
    foreach ($usuarios as $usuario) {
        if ($usuario['usuario'] === $usuarioIngresado && $usuario['clave'] === $claveIngresada) {
            $_SESSION['usuario'] = $usuarioIngresado;
            header("Location: inventario.php");
            exit();
        }
    }

    $mensaje = "❌ Usuario o contraseña incorrectos.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login con JSON</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/estilos/login.css">
</head>
<body>

    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <?php if ($mensaje): ?>
            <div class="error"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form method="POST" action="" style=" display: flex !important;
    flex-direction: column !important; align-items: center !important;">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <input type="submit" value="Ingresar">
        </form>
    </div>

</body>
</html>

