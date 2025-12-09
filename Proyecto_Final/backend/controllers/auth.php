<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Tiempo máximo de inactividad permitido (en segundos)
$tiempo_inactividad = 30 * 60; // 6 minutos

// Si existe "last_activity", comparar el tiempo
if (isset($_SESSION['last_activity'])) {

    // Si el tiempo actual - el último movimiento es mayor al tiempo permitido
    if (time() - $_SESSION['last_activity'] > $tiempo_inactividad) {

        // Destruir sesión y redirigir al login
        session_unset();
        session_destroy();

        header("Location: ../../frontend/login.php?timeout=1");
        exit();
    }
}

// Actualizar el tiempo de última actividad
$_SESSION['last_activity'] = time();

// Verificar que haya sesión iniciada
if (!isset($_SESSION['email'])) {
    header("Location: ../../frontend/login.php");
    exit();
}
?>
