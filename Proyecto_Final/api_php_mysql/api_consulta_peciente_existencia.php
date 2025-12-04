<?php
// API para verificar si ya existe un paciente con los mismos datos
include_once('../database/conexion_bd_clinica.php');

$con = ConexionBDClinica::getInstancia();
$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if (!$cadenaJSON) {
        echo json_encode(["error" => "No hay cadena JSON"]);
        exit;
    }

    $datos_pacientes = json_decode($cadenaJSON, true);

    if (!$datos_pacientes) {
        echo json_encode(["error" => "JSON inválido"]);
        exit;
    }

    $nombre_api = $datos_pacientes["nombre_cel"] ?? '';
    $ap_api = $datos_pacientes["ap_cel"] ?? '';
    $am_api = $datos_pacientes["am_cel"] ?? '';
    $f_api = $datos_pacientes["fecha_cel"] ?? '';
    $s_api = $datos_pacientes["sexo_cel"] ?? '';
    $t_api = $datos_pacientes["telefono_cel"] ?? '';

    $sql = "SELECT 1 FROM pacientes WHERE 
            Nombre = ? AND 
            Apellido_Paterno = ? AND 
            Apellido_Materno = ? AND 
            Fecha_Nac = ? AND 
            Sexo = ? AND 
            Telefono = ? LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssss", $nombre_api, $ap_api, $am_api, $f_api, $s_api, $t_api);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["EXISTE_PACIENTE" => true]);
    } else {
        echo json_encode(["EXISTE_PACIENTE" => false]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Método no permitido, use POST"]);
}
?>
