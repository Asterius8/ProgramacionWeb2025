<?php

include_once('facade.php');

$citaDao = new citaDAO();

$datos_correctos = false;
$errores = [];

$f_php = $_POST['fecha'];
$h_php = $_POST['hora'];
$im_php = $_POST['medico'];

?>