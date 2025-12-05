<?php
//API usada para el ingreso del usuario y contraseña
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

        $stmt = $conexion->prepare("SELECT Password FROM cuentas WHERE Correo = ?");
        $stmt->bind_param("s", $email_api);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hash_bd = $row['Password'];

            if (password_verify($password_api, $hash_bd)) {
                // Contraseña correcta
                echo json_encode(["LOGIN" => true]);
            } else {
                // Contraseña incorrecta
                echo json_encode(["LOGIN" => false, "mensaje" => "Contraseña invalida"]);
            }

        } else {
            // No existe el correo
            echo json_encode(["LOGIN" => false, "mensaje" => "Correo no encontrado"]);
        }
    }
}
