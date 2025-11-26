<?php
//Actualiza cierta informacion del cliente mediante su correo
include_once('../database/conexion_bd_clinica.php');

$con = ConexionBDClinica::getInstancia();
$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {

        echo json_encode(["error" => "No hay cadena JSON"]);

    } else {

        $datos_pacientes = json_decode($cadenaJSON, true);

        // Datos recibidos
        $email_api = $datos_pacientes['email_cel']; // correo para identificar al paciente
        $ts_api    = $datos_pacientes['ts_cel'];
        $cen_api   = $datos_pacientes['cen_cel'];
        $cet_api   = $datos_pacientes['cet_Cel'];

        // Sentencia UPDATE
        $stmt = $conexion->prepare("UPDATE pacientes 
            SET Tipo_Seguro = ?, 
                Contacto_Emergencia_Nombre = ?, 
                Contacto_Emergencia_Telefono = ? 
            WHERE Email = ?");

        $stmt->bind_param("ssss", $ts_api, $cen_api, $cet_api, $email_api);

        if ($stmt->execute()) {
            echo json_encode(["ACTUALIZAR_PACIENTE" => true]);
        } else {
            echo json_encode(["ACTUALIZAR_PACIENTE" => false, "error" => $stmt->error]);
        }
    }
}
?>
