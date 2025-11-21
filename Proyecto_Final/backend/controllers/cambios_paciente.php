<?php

session_start();
include_once('facade.php');

$pacienteDAO = new pacienteDAO();

$datos_correctos = false;
$errores = [];

//asignaciones de variables
$n_php = $_POST['nombre'];
$ap_php = $_POST['primer_apellido'];
$am_php = $_POST['segundo_apellido'];
$f_php = $_POST['fecha_nac'];
$s_php = $_POST['sexo'];
$t_php = $_POST['telefono'];
$ts_php = $_POST['tipo_seguro'];
$cen_php = $_POST['contacto_emergencia'];
$cet_php = $_POST['telefono_emergencia'];
$email_php = $_SESSION['email'];

//Validaciones PHP
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

if (empty($am_php)) {
    $errores[] = "El segundo apellido también es obligatorio.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $am_php)) {
    $errores[] = "El segundo apellido solo puede contener letras.";
}

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

$sexos_validos = ["M", "F", "O"];

if (empty($s_php)) {
    $errores[] = "El sexo es obligatorio.";
} elseif (!in_array($s_php, $sexos_validos)) {
    $errores[] = "El sexo seleccionado no es válido.";
}

if (empty($t_php)) {
    $errores[] = "El teléfono es obligatorio.";
} elseif (!preg_match("/^[0-9]{10}$/", $t_php)) {
    $errores[] = "El teléfono debe tener 10 dígitos.";
}

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

    $res = $pacienteDAO->cambiarPaciente($n_php, $ap_php, $am_php, $f_php, $s_php, $t_php, $ts_php, $cen_php, $cet_php, $email_php);

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

        $_SESSION['paciente_editado'] = true;   // <--- mensaje de éxito
        header("Location: ../../frontend/edit_paciente.php");

    }else{

        $_SESSION['error_modificar_paciente'] = true;    // <--- mensaje de error
        header("Location: ../../frontend/edit_paciente.php");
        
    }

}else{

    $_SESSION['error_modificar_paciente'] = true;
    $_SESSION['errores_lista_m'] = $errores;   // <--- AQUÍ guardamos los errores

    header('location:../../frontend/edit_paciente.php');

}
?>