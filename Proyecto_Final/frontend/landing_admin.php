<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/navbar_admin.css">
<body>
    <!-- Header -->
    <?php
    
    require_once('navbar_admin.php');
    
    ?>
    
    <!-- Main Content -->
    <div class="container">
        <div class="main-content">
            <!-- Welcome Section -->
            <section class="welcome-section">
                <h1>Panel de Administración - Gestión de Médicos</h1>
                <p>Desde aquí puedes gestionar toda la información relacionada con los médicos de la clínica.</p>
                <p>Selecciona una de las opciones disponibles en el menú superior o en las tarjetas de abajo para comenzar.</p>
            </section>

            <!-- Admin Actions -->
            <div class="admin-actions">
                <div class="action-card">
                    <h3><i class="fas fa-user-plus"></i> Agregar Médico</h3>
                    <p>Registra un nuevo médico en el sistema con toda su información profesional, especialidad y datos de contacto.</p>
                    <a href="agregar_medico.php" class="btn btn-primary">Agregar Nuevo Médico</a>
                </div>
                <div class="action-card">
                    <h3><i class="fas fa-users-cog"></i> Administrar Médicos</h3>
                    <p>Gestiona la información de los médicos existentes, edita sus datos, horarios o elimina registros cuando sea necesario.</p>
                    <a href="consultar_medico.php" class="btn btn-outline">Ver y Gestionar Médicos</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Clínica del Bienestar</h3>
                    <p>Sistema de administración médica. Gestiona eficientemente los recursos de tu clínica.</p>
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <ul class="footer-links">
                        <li>📍 Av. Principal 123, Ciudad</li>
                        <li>📞 (123) 456-7890</li>
                        <li>✉️ admin@clinicabienestar.com</li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Enlaces Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="#">Panel Principal</a></li>
                        <li><a href="#">Reportes</a></li>
                        <li><a href="#">Configuración</a></li>
                        <li><a href="#">Ayuda</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 Clínica del Bienestar. Sistema de Administración.</p>
            </div>
        </div>
    </footer>
</body>
</html>