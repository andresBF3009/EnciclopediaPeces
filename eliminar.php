<?php
// Conexión a la base de datos MySQL
$conexion = new mysqli("localhost", "Pezmysql", "pescaito_1234;", "enciclopedia_peces");
if ($conexion->connect_error) die("Conexión fallida: " . $conexion->connect_error);

// Verifica que se haya enviado un ID de pez por GET
if (!isset($_GET['id'])) {
    header("Location: peces1.php");
    exit();
}

$id = (int)$_GET['id'];

// Obtiene los datos del pez a eliminar
$pez = $conexion->query("SELECT * FROM peces WHERE idPeces = $id")->fetch_assoc();

// Si no se encuentra el pez, muestra un mensaje y termina la ejecución
if (!$pez) {
    echo "<div class='alert alert-danger'>Pez no encontrado.</div>";
    exit();
}

// Procesa el formulario cuando se confirma la eliminación por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar'])) {
        // Elimina el pez de la base de datos
        $conexion->query("DELETE FROM peces WHERE idPeces = $id");
    }
    // Redirige a la página principal después de eliminar o cancelar
    header("Location: peces1.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Pez</title>
    <!-- Carga Bootstrap para estilos modernos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e3f2fd; }
        .container { max-width: 800px; margin-top: 40px; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        img { max-height: 200px; object-fit: contain; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Eliminar Pez</h4>
            </div>
            <div class="card-body">
                <!-- Muestra la imagen y los datos del pez a eliminar -->
                <div class="text-center mb-3">
                    <img src="img/peces/<?= $pez['imagen'] ?>" alt="<?= $pez['nombreComun'] ?>" class="img-fluid">
                </div>
                <h5><?= $pez['nombreComun'] ?></h5>
                <p><strong>Nombre científico:</strong> <?= $pez['nombreCientifico'] ?></p>
                <p><strong>Características:</strong> <?= $pez['caracteristicas'] ?></p>
                <p class="text-danger fw-bold">¿Estás seguro de que deseas eliminar este pez?</p>
                <!-- Formulario de confirmación de eliminación -->
                <form method="post">
                    <button name="confirmar" class="btn btn-danger">Sí, eliminar</button>
                    <a href="peces1.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>