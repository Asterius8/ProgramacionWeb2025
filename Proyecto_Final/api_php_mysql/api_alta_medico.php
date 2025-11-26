<?php
//API para dar de alta una cita
//IMPORTANTE SE DEBE MANDAR DESDE ANDROID ALGO ESTILO: NOMBRE APELLIDO APELLIDO - ESPECIALIDAD
include_once('../database/conexion_bd_clinica.php');

$con = ConexionBDClinica::getInstancia();
$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {

        echo "No hay cadena JSON";

    } else {

        $datos_cita = json_decode($cadenaJSON, true);

        $fecha_api        = $datos_cita['fecha'];
        $hora_api         = $datos_cita['hora'];
        $id_paciente_api  = $datos_cita['id_paciente'];
        $id_medico_api    = $datos_cita['id_medico'];     
        $especialidad_api = $datos_cita['especialidad'];  

        $stmt = $conexion->prepare("INSERT INTO citas 
        (Fecha, Hora, Pacientes_Id_Pacientes, Medicos_Id_Medicos, Especialidad_Nombre) 
        VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $fecha_api, $hora_api, $id_paciente_api, $id_medico_api, $especialidad_api);

        if ($stmt->execute()) {

            echo json_encode(["CITA_CREADA" => true]);

        } else {

            echo json_encode(["CITA_CREADA" => false, "error" => $stmt->error]);

        }
    }
}
