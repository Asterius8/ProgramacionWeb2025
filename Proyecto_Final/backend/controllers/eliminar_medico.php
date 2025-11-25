<?php
session_start();
require_once("facade.php");

if (!isset($_GET['id'])) {
    $_SESSION['medico_delete_error'] = "ID no recibido.";
    header("Location: ../../frontend/consultar_medico.php");
    exit();
}

$id = intval($_GET['id']);

$medicoDAO = new medicoDAO();

if ($medicoDAO->eliminarMedico($id)) {
    $_SESSION['medico_delete_ok'] = true;
} else {
    $_SESSION['medico_delete_error'] = "Error al eliminar médico.";
}
header("Location: ../../frontend/consultar_medico.php");
exit();
