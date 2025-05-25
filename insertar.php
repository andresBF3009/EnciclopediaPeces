<?php
// Inicia la sesión y verifica que el usuario esté autenticado
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos MySQL
$conexion = new mysqli("localhost", "Pezmysql", "pescaito_1234;", "enciclopedia_peces");
if ($conexion->connect_error) die("Conexión fallida: " . $conexion->connect_error);

// Recupera los datos para los selectores desplegables (hábitat, estado de conservación, tipo de especie)
$habitats = $conexion->query("SELECT * FROM habitat");
$estados = $conexion->query("SELECT * FROM estado_conservacion");
$tipos = $conexion->query("SELECT * FROM tipo_especie");

// Procesa el formulario cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $cientifico = $_POST['cientifico'];
    $caracteristicas = $_POST['caracteristicas'];
    $habitat = $_POST['habitat'];
    $estado = $_POST['estado'];
    $tipo = $_POST['tipo'];
    $imagen = $_POST['imagen'];

    // Inserta el nuevo pez en la base de datos usando una consulta preparada
    $sql = "INSERT INTO peces (nombreComun, nombreCientifico, caracteristicas, idHabitatFK, idEstadoConservacionFK, idTipoEspecieFK, imagen)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssiiis", $nombre, $cientifico, $caracteristicas, $habitat, $estado, $tipo, $imagen);
    $stmt->execute();

    // Redirige a la página principal después de insertar
    header("Location: peces1.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Insertar Pez</title>
    <!-- Carga Bootstrap para estilos modernos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e3f2fd; }
        .container { max-width: 800px; margin-top: 40px; }
        .header { background-color: #198754; color: white; padding: 20px; border-radius: 10px 10px 0 0; }
        .form-container { background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header text-center">
            <h2>Insertar Nuevo Pez</h2>
        </div>
        <div class="form-container">
            <!-- Formulario para insertar un nuevo pez -->
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nombre común</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre científico</label>
                    <input type="text" name="cientifico" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Características</label>
                    <textarea name="caracteristicas" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagen (nombre del archivo)</label>
                    <input type="text" name="imagen" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hábitat</label>
                    <select name="habitat" class="form-select">
                        <!-- Opciones para el selector de hábitat -->
                        <?php while($h = $habitats->fetch_assoc()) {
                            echo "<option value='{$h['idHabitat']}'>{$h['tipo']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estado de conservación</label>
                    <select name="estado" class="form-select">
                        <!-- Opciones para el selector de estado de conservación -->
                        <?php while($e = $estados->fetch_assoc()) {
                            echo "<option value='{$e['idEstadoConservacion']}'>{$e['estado']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo de especie</label>
                    <select name="tipo" class="form-select">
                        <!-- Opciones para el selector de tipo de especie -->
                        <?php while($t = $tipos->fetch_assoc()) {
                            echo "<option value='{$t['idTipoEspecie']}'>{$t['tipo']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success">Insertar</button>
                    <a href="peces1.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>