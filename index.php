<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Login</h2>
  <form method="POST" action="index.php">
    <input type="text" name="usuario" placeholder="Usuario" required><br><br>
    <input type="password" name="password" placeholder="Contraseña" required><br><br>
    <button type="submit">Iniciar sesión</button>
  </form>
  <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    $usuarios = json_decode(file_get_contents("usuarios.json"), true);
    foreach ($usuarios as $u) {
        if ($u["usuario"] === $usuario && $u["password"] === $password) {
            $_SESSION["usuario"] = $usuario;
            header("Location: inventario.php");
            exit();
        }
    }
    echo "<p style='color:red;'>Usuario o contraseña incorrectos</p>";
}
?>
</body>
</html>
