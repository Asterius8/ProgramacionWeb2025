<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Médico - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/agregar_medico.css">
</head>
<body>

    <?php
    
    require_once('navbar_admin.php');
    
    ?>

    <div class="form-container">
        <div class="form-header">
            <h1>Alta de Nuevo Médico</h1>
            <p>Complete todos los campos para registrar un nuevo médico en el sistema</p>
        </div>

        <form id="doctor-form" action="../backend/controllers/alta_medico.php"  method="POST">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre del médico" required>
            </div>

            <div class="form-group">
                <label for="apellido_paterno">Apellido Paterno</label>
                <input type="text" id="apellido_paterno" name="apellido_paterno" placeholder="Ingrese el apellido paterno" required>
            </div>

            <div class="form-group">
                <label for="apellido_materno">Apellido Materno</label>
                <input type="text" id="apellido_materno" name="apellido_materno" placeholder="Ingrese el apellido materno" required>
            </div>

            <div class="form-group">
                <label for="especialidad">Especialidad</label>
                <input type="text" id="especialidad" name="especialidad" placeholder="Ingrese la especialidad del médico" required>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="window.location.href='admin-panel.html'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Médico</button>
            </div>
        </form>
    </div>
</body>
</html>