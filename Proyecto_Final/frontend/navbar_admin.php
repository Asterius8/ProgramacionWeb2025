<?php

require_once(__DIR__ . '/../backend/controllers/auth.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/navbar_admin.css">
</head>
<body>
    
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <img src="../../frontend/icono_clinicas.png" alt="Logo Clínica del Bienestar">
                </div>
                <ul class="nav-links">
                    <li><a href="landing_admin.php"><i class="fas fa-user-cog"></i> Panel Principal</a></li>
                    <li><a href="agregar_medico.php"><i class="fas fa-user-md"></i> Agregar Médico</a></li>
                    <li><a href="administrar-medicos.html"><i class="fas fa-users"></i> Administrar Médicos</a></li>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> Administrar Citas</a></li>
                    <li class="user-info">
                        <i class="fas fa-user-shield"></i>
                        <span>Administrador</span>
                    </li>
                    <li><a href="/backend/controllers/logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>