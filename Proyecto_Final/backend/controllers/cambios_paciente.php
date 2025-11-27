<?php

session_start();
include_once('facade.php');

$pacienteDAO = new pacienteDAO();

$datos_correctos = false;
$errores = [];

//asignaciones de variables
$ts_php = $_POST['tipo_seguro'];
$cen_php = $_POST['contacto_emergencia'];
$cet_php = $_POST['telefono_emergencia'];
$email_php = $_SESSION['email'];

//Validaciones PHP
$seguros_validos = ["Privado", "Aseguradora", "Gobierno", "Indigente", "Ninguno"];

if (empty($ts_php)) {
    $errores[] = "El tipo de seguro es obligatorio.";
} elseif (!in_array($ts_php, $seguros_validos)) {
    $errores[] = "El tipo de seguro seleccionado no es válido.";
}

if (empty($cen_php)) {
    $errores[] = "El contacto de emergencia es obligatorio.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $cen_php)) {
    $errores[] = "El contacto de emergencia solo puede contener letras.";
}

if (empty($cet_php)) {
    $errores[] = "El teléfono de emergencia es obligatorio.";
} elseif (!preg_match("/^[0-9]{10}$/", $cet_php)) {
    $errores[] = "El teléfono de emergencia debe tener 10 dígitos.";
}

if (empty($errores)) {
    $datos_correctos = true;
}

if ($datos_correctos) {

    $res = $pacienteDAO->cambiarPaciente($ts_php, $cen_php, $cet_php, $email_php);

    if ($res) {

        unset($_SESSION['tipo_seguro']);
        unset($_SESSION['cen']);
        unset($_SESSION['cet']);

        $_SESSION['paciente_editado'] = true;   // <--- mensaje de éxito
        header("Location: ../../frontend/edit_paciente.php");
    } else {

        $_SESSION['error_modificar_paciente'] = true;    // <--- mensaje de error
        header("Location: ../../frontend/edit_paciente.php");
    }
} else {

    $_SESSION['error_modificar_paciente'] = true;
    $_SESSION['errores_lista_m'] = $errores;   // <--- AQUÍ guardamos los errores

    $_SESSION['tipo_seguro'] = $ts_php;
    $_SESSION['contacto_emergencia'] = $cen_php ;
    $_SESSION['telefono_emergencia'] = $cet_php;

    header('location:../../frontend/edit_paciente.php');
}
