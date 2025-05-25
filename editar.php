<?php
// Habilita la visualización de errores para facilitar la depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establece la conexión con la base de datos MySQL
$conexion = new mysqli("localhost", "Pezmysql", "pescaito_1234;", "enciclopedia_peces");
if ($conexion->connect_error) die("Conexión fallida: " . $conexion->connect_error);

// Verifica que se haya enviado un ID de pez por GET
if (!isset($_GET['id'])) {
    echo "ID no especificado";
    exit();
}

$id = (int)$_GET['id'];

// Consulta para obtener los datos actuales del pez a editar
$consulta = $conexion->prepare("SELECT * FROM peces WHERE idPeces = ?");
$consulta->bind_param("i", $id);
$consulta->execute();
$resultado = $consulta->get_result();
$pez = $resultado->fetch_assoc();

// Si no se encuentra el pez, muestra un mensaje y termina la ejecución
if (!$pez) {
    echo "Pez no encontrado";
    exit();
}

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

    // Actualiza los datos del pez en la base de datos usando una consulta preparada
    $sql = "UPDATE peces SET nombreComun=?, nombreCientifico=?, caracteristicas=?, idHabitatFK=?, idEstadoConservacionFK=?, idTipoEspecieFK=?, imagen=? WHERE idPeces=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssiiisi", $nombre, $cientifico, $caracteristicas, $habitat, $estado, $tipo, $imagen, $id);
    $stmt->execute();

    // Redirige a la página principal después de actualizar
    header("Location: peces1.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Pez</title>
    <!-- Carga Bootstrap para estilos modernos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e3f2fd; }
        .container { max-width: 800px; margin-top: 40px; }
        .header { background-color: #0d6efd; color: white; padding: 20px; border-radius: 10px 10px 0 0; }
        .form-container { background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .fish-image { width: 300px; height: 200px; object-fit: contain; display: block; margin: 0 auto 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header text-center">
            <h2>Editar Pez</h2>
        </div>
        <div class="form-container">
            <!-- Imagen y características actuales del pez -->
            <img src="img/peces/<?php echo htmlspecialchars($pez['imagen']); ?>" alt="Imagen del pez" class="fish-image">
            <p class="text-center"><strong><?php echo $pez['caracteristicas']; ?></strong></p>
            <!-- Formulario para editar los datos del pez -->
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nombre común</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($pez['nombreComun']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre científico</label>
                    <input type="text" name="cientifico" class="form-control" value="<?php echo htmlspecialchars($pez['nombreCientifico']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Características</label>
                    <textarea name="caracteristicas" class="form-control" required><?php echo htmlspecialchars($pez['caracteristicas']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagen (nombre del archivo)</label>
                    <input type="text" name="imagen" class="form-control" value="<?php echo htmlspecialchars($pez['imagen']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hábitat</label>
                    <select name="habitat" class="form-select">
                        <!-- Opciones para el selector de hábitat, con la opción actual seleccionada -->
                        <?php while($h = $habitats->fetch_assoc()) {
                            $sel = $h['idHabitat'] == $pez['idHabitatFK'] ? 'selected' : '';
                            echo "<option value='{$h['idHabitat']}' $sel>{$h['tipo']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estado de conservación</label>
                    <select name="estado" class="form-select">
                        <!-- Opciones para el selector de estado de conservación, con la opción actual seleccionada -->
                        <?php while($e = $estados->fetch_assoc()) {
                            $sel = $e['idEstadoConservacion'] == $pez['idEstadoConservacionFK'] ? 'selected' : '';
                            echo "<option value='{$e['idEstadoConservacion']}' $sel>{$e['estado']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo de especie</label>
                    <select name="tipo" class="form-select">
                        <!-- Opciones para el selector de tipo de especie, con la opción actual seleccionada -->
                        <?php while($t = $tipos->fetch_assoc()) {
                            $sel = $t['idTipoEspecie'] == $pez['idTipoEspecieFK'] ? 'selected' : '';
                            echo "<option value='{$t['idTipoEspecie']}' $sel>{$t['tipo']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                    <a href="peces1.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>