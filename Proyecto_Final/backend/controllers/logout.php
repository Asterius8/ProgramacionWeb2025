<?php
session_start();

// Destruir completamente la sesión
session_unset();
session_destroy();

// Eliminar la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Headers para evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirigir al login
header("Location: ../../frontend/login.php?logout=1");
exit();
?>