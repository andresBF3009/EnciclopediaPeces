<?php
// Habilita la visualización de errores para facilitar la depuración durante el desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia la sesión para manejar el acceso de usuarios
session_start();

// Establece la conexión con la base de datos MySQL
$conexion = new mysqli("localhost", "Pezmysql", "pescaito_1234;", "enciclopedia_peces");

// Inicializa el mensaje de error (si lo hay)
$mensaje = "";

// Procesa el formulario cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Escapa el nombre de usuario para evitar inyección SQL
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    // Encripta la contraseña ingresada usando SHA-256
    $contrasena = hash('sha256', $_POST['contrasena']);

    // Consulta para verificar si existe el usuario con esa contraseña
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
    $resultado = $conexion->query($sql);

    // Si hay un usuario válido, inicia sesión y redirige
    if ($resultado->num_rows === 1) {
        $_SESSION['usuario'] = $usuario;
        header("Location: peces1.php");
        exit();
    } else {
        // Si no, muestra mensaje de error
        $mensaje = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Enciclopedia</title>
    <!-- Carga Bootstrap para estilos responsivos y modernos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4>Iniciar sesión</h4>
                </div>
                <div class="card-body">
                    <!-- Muestra el mensaje de error si existe -->
                    <?php if ($mensaje): ?>
                        <div class="alert alert-danger"><?= $mensaje ?></div>
                    <?php endif; ?>
                    <!-- Formulario de inicio de sesión -->
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" name="usuario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="contrasena" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>