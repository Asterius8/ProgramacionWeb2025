<?php

session_start();
include_once('facade.php');

$citaDAO = new citaDAO();
$pacienteDAO = new pacienteDAO();
$medicoDAO = new medicoDAO();

$datos_correctos = false;
$errores = [];

$p_php = $_POST['paciente'];
$f_php = $_POST['fecha'];
$h_php = $_POST['hora'];
$m_php = $_POST['medico'];
$idc_php = $_POST['id'];

//validaciones para hora sea de 8:00 y 18:00 y fecha sean dias despues de hoy,

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
if (empty($m_php)) {
    $errores[] = "Debe seleccionar un médico.";
}

// VALIDAR Pacietne
if (empty($p_php)) {
    $errores[] = "Debe seleccionar un médico.";
}

if (empty($errores)) {
    $datos_correctos = true;
}

//validacion de existencia medicos y existencia pacientes 
$resultadoP = $pacienteDAO->consultarPaciente('');
$resultadoM = $medicoDAO->consultarMedicos('');

//consulta de citas para obtener los id paciente y medico en base al id citas 
$datos = $citaDAO->consultarIdsCitas($idc_php);

$ids = [
    'paciente' => $datos['Pacientes_Id_Pacientes'],
    'medico'   => $datos['Medicos_Id_Medicos']
];

if ($resultadoP->num_rows > 1) {

    if ($resultadoM->num_rows > 1) {

        if ($datos_correctos) {

            $res = $citaDAO->editarCitas($idc_php, $h_php, $f_php, $ids['paciente'], $ids['medico'], $m_php);

            if ($res) {
                $_SESSION['cita_edit'] = true;
                echo "si cambia";
                //header("Location: ../../frontend/consultar_medico.php");
                //exit();
            } else {
                $_SESSION['cita_edit_error'] = true;
                echo "Error BD";
                //header("Location: ../../frontend/consultar_medico.php");
                //exit();
            }
        } else {

            $_SESSION['cita_edit_error'] = true;
            $_SESSION['errores_lista'] = $errores;

            header("Location: ../../frontend/consultar_medico.php");
            exit();
        }
    } else {
        $errores[] = "No se encotraron medicos";
        $_SESSION['cita_edit_error'] = true;
        $_SESSION['errores_lista'] = $errores;

        header("Location: ../../frontend/consultar_medico.php");
        exit();
    }
} else {
    $errores[] = "No se encotraron pacientes";
    $_SESSION['cita_edit_error'] = true;
    $_SESSION['errores_lista'] = $errores;

    header("Location: ../../frontend/consultar_medico.php");
    exit();
}
