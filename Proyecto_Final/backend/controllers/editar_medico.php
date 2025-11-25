<?php

session_start();
include_once('facade.php');

$medicoDAO = new medicoDAO();

$datos_correctos = false;
$errores = [];


$id = $_POST['id'];
$nombre = $_POST['nombre'];
$ap = $_POST['apellido_paterno'];
$am = $_POST['apellido_materno'];
$especialidad = $_POST['especialidad'];

// Validaciones
if (empty($nombre)) {
    $errores[] = "El nombre es obligatorio.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $nombre)) {
    $errores[] = "El nombre solo puede contener letras y espacios.";
}

if (empty($ap)) {
    $errores[] = "El primer apellido es obligatorio.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $ap)) {
    $errores[] = "El primer apellido solo puede contener letras.";
}

if (empty($am)) {
    $errores[] = "El segundo apellido también es obligatorio.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $am)) {
    $errores[] = "El segundo apellido solo puede contener letras.";
}

if ($especialidad === "") {
    $errores[] = "Debe seleccionar una especialidad válida.";
}
// Si no hay errores
if (empty($errores)) {

    $res = $medicoDAO->editarMedico( $id, $nombre , $ap, $am, $especialidad);

    if ($res) {
        $_SESSION['medico_edit'] = true;
        header("Location: ../../frontend/consultar_medico.php");
        exit();
    } else {
        $_SESSION['medico_edit_error'] = true;
        echo 'Cai';
        header("Location: ../../frontend/consultar_medico.php");
        exit();
    }
} else {

    $_SESSION['medico_edit_error'] = true;
    $_SESSION['errores_lista'] = $errores;
echo 'datos';
    header("Location: ../../frontend/consultar_medico.php");
    exit();
}
?>
