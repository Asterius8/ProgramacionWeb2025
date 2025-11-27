<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cita - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/agregar_cita.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- LiveValidation -->
    <script src="https://cdn.jsdelivr.net/gh/Formu8JS/LiveValidateJS@main/livevalidate.js"></script>
    <style>
        .error-item,
        .error-message .error-item,
        div.error-item,
        .error-item[data-error],
        .error-item[style] {
            color: red !important;
            font-weight: 700 !important;
            font-size: 15px !important;
        }
    </style>

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
                <input type="date" id="fecha" name="fecha"
                    value="<?php echo $_SESSION['fecha'] ?? ''; ?>">

            </div>

            <div class="form-group">
                <label for="hora">Hora de la Cita</label>
                <input type="time" id="hora" name="hora"
                    value="<?php echo $_SESSION['hora'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="medico">Médico y Especialidad</label>
                <select id="medico" name="medico">
                    <option value="">Seleccione un médico</option>

                    <?php
                    $medicoSeleccionado = $_SESSION['medico_seleccionado'] ?? '';

                    while ($row = mysqli_fetch_assoc($listaMedicos)) :
                        $id = $row['Id_Medicos'];
                        $nombreCompleto = $row['Nombre'] . ' ' . $row['Apellido_Paterno'] . ' ' . $row['Apellido_Materno'];
                        $especialidad = $row['Especialidad'];

                        // Si el ID coincide con el de la sesión
                        $selected = ($id == $medicoSeleccionado) ? 'selected' : '';
                    ?>
                        <option value="<?= $id ?>" <?= $selected ?>>
                            <?= $nombreCompleto ?> - <?= $especialidad ?>
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

    <script>
        // === FECHA ===
        const fechaInput = document.getElementById('fecha');

        const fechaValidator = addLiveValidation(fechaInput, [{
            required: true,
            requiredMessage: "La fecha es obligatoria"
        }], {
            displayMode: "classic"
        });


        // === HORA ===
        const horaInput = document.getElementById('hora');

        const horaValidator = addLiveValidation(horaInput, [{
            required: true,
            requiredMessage: "La hora es obligatoria"
        }, ], {
            displayMode: "classic"
        });


        // === MÉDICO ===
        const medicoInput = document.getElementById('medico');

        const medicoValidator = addLiveValidation(medicoInput, [{
            required: true,
            requiredMessage: "Debe seleccionar un médico"
        }], {
            displayMode: "classic"
        });
    </script>

    <script>
        // Elimina LOS MENSAJES DE ÉXITO que LiveValidate crea
        const observer = new MutationObserver(() => {
            document.querySelectorAll(".success-message").forEach(msg => msg.remove());
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    </script>

    <!-- SweetAlert para retroalimentar al usuario-->
    <?php

    if (isset($_SESSION['cita_creada']) && $_SESSION['cita_creada'] == true) {
        echo "<script>
        Swal.fire({
            title: '¡Cuenta creada!',
            text: 'Tu cuenta se registró correctamente.',
            icon: 'success',
            confirmButtonColor: '#8B0035'
        });
    </script>";
        unset($_SESSION['cita_creada']);
    }

    if (isset($_SESSION['error_crear_cita']) && $_SESSION['error_crear_cita'] == true) {

        $lista = "";
        if (isset($_SESSION['errores_lista'])) {
            foreach ($_SESSION['errores_lista'] as $err) {
                $lista .= "<li>$err</li>";
            }
        }

        echo "<script> 
        Swal.fire({
            title: 'Errores encontrados',
            html: '<ul style=\"text-align: left; color:#d33;\">$lista</ul>',
            icon: 'error', 
            confirmButtonColor: '#8B0035' 
        }); 
    </script>";

        unset($_SESSION['error_crear_cita']);
        unset($_SESSION['errores_lista']);
    }

    ?>


</body>

</html>