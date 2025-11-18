<?php 

session_start();

include_once('facade.php');

$pacienteDAO = new pacienteDAO();

$datos_correctos = false;

$n_php = $_POST['nombre'];
$ap_php = $_POST['primer_apellido'];
$am_php = $_POST['segundo_apellido'];
$f_php = $_POST['fecha_nac'];
$s_php = $_POST['sexo'];
$t_php = $_POST['telefono'];
$e_php = $_SESSION['email'];
$ts_php = $_POST['tipo_seguro'];
$cen_php = $_POST['contacto_emergencia'];
$cet_php = $_POST['telefono_emergencia'];
$ic_php = $_SESSION['idCuenta'];

$datos_correctos = true;



if ($datos_correctos) {

    $res = $pacienteDAO->agregarPaciente($n_php, $ap_php, $am_php, $f_php, $s_php, $t_php, $e_php, $ts_php, $cen_php, $cet_php, $ic_php);

    if($res){

        echo "exito";

    }else{

        echo "fracaso"; 

    }

        

}

?>