<?php
//Api para la consulta de citas del paciente en la BD y usada para traer los datos de las citas
include_once('../database/conexion_bd_clinica.php');

$con = ConexionBDClinica::getInstancia();
$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {

        echo json_encode(["error" => "No hay cadena JSON"]);

    } else {

        $datos_cita = json_decode($cadenaJSON, true);

        $id_paciente = $datos_cita['id_paciente'];

        $stmt = $conexion->prepare("SELECT Id_Citas, Fecha, Hora, Medicos_Id_Medicos, Especialidad_Nombre 
                                    FROM citas 
                                    WHERE Pacientes_Id_Pacientes = ?");

        $stmt->bind_param("i", $id_paciente);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $citas = [];
            while ($row = $result->fetch_assoc()) {
                $citas[] = [
                    "Id_Citas" => $row['Id_Citas'],
                    "Fecha" => $row['Fecha'],
                    "Hora" => $row['Hora'],
                    "Medicos_Id_Medicos" => $row['Medicos_Id_Medicos'],
                    "Especialidad_Nombre" => $row['Especialidad_Nombre']
                ];
            }
            echo json_encode(["hayCitas" => true, "lista" => $citas]);
        } else {
            echo json_encode(["hayCitas" => false]);
        }
    }
}
?>
