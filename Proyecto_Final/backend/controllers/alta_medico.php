<?php

include_once('facade.php');

$medicoDAO = new medicoDAO();

$datos_correctos = false;
$errores = [];

$n_php = $_POST['nombre'];
$ap_php = $_POST['apellido_paterno'];
$am_php = $_POST['apellido_materno'];
$e_php = $_POST['especialidad'];

// Validaciones
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

if (empty($e_php)) {
    $errores[] = "La especialidad es obligatoria.";
} elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/", $e_php)) {
    $errores[] = "La especialidad solo puede contener letras.";
}

if (empty($errores)) {
    $datos_correctos = true;
}

if ($datos_correctos) {

    $res = $medicoDAO->agregarMedico($n_php,$ap_php,$am_php,$e_php);

    if($res){

        $_SESSION['medico_alta'] = true;
        echo"Exito";

    }else{
        echo"Fracaso en nivel BD";
    }

}else{

    echo"Fracaso, datos mal escritos";

}


?>