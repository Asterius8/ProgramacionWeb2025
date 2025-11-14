<?php 

    include_once('facade.php');

    $usuarioDAO = new usuarioDAO();

    $email_php = $_POST['caja_email'];
    $password_php = $_POST['caja_password'];

    //VALIDACION!!!
    $datos_correctos = true;


    session_start();

    if ($datos_correctos) {

        $res = $usuarioDAO->agregarUsuario($email_php, $password_php);

        if ($res){

            echo "Correcta";

        }else{


            echo "Mejor me dedico a las redes";
    
            
        }

    }
?>