<?php 

include_once('facade.php');
session_start();

$usuarioDAO = new usuarioDAO();

$email_php = $_POST['caja_email'];
$password_php = $_POST['caja_password'];

$datos_correctos = true;

if ($datos_correctos) {

    $res = $usuarioDAO->agregarUsuario($email_php, $password_php);

    if ($res){
        $_SESSION['cuenta_creada'] = true;   // <--- mensaje de éxito
        header("Location: ../../frontend/crear_cuenta.php");
        exit;
    } else {
        $_SESSION['error_crear'] = true;    // <--- mensaje de error
        header("Location: ../../frontend/crear_cuenta.php");
        exit;
    }

}

?>
