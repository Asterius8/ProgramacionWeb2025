<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Paciente - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/landing_paciente.css">
</head>
<body>
    <?php 

    require_once('navbar_paciente.php');

    $pacienteDAO = new pacienteDAO();

    $datos = $pacienteDAO->consultarPaciente($_SESSION['email']);

    $paciente = mysqli_fetch_assoc($datos);

    ?>
    <!-- Contenido principal -->
    <div class="main-content">
        <div class="welcome-message">
            <h1>Bienvenido, <?php echo $paciente['Nombre'] . " " . $paciente['Apellido_Paterno']; ?></h1>
            <p>Estamos encantados de tenerte en tu panel de paciente. Desde aquí puedes gestionar toda tu información médica de forma sencilla y segura.</p>
        </div>

        <div class="instruction-box">
            <h2>¿Qué te gustaría hacer?</h2>
            <p>Selecciona alguna de las opciones que se observan en el menú de arriba:</p>
            
            <ul class="options-list">
                <li><i class="fas fa-user-edit"></i> <strong>Editar Perfil:</strong> Actualiza tu información personal y preferencias de contacto</li>
                <li><i class="fas fa-calendar-plus"></i> <strong>Crear Cita:</strong> Solicita una nueva cita con nuestros especialistas</li>
                <li><i class="fas fa-calendar-alt"></i> <strong>Administrar Citas:</strong> Consulta, modifica o cancela tus citas programadas</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="../../frontend/icono_clinicas.png" alt="Logo Clínica del Bienestar">
                </div>
                <div class="footer-text">
                    <p>Clínica del Bienestar - Cuidamos de tu salud integral</p>
                    <p>© 2023 Todos los derechos reservados</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>