<?php
//Api para la consulta de medicos en la BD y usada para traer los datos del medico
include_once('../database/conexion_bd_clinica.php');
$con = ConexionBDClinica::getInstancia();
$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $stmt = $conexion->prepare("SELECT Id_Medicos, Nombre, Apellido_Paterno, Apellido_Materno, Especialidad FROM medicos");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $medicos = [];
        while ($row = $result->fetch_assoc()) {
            $medicos[] = [
                "Id_Medicos" => $row['Id_Medicos'],
                "Nombre" => $row['Nombre'],
                "Apellido_Paterno" => $row['Apellido_Paterno'],
                "Apellido_Materno" => $row['Apellido_Materno'],
                "Especialidad" => $row['Especialidad']
            ];
        }
        echo json_encode(["hayMedicos" => true, "lista" => $medicos]);
    } else {
        echo json_encode(["hayMedicos" => false]);
    }
}
