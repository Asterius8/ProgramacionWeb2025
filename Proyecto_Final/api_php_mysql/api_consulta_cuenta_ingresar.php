<?php
//API usada para el ingreso del usuario y contraseña
include_once('../database/conexion_bd_user_clinica.php');

$con = ConexionBDUserClinica::getInstancia();

$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {

        echo json_encode(["error" => "No hay cadena JSON"]);
        
    } else {

        $datos_cuentas = json_decode($cadenaJSON, true);

        $email_api = $datos_cuentas['email_cell'];
        $password_api = $datos_cuentas['password_cell'];

        // Modificado: Ahora también se consulta el Rol
        $stmt = $conexion->prepare("SELECT Password, Rol FROM cuentas WHERE Correo = ?");
        $stmt->bind_param("s", $email_api);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hash_bd = $row['Password'];
            $rol_usuario = $row['Rol'];

            if (password_verify($password_api, $hash_bd)) {
                // Contraseña correcta - devolver LOGIN y ROL
                echo json_encode([
                    "LOGIN" => true, 
                    "ROL" => $rol_usuario
                ]);
            } else {
                // Contraseña incorrecta
                echo json_encode([
                    "LOGIN" => false, 
                    "mensaje" => "Contraseña invalida"
                ]);
            }

        } else {
            // No existe el correo
            echo json_encode([
                "LOGIN" => false, 
                "mensaje" => "Correo no encontrado"
            ]);
        }
    }
}
?>