<?php
// API para dar de alta una cita usando el SP CrearCita
include_once('../database/conexion_bd_clinica.php');

$con = ConexionBDClinica::getInstancia();
$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {

        echo json_encode(["error" => "No hay cadena JSON"]);

    } else {

        $datos_cita = json_decode($cadenaJSON, true);

        $fecha_api        = $datos_cita['fecha'];
        $hora_api         = $datos_cita['hora'];
        $id_paciente_api  = $datos_cita['id_paciente'];
        $id_medico_api    = $datos_cita['id_medico'];
        $especialidad_api = $datos_cita['especialidad'];

        // LLAMAR AL PROCEDIMIENTO ALMACENADO
        $stmt = $conexion->prepare("CALL CrearCita(?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $fecha_api, $hora_api, $id_paciente_api, $id_medico_api, $especialidad_api);

        try {

            $stmt->execute();

            echo json_encode(["success" => true]);

        } catch (mysqli_sql_exception $e) {

            $code = $e->getCode();
            $msg  = $e->getMessage();

            if ($code == 1062) {
                echo json_encode([
                    "success" => false,
                    "type" => "unique_violation",
                    "message" => "Cita duplicada."
                ]);
                exit;
            }

            if ($code == 1644) {
                echo json_encode([
                    "success" => false,
                    "type" => "procedure_validation",
                    "message" => $msg
                ]);
                exit;
            }

            echo json_encode(["success" => false, "error" => $msg]);
        }
    }
}
?>
