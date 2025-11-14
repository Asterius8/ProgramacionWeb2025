<?php

    include("conexion_bd_clinica.php");//Nos conectamos al archivo de conexion

    $cn = ConexionBDClinica::getInstancia()->getConexion();

    var_dump($cn);

    ?>