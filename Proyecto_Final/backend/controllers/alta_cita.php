<?php
session_start();

include_once('facade.php');

$citaDAO = new citaDAO();
$medicoDAO = new medicoDAO();

$datos_correctos = false;
$errores = [];

$f_php = $_POST['fecha'];
$h_php = $_POST['hora'];
$ip_php = $_SESSION['Id_p'];
$im_php = $_POST['medico'];

$datos = $medicoDAO->consultarMedicos($im_php);
$en_php = ""; // Aquí quedará el nombre completo con especialidad

if ($fila = mysqli_fetch_assoc($datos)) {

    // Crear cadena completa
    $en_php = $fila['Nombre'] . " " .
        $fila['Apellido_Paterno'] . " " .
        $fila['Apellido_Materno'] . " - " .
        $fila['Especialidad'];
}

// VALIDAR FECHA
if (empty($f_php)) {
    $errores[] = "La fecha es obligatoria.";
} else {
    $fecha_actual = date("Y-m-d");

    if ($f_php < $fecha_actual) {
        $errores[] = "La fecha no puede ser anterior a hoy.";
    }
}

// VALIDAR HORA
if (empty($h_php)) {
    $errores[] = "La hora es obligatoria.";
} else {
    // Rango permitido
    $hora_min = "08:00";
    $hora_max = "18:00";

    if ($h_php < $hora_min || $h_php > $hora_max) {
        $errores[] = "La hora debe estar entre 08:00 y 18:00.";
    }
}

// VALIDAR MÉDICO
if (empty($im_php)) {
    $errores[] = "Debe seleccionar un médico.";
}

if (empty($errores)) {
    $datos_correctos = true;
}

if ($datos_correctos) {

    $res = $citaDAO->agregarCita($f_php, $h_php, $ip_php, $im_php, $en_php);

    if ($res['success']) {

        $_SESSION['cita_creada'] = true;
        header("Location: ../../frontend/agregar_cita.php");
        exit;

    } else {

        // Manejo de errores según tipo
        switch ($res['type']) {
            case 'procedure_validation':
                $errores[] = $res['message']; // Por ejemplo: "El paciente ya alcanzó el límite de citas permitidas."
                break;
            case 'unique_violation':
                $errores[] = $res['message']; // Por ejemplo: "Cita duplicada: el paciente ya tiene una cita con ese médico a esa hora."
                break;
            default:
                $errores[] = "Ocurrió un error desconocido: " . $res['message'];
                break;
        }

        $_SESSION['error_crear_cita'] = true;
        $_SESSION['errores_lista'] = $errores;
        header("Location: ../../frontend/agregar_cita.php");
        exit;
    }
} else {

    $_SESSION['error_crear_cita'] = true;
    $_SESSION['errores_lista'] = $errores;
    header("Location: ../../frontend/agregar_cita.php");
}
