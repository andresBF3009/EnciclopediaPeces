<?php
// Inicia la sesión para poder destruirla
session_start();

// Destruye toda la información de la sesión
session_destroy();

// Redirige al usuario a la página principal de peces
header("Location: peces1.php");
exit();
?>