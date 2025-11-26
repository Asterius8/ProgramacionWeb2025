<?php
//API usada al momento de crear usuarios y ver que ese correo no sea uno existente
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

        $sql = "SELECT 1 FROM cuentas WHERE Correo = ? LIMIT 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email_api);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Ya existe la cuenta
            echo json_encode(["EXISTE_CUENTA" => true]);
        } else {
            // No existe, puedes crearla
            echo json_encode(["EXISTE_CUENTA" => false]);
        }
    }
}
