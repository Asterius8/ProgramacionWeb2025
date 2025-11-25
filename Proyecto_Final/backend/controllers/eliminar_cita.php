<?php

include_once('../../backend/controllers/facade.php');

if (!isset($_GET['id'])) {
    header("Location: ../../frontend/consulta_cita_admin.php");
    exit;
}

$id = $_GET['id'];

$citaDAO = new citaDAO();

$result = $citaDAO->eliminarCita($id);

if ($result) {
    header("Location: ../../frontend/consulta_cita_admin.php?delete=ok");
    exit;
} else {
    header("Location: ../../frontend/consulta_cita_admin.php?delete=error");
    exit;
}

?>