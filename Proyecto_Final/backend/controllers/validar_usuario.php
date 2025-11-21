<?php

session_start();
include_once('facade.php');

$usuarioDAO = new usuarioDAO();


$datos_correctos = false;

// Recibir datos del formulario
$email_php = $_POST['email'];
$password_php = $_POST['password'];

$errores = [];

// Validaciones
if (empty($email_php)) {
    $errores[] = "El correo es obligatorio.";
    $datos_correctos = false;
}

if (!filter_var($email_php, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El correo no tiene un formato válido.";
    $datos_correctos = false;
}

if (empty($password_php)) {
    $errores[] = "La contraseña es obligatoria.";
    $datos_correctos = false;
}

if (strlen($password_php) < 6) {
    $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    $datos_correctos = false;
}

if (strlen($password_php) > 20) {
    $errores[] = "La contraseña no puede superar los 20 caracteres.";
    $datos_correctos = false;
}

if (preg_match('/\s/', $password_php)) {
    $errores[] = "La contraseña no puede tener espacios.";
    $datos_correctos = false;
}

// Si no hay errores enviamos al formulario
if (empty($errores)) {
    $datos_correctos = true;
}

if ($datos_correctos) {

    // Llamar al método de la fachada que obtiene correo + password hash
    $datosUsuario = $usuarioDAO->obtenerEmailPassword($email_php);

    // Si no se encontró el correo
    if ($datosUsuario === null) {
        $_SESSION['error_login'] = "El correo no está registrado.";
        header("Location: ../../frontend/login.php");
        exit;
    }

    // Validar contraseña encriptada
    if (!password_verify($password_php, (string)$datosUsuario['password'])) {

        $_SESSION['error_login'] = "La contraseña es incorrecta.";
        header("Location: ../../frontend/login.php");
        exit;
    }

    // Si llegamos aquí → Login correcto
    $_SESSION['email'] = $datosUsuario['correo'];
    $_SESSION['login_success'] = true;
    $_SESSION['logged'] = true;
    $_SESSION['ultimo_movimiento'] = time(); // Para expiración automática
    //Aqui tiene que ir el switch para comparar los 2 tipos de usuarios
    $rol = $datosUsuario['rol'];
    switch ($rol) {
        case 'Admin':
            header("Location: ../../frontend/landing_admin.php"); //CREAR ESTA PAGINA
            break;

        default:
            header("Location: ../../frontend/landing_paciente.php");
            break;
    }
} else {

    $_SESSION['error_crear'] = true;
    $_SESSION['errores_lista'] = $errores;   // <--- AQUÍ guardamos los errores
    header("Location: ../../frontend/login.php");
    $_SESSION['email'] = $email_php;
}
