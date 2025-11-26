<?php
//API usada para la creacion de cuenta de usuario y contraseña
include_once('../database/conexion_bd_user_clinica.php');

$con = ConexionBDUserClinica::getInstancia();

$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {

        echo "No hay cadena JSON";

    } else {

        $datos_cuentas = json_decode($cadenaJSON, true);

        $email_api = $datos_cuentas['email_cell'];
        $password_api = $datos_cuentas['password_cell'];

        $hash = password_hash($password_api, PASSWORD_DEFAULT);

        $sql = "INSERT INTO cuentas (Correo, Password, Rol) VALUES ('$email_api', '$hash', 'Paciente')";

        $res = mysqli_query($conexion, $sql);

        $respuesta = array();

        if ($res) {

            $respuesta['ALTA_CUENTA'] = true;
        } else {

            $respuesta['ALTA_CUENTA'] = false;
        }

        $respuestaJSON = json_encode($respuesta);

        echo $respuestaJSON;
    }
}
