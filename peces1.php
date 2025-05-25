<?php
// Habilita la visualización de errores para facilitar la depuración durante el desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia la sesión para manejar el acceso de usuarios
session_start();

// Establece la conexión con la base de datos MySQL
$conexion = new mysqli("localhost", "Pezmysql", "pescaito_1234;", "enciclopedia_peces");
if ($conexion->connect_error) die("Conexión fallida: " . $conexion->connect_error);

// Recoge los parámetros de filtrado enviados por GET (seleccionados por el usuario)
$tipo = $_GET['tipo'] ?? '';
$estado = $_GET['estado'] ?? '';
$habitat = $_GET['habitat'] ?? '';

// Construye las condiciones de filtrado según lo seleccionado en el formulario
$filtros = [];
if ($tipo !== '') $filtros[] = "peces.idTipoEspecieFK = $tipo";
if ($estado !== '') $filtros[] = "peces.idEstadoConservacionFK = $estado";
if ($habitat !== '') $filtros[] = "peces.idHabitatFK = $habitat";
$where = $filtros ? "WHERE " . implode(" AND ", $filtros) : '';

// Consulta SQL para obtener los peces y sus relaciones con hábitat, estado y tipo
$sql = "SELECT peces.idPeces, peces.nombreComun, peces.nombreCientifico, peces.caracteristicas, peces.imagen,
        habitat.tipo AS habitat, estado_conservacion.estado AS estado, tipo_especie.tipo AS tipo
        FROM peces
        INNER JOIN habitat ON peces.idHabitatFK = habitat.idHabitat
        INNER JOIN estado_conservacion ON peces.idEstadoConservacionFK = estado_conservacion.idEstadoConservacion
        INNER JOIN tipo_especie ON peces.idTipoEspecieFK = tipo_especie.idTipoEspecie
        $where";
$resultado = $conexion->query($sql);

// Consulta para contar el número total de peces registrados
$cuentaTotal = $conexion->query("SELECT COUNT(*) AS total FROM peces")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Enciclopedia de Peces</title>
    <!-- Carga Bootstrap para estilos responsivos y modernos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e3f2fd; }
        header {
            background-color: #0d6efd;
            color: white;
            padding: 2rem 1rem 1rem;
            text-align: center;
            position: relative;
        }
        header h1 { font-size: 2.5rem; margin: 0; }
        header .autores { font-size: 1rem; margin-top: 0.5rem; }
        .form-select, .btn { margin-bottom: 10px; }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            height: 100%;
        }
        .card-img {
            width: 100%;
            max-width: 300px;
            height: 200px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }
        .total {
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .row > .col-md-4 {
            margin-bottom: 20px;
            display: flex;
        }
        .card-body-custom {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
        }
        .login-button {
            position: absolute;
            top: 10px;
            right: 20px;
        }
    </style>
</head>
<body>
<header>
    <h1>Enciclopedia de Peces de España</h1>
    <div class="autores">
        <div>Andrés Barba Filiberto</div>
        <div>Marco Silva Cotán</div>
    </div>
    <!-- Botón de inicio/cierre de sesión según el estado de la sesión -->
    <div class="login-button">
        <?php if (isset($_SESSION['usuario'])): ?>
            Hola, <?= ucfirst($_SESSION['usuario']) ?> <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline-light btn-sm">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</header>
<div class="container mt-4">
    <!-- Formulario de filtros para seleccionar tipo, estado y hábitat -->
    <form method="get" class="row justify-content-center">
        <div class="col-md-3">
            <select name="tipo" class="form-select">
                <option value="">-- Tipo de especie --</option>
                <?php
                // Opciones dinámicas para el filtro de tipo de especie
                $tipos = $conexion->query("SELECT * FROM tipo_especie");
                while ($t = $tipos->fetch_assoc()) {
                    $sel = $tipo == $t['idTipoEspecie'] ? 'selected' : '';
                    echo "<option value='{$t['idTipoEspecie']}' $sel>{$t['tipo']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="estado" class="form-select">
                <option value="">-- Estado conservación --</option>
                <?php
                // Opciones dinámicas para el filtro de estado de conservación
                $estados = $conexion->query("SELECT * FROM estado_conservacion");
                while ($e = $estados->fetch_assoc()) {
                    $sel = $estado == $e['idEstadoConservacion'] ? 'selected' : '';
                    echo "<option value='{$e['idEstadoConservacion']}' $sel>{$e['estado']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="habitat" class="form-select">
                <option value="">-- Hábitat --</option>
                <?php
                // Opciones dinámicas para el filtro de hábitat
                $habitats = $conexion->query("SELECT * FROM habitat");
                while ($h = $habitats->fetch_assoc()) {
                    $sel = $habitat == $h['idHabitat'] ? 'selected' : '';
                    echo "<option value='{$h['idHabitat']}' $sel>{$h['tipo']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
        <!-- Botón para insertar nuevos peces, solo visible para usuarios logueados -->
        <?php if (isset($_SESSION['usuario'])): ?>
        <div class="col-md-1 d-flex align-items-end">
            <a href="insertar.php" class="btn btn-success w-100">Insertar</a>
        </div>
        <?php endif; ?>
    </form>
    <!-- Muestra el número total de peces registrados en la enciclopedia -->
    <div class="total">Número total de peces en la enciclopedia: <?= $cuentaTotal ?></div>

    <div class="row">
        <!-- Muestra las tarjetas de peces obtenidas de la base de datos -->
        <?php while($fila = $resultado->fetch_assoc()): ?>
        <div class="col-md-4 d-flex">
            <div class="card w-100 d-flex flex-column">
                <img src="img/peces/<?= $fila['imagen'] ?>" alt="<?= $fila['nombreComun'] ?>" class="card-img">
                <div class="card-body-custom">
                    <h5 class="mt-2"><?= $fila['nombreComun'] ?></h5>
                    <p><strong>Científico:</strong> <?= $fila['nombreCientifico'] ?></p>
                    <p><strong>Características:</strong> <?= $fila['caracteristicas'] ?></p>
                    <p><strong>Hábitat:</strong> <?= $fila['habitat'] ?></p>
                    <p><strong>Estado de conservación:</strong> <?= $fila['estado'] ?></p>
                    <p><strong>Tipo de especie:</strong> <?= $fila['tipo'] ?></p>
                </div>
                <!-- Botones de edición y eliminación, solo para usuarios logueados -->
                <?php if (isset($_SESSION['usuario'])): ?>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <a href="editar.php?id=<?= $fila['idPeces'] ?>" class="btn btn-warning">Editar</a>
                    <a href="eliminar.php?id=<?= $fila['idPeces'] ?>" class="btn btn-danger">Eliminar</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>