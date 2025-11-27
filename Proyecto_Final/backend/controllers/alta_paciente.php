<?php

session_start();

include_once('facade.php');

$pacienteDAO = new pacienteDAO();

$datos_correctos = false;

$n_php = $_POST['nombre'];
$ap_php = $_POST['primer_apellido'];
$am_php = $_POST['segundo_apellido'];
$f_php = $_POST['fecha_nac'];
$s_php = $_POST['sexo'];
$t_php = $_POST['telefono'];
$e_php = $_SESSION['email'];
$ts_php = $_POST['tipo_seguro'];
$cen_php = $_POST['contacto_emergencia'];
$cet_php = $_POST['telefono_emergencia'];
$ic_php = $_SESSION['idCuenta'];

$errores = [];

// -----------------------
// CAMPOS DE TEXTO
// -----------------------

if (empty($n_php)) {
    $errores[] = "El nombre es obligatorio.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $n_php)) {
    $errores[] = "El nombre solo puede contener letras y espacios.";
}

if (empty($ap_php)) {
    $errores[] = "El primer apellido es obligatorio.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $ap_php)) {
    $errores[] = "El primer apellido solo puede contener letras.";
}

// Segundo apellido REQUERIDO
if (empty($am_php)) {
    $errores[] = "El segundo apellido también es obligatorio.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $am_php)) {
    $errores[] = "El segundo apellido solo puede contener letras.";
}

// -----------------------
// FECHA (NO TEXTO - DATE)
// -----------------------
if (empty($f_php)) {
    $errores[] = "La fecha de nacimiento es obligatoria.";
} elseif (!DateTime::createFromFormat("Y-m-d", $f_php)) {
    $errores[] = "La fecha de nacimiento no tiene un formato válido.";
} else {
    $fecha_nac = new DateTime($f_php);
    $hoy = new DateTime();

    if ($fecha_nac > $hoy) {
        $errores[] = "La fecha de nacimiento no puede ser futura.";
    }
}

// -----------------------
// SEXO (SELECT)
// -----------------------
$sexos_validos = ["M", "F", "O"]; // Masculino, Femenino, Otro

if (empty($s_php)) {
    $errores[] = "El sexo es obligatorio.";
} elseif (!in_array($s_php, $sexos_validos)) {
    $errores[] = "El sexo seleccionado no es válido.";
}

// -----------------------
// TELÉFONO
// -----------------------
if (empty($t_php)) {
    $errores[] = "El teléfono es obligatorio.";
} elseif (!preg_match("/^[0-9]{10}$/", $t_php)) {
    $errores[] = "El teléfono debe tener 10 dígitos.";
}

// -----------------------
// TIPO DE SEGURO (SELECT)
// -----------------------
$seguros_validos = ["Privado", "Aseguradora", "Gobierno", "Indigente", "Ninguno"];

if (empty($ts_php)) {
    $errores[] = "El tipo de seguro es obligatorio.";
} elseif (!in_array($ts_php, $seguros_validos)) {
    $errores[] = "El tipo de seguro seleccionado no es válido.";
}

// -----------------------
// CONTACTO DE EMERGENCIA
// -----------------------
if (empty($cen_php)) {
    $errores[] = "El contacto de emergencia es obligatorio.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $cen_php)) {
    $errores[] = "El contacto de emergencia solo puede contener letras.";
}

// Teléfono de emergencia
if (empty($cet_php)) {
    $errores[] = "El teléfono de emergencia es obligatorio.";
} elseif (!preg_match("/^[0-9]{10}$/", $cet_php)) {
    $errores[] = "El teléfono de emergencia debe tener 10 dígitos.";
}

if (empty($errores)) {
    $datos_correctos = true;
}

if ($datos_correctos) {

    if ($pacienteDAO->existePacienteDuplicado($n_php, $ap_php, $am_php, $f_php, $s_php, $t_php)) {

        $_SESSION['error_crear'] = true;
        $_SESSION['errores_lista'] = ["Ese paciente ya existe en la base de datos."];

        header('location:../../frontend/form_paciente.php');
        exit;
        
    } else {

        $res = $pacienteDAO->agregarPaciente($n_php, $ap_php, $am_php, $f_php, $s_php, $t_php, $e_php, $ts_php, $cen_php, $cet_php, $ic_php);

        if ($res) {

            unset($_SESSION['nombre']);
            unset($_SESSION['apellido_paterno']);
            unset($_SESSION['apellido_materno']);
            unset($_SESSION['fecha_nac']);
            unset($_SESSION['sexo']);
            unset($_SESSION['telefono']);
            unset($_SESSION['tipo_seguro']);
            unset($_SESSION['cen']);
            unset($_SESSION['cet']);

            $_SESSION['cuenta_creada'] = true;
            header("Location: ../../frontend/form_paciente.php");
        } else {
            $_SESSION['error_crear'] = true;
            header("Location: ../../frontend/form_paciente.php");
        }
    }
} else {

    $_SESSION['error_crear'] = true;
    $_SESSION['errores_lista'] = $errores;   // <--- AQUÍ guardamos los errores

    $_SESSION['nombre'] = $n_php;
    $_SESSION['apellido_paterno'] = $ap_php;
    $_SESSION['apellido_materno'] = $am_php;
    $_SESSION['fecha_nac'] = $f_php;
    $_SESSION['sexo'] = $s_php;
    $_SESSION['telefono'] = $t_php;
    $_SESSION['email'] = $e_php;
    $_SESSION['tipo_seguro'] = $ts_php;
    $_SESSION['cen'] = $cen_php;
    $_SESSION['cet'] = $cet_php;
    $_SESSION['id_cuente'] = $ic_php;

    header('location:../../frontend/form_paciente.php');
}
