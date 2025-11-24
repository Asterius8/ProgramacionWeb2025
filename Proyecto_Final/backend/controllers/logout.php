<?php
session_start();

// Destruir sesión completamente
session_unset();
session_destroy();

// Redirigir al login
header("Location: login.php?logout=ok");
exit;
?>
