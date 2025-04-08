<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo = [
        "usuario" => $_POST["usuario"],
        "password" => $_POST["password"]
    ];

    $usuarios = json_decode(file_get_contents("usuarios.json"), true);
    foreach ($usuarios as $u) {
        if ($u["usuario"] === $nuevo["usuario"]) {
            $error = "El usuario ya existe.";
            break;
        }
    }

    if (!isset($error)) {
        $usuarios[] = $nuevo;
        file_put_contents("usuarios.json", json_encode($usuarios, JSON_PRETTY_PRINT));
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
</head>
<body>
  <h2>Registro</h2>
  <form method="POST">
    <input type="text" name="usuario" placeholder="Usuario" required><br><br>
    <input type="password" name="password" placeholder="Contraseña" required><br><br>
    <button type="submit">Registrar</button>
  </form>
  <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <p><a href="index.php">Volver al login</a></p>
</body>
</html>
