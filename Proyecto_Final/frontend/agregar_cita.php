<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cita - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/agregar_cita.css">

</head>
<body>

    <?php

    require_once('navbar_paciente.php');

    //verificar que existan medicos en la BD

    ?>


    <div class="form-container">
        <div class="form-header">
            <h1>Crear Nueva Cita</h1>
            <p>Complete todos los campos para programar una nueva cita médica</p>
        </div>

        <form id="appointment-form" method="POST">
            <div class="form-group">
                <label for="fecha">Fecha de la Cita</label>
                <input type="date" id="fecha" name="fecha">
            </div>

            <div class="form-group">
                <label for="hora">Hora de la Cita</label>
                <input type="time" id="hora" name="hora" min="08:00" max="18:00">
            </div>

            <div class="form-group">
                <label for="medico">Médico y Especialidad</label>
                <select id="medico" name="medico">
                    <option value="">Seleccione un médico</option>
                    <option value="1">Dr. Carlos Rodríguez - Pediatra</option>
                    <option value="2">Dra. Ana Martínez - Pediatra</option>
                    <option value="3">Dr. Roberto Sánchez - Cirujano</option>
                    <option value="4">Dra. Laura García - Cirujano</option>
                    <option value="5">Dr. Miguel Ángel López - Internista</option>
                    <option value="6">Dra. Patricia Hernández - Internista</option>
                    <option value="7">Dr. Javier Ramírez - General</option>
                    <option value="8">Dra. Sofía Castro - General</option>
                    <option value="9">Dr. Eduardo Morales - Cardiología</option>
                    <option value="10">Dra. Carmen Ruiz - Dermatología</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="window.history.back()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Programar Cita</button>
            </div>
        </form>
    </div>

   
</body>
</html>