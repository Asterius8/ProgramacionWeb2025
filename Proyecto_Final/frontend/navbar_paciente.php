<?php

require_once(__DIR__ . '/../backend/controllers/auth.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/landing_paciente.css">
    <title>Document</title>
</head>

<body>
    <?php
    require_once('navbar_paciente.php');

    include('../backend/controllers/facade.php');

    $pacienteDAO = new pacienteDAO();

    $datos = $pacienteDAO->consultarPaciente($_SESSION['email']);
    $datosCompletos = $pacienteDAO->consultarIdPaciente($_SESSION['email']);

    $paciente = mysqli_fetch_assoc($datos);
    $pacienteCompleto = mysqli_fetch_assoc($datosCompletos);

    $_SESSION['Id_p'] = $pacienteCompleto['Id_Pacientes'];

    $nombre_completo = $paciente['Nombre'] . " " . $paciente['Apellido_Paterno'] . " " . $paciente['Apellido_Materno'];

    $_SESSION['ncp'] = $nombre_completo;
    ?>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <img src="../../frontend/icono_clinicas.png" alt="Logo Clínica del Bienestar">
                </div>
                <ul class="nav-links">
                    <li><a href="edit_paciente.php"><i class="fas fa-user-edit"></i> Editar Perfil</a></li>
                    <li><a href="agregar_cita.php"><i class="fas fa-calendar-plus"></i> Crear Cita</a></li>
                    <li><a href="consulta_cita.php"><i class="fas fa-calendar-alt"></i> Ver Citas</a></li>
                    <li class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo $paciente['Nombre'] . " " . $paciente['Apellido_Paterno']; ?></span>
                    </li>
                    <li><a href="/backend/controllers/logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>
</body>

</html>