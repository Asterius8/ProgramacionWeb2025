<?php
//API para eliminar una cita específica
include_once('../database/conexion_bd_clinica.php');

$con = ConexionBDClinica::getInstancia();
$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {
        echo json_encode(["error" => "No hay cadena JSON"]);
    } else {

        $datos = json_decode($cadenaJSON, true);

        $id_cita = $datos['id_cita'];

        // Eliminar la cita
        $stmt = $conexion->prepare("DELETE FROM citas WHERE Id_Citas = ?");
        $stmt->bind_param("i", $id_cita);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    "success" => true, 
                    "mensaje" => "Cita eliminada correctamente"
                ]);
            } else {
                echo json_encode([
                    "success" => false, 
                    "mensaje" => "No se encontró la cita"
                ]);
            }
        } else {
            echo json_encode([
                "success" => false, 
                "mensaje" => "Error al eliminar la cita"
            ]);
        }

        $stmt->close();
    }
}
?>