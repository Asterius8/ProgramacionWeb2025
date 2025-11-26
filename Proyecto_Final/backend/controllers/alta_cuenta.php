<?php

session_start();

// 1. Validar CAPTCHA
if (!isset($_POST['g-recaptcha-response'])) {
    $_SESSION['error_crear'] = true;
    $_SESSION['errores_lista'][] = "Debes completar el captcha.";
    header("Location: ../../frontend/crear_cuenta.php");
    exit;
}

$captcha = $_POST['g-recaptcha-response'];
$secretKey = "6LfwqhgsAAAAADjPYISyLIzfLztQGQtsPKDO6yzT";

$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captcha}");
$response = json_decode($verify);

if (!$response->success) {
    $_SESSION['error_crear'] = true;
    $_SESSION['errores_lista'][] = "Captcha inválido. Inténtalo de nuevo.";
    header("Location: ../../frontend/crear_cuenta.php");
    exit;
}

// --- SI EL CAPTCHA ES CORRECTO, SEGUIR CON TU LÓGICA NORMAL ---

include_once('facade.php');

$usuarioDAO = new usuarioDAO();

$datos_correctos = false;

$email_php = $_POST['caja_email'];
$password_php = $_POST['caja_password'];


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

// Validar que no exista en BD
if ($usuarioDAO->existeCorreo($email_php)) {
    $errores[] = "Ya existe una cuenta registrada con este correo.";
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

session_start();

if ($datos_correctos) {

    $res = $usuarioDAO->agregarUsuario($email_php, $password_php);

    if ($res) {

        unset($_SESSION['email']);  // ← BORRAR correo guardado

        $_SESSION['cuenta_creada'] = true;   // <--- mensaje de éxito

        $idCuenta = $usuarioDAO->obtenerIdCuentaPorEmail($email_php);
        $_SESSION['idCuenta'] = $idCuenta;

        $_SESSION['email'] = $email_php;

        header("Location: ../../frontend/crear_cuenta.php");
    } else {
        $_SESSION['error_crear'] = true;    // <--- mensaje de error
        header("Location: ../../frontend/crear_cuenta.php");
    }
} else {

    $_SESSION['error_crear'] = true;
    $_SESSION['errores_lista'] = $errores;   // <--- AQUÍ guardamos los errores

    $_SESSION['email'] = $email_php;

    header('location:../../frontend/crear_cuenta.php');
}
