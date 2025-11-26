<?php
//Api usada para la creacion de pacientes, llenando el campo id_cuenta gracias a la busqueda por correo obtenido desde android
//TENGO QUE ENVIAR EL correo del usuario que ingreso
//Cadena JSON esperada:
//{"nombre_cel":"Oscar", "ap_cel":"Vargas", "am_cel":"Garcia", "fn_cel":"1997/04/08", "s_cel":"M",
//"t_cel":"4941003247", "email_cel":"vargasgarciaoscar947@gmail.com", "ts_cel":"Privado", "cen_cel":"Juan", "cet_Cel":"4949423204"}
include_once('../database/conexion_bd_clinica.php');
include_once('../database/conexion_bd_user_clinica.php');

$con = ConexionBDClinica::getInstancia();
$con2 = ConexionBDUserClinica::getInstancia();

$conexion = $con->getConexion();
$conexion2 = $con2->getConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {

        echo "No hay cadena JSON";

    } else {

        $datos_pacientes = json_decode($cadenaJSON, true);

        //TENGO QUE ENVIAR EL correo del usuario que ingreso
        //consulta de id en la tabla de usuarios para guardar
        $email_api = $datos_pacientes['email_cel']; // correo enviado en el JSON

        $stmt = $conexion2->prepare("SELECT Id_Cuenta FROM cuentas WHERE Correo = ?");
        $stmt->bind_param("s", $email_api);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_cuenta_api = $row['Id_Cuenta'];
        } else {
            echo json_encode(["ERROR" => "Cuenta no encontrada"]);
            exit;
        }

        $n_api = $datos_pacientes['nombre_cel'];
        $ap_api = $datos_pacientes['ap_cel'];
        $am_api = $datos_pacientes['am_cel'];
        $fn_api = $datos_pacientes['fn_cel'];
        $s_api = $datos_pacientes['s_cel'];
        $t_api = $datos_pacientes['t_cel'];
        $ts_api = $datos_pacientes['ts_cel'];
        $cen_api = $datos_pacientes['cen_cel'];
        $cet_api = $datos_pacientes['cet_Cel'];
        //$id_cuenta_api = $datos_pacientes['id_cuenta_cel'];

        $stmt = $conexion->prepare("INSERT INTO pacientes 
    (Nombre, Apellido_Paterno, Apellido_Materno, Fecha_Nac, Sexo, Telefono, Email, Tipo_Seguro, Contacto_Emergencia_Nombre, Contacto_Emergencia_Telefono, Id_Cuenta) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "ssssssssssi",
            $n_api,
            $ap_api,
            $am_api,
            $fn_api,
            $s_api,
            $t_api,
            $email_api,
            $ts_api,
            $cen_api,
            $cet_api,
            $id_cuenta_api
        );

        if ($stmt->execute()) {
            echo json_encode(["ALTA_PACIENTE" => true]);
        } else {
            echo json_encode(["ALTA_PACIENTE" => false, "error" => $stmt->error]);
        }
    }
}
