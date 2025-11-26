<?php
//Api para la consulta total del paciente, usada para traer los datos del paciente a editar
//TENGO QUE ENVIAR EL correo del usuario que ingreso
include_once('../database/conexion_bd_clinica.php');
$con = ConexionBDClinica::getInstancia();

$conexion = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {

        echo "No hay cadena JSON";
        
    } else {

        $datos_pacientes = json_decode($cadenaJSON, true);

        $email_api = $datos_pacientes['email_cel']; // correo enviado en el JSON desde Android

        $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE Email = ?");
        $stmt->bind_param("s", $email_api);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode($row);  // aquí se muestra en Postman
        } else {
            echo json_encode(["error" => "Paciente no encontrado"]);
        }
    }
}
