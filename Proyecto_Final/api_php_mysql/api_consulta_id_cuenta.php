<?php
include_once('../database/conexion_bd_user_clinica.php');

$con = ConexionBDUserClinica::getInstancia();

$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {

        echo "No hay cadena JSON";
    } else {

        $datos = json_decode($cadenaJSON, true);
        $correo = $datos['email_cell'];


        $sql = "SELECT Id_Cuenta FROM cuentas WHERE Correo = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $respuesta = [];

        if ($fila = $resultado->fetch_assoc()) {

            $respuesta["EXISTE"] = true;
            $respuesta["ID_CUENTA"] = $fila["Id_Cuenta"];
        } else {

            $respuesta["EXISTE"] = false;
            $respuesta["ID_CUENTA"] = null;
        }

        $respuestaJSON = json_encode($respuesta);

        echo $respuestaJSON;
    }
}
