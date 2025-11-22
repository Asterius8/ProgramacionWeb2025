<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cita - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/agregar_cita.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <?php

    require_once('navbar_paciente.php');

    //verificar que existan medicos en la BD
    $medicoDAO = new medicoDAO();

    if (!$medicoDAO->hayMedicos()) {
        echo "
    <script>
    Swal.fire({
        icon: 'error',
        title: 'No hay médicos registrados',
        text: 'Debes esperar que al menos un médico sea registrado.'
    }).then(() => {
        window.location.href = 'landing_paciente.php';
    });
    </script>";
        exit;
    }

    // Obtener lista de médicos
    $listaMedicos = $medicoDAO->obtenerMedicos();

    ?>

    <div class="form-container">
        <div class="form-header">
            <h1>Crear Nueva Cita</h1>
            <p>Complete todos los campos para programar una nueva cita médica</p>
        </div>

        <form id="appointment-form" method="POST" action="../backend/controllers/alta_cita.php">
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

                    <?php while ($row = mysqli_fetch_assoc($listaMedicos)) : ?>
                        <option value="<?= $row['Id_Medicos'] ?>">
                            <?= $row['Nombre'] . ' ' . $row['Apellido_Paterno'] . ' ' . $row['Apellido_Materno'] ?>
                            - <?= $row['Especialidad'] ?>
                        </option>
                    <?php endwhile; ?>

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