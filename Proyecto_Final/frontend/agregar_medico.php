<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Médico - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/agregar_medico.css">
    <!-- LiveValidation -->
    <script src="https://cdn.jsdelivr.net/gh/Formu8JS/LiveValidateJS@main/livevalidate.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    require_once('navbar_admin.php');

    ?>

    <div class="form-container">
        <div class="form-header">
            <h1>Alta de Nuevo Médico</h1>
            <p>Complete todos los campos para registrar un nuevo médico en el sistema</p>
        </div>

        <form id="doctor-form" action="../backend/controllers/alta_medico.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre del médico">
            </div>

            <div class="form-group">
                <label for="apellido_paterno">Apellido Paterno</label>
                <input type="text" id="apellido_paterno" name="apellido_paterno" placeholder="Ingrese el apellido paterno">
            </div>

            <div class="form-group">
                <label for="apellido_materno">Apellido Materno</label>
                <input type="text" id="apellido_materno" name="apellido_materno" placeholder="Ingrese el apellido materno">
            </div>

            <div class="form-group">
                <label for="especialidad">Especialidad</label>
                <select id="especialidad" name="especialidad">
                    <option value="">Seleccione una especialidad</option>
                    <option value="Pediatra">Pediatra</option>
                    <option value="Cirujano">Cirujano</option>
                    <option value="Internista">Internista</option>
                    <option value="General">General</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="window.location.href='admin-panel.html'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Médico</button>
            </div>
        </form>
    </div>

    <!-- Validaciones con LiveValidation -->
    <script>
        const nombreInput = document.getElementById('nombre');
        const apellidoPInput = document.getElementById('apellido_paterno');
        const apellidoMInput = document.getElementById('apellido_materno');
        const especialidadInput = document.getElementById('especialidad');

        const soloLetras = /^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/;

        const nombreValidator = addLiveValidation(nombreInput, [{
                required: true,
                requiredMessage: "El nombre es obligatorio"
            },
            {
                pattern: soloLetras,
                patternMessage: "El nombre solo puede contener letras"
            },
            {
                minLength: 2,
                minLengthMessage: "El nombre debe tener al menos 2 letras"
            },
            {
                maxLength: 40,
                maxLengthMessage: "El nombre no puede exceder 40 letras"
            }
        ], {
            displayMode: "classic",
        });

        const apellidoPValidator = addLiveValidation(apellidoPInput, [{
                required: true,
                requiredMessage: "El apellido paterno es obligatorio"
            },
            {
                pattern: soloLetras,
                patternMessage: "El apellido paterno solo puede contener letras"
            },
            {
                minLength: 2,
                minLengthMessage: "Debe tener al menos 2 letras"
            },
            {
                maxLength: 40,
                maxLengthMessage: "No puede exceder 40 letras"
            }
        ], {
            displayMode: "classic",
        });

        const apellidoMValidator = addLiveValidation(apellidoMInput, [{
                required: true,
                requiredMessage: "El apellido materno es obligatorio"
            },
            {
                pattern: soloLetras,
                patternMessage: "El apellido materno solo puede contener letras"
            },
            {
                minLength: 2,
                minLengthMessage: "Debe tener al menos 2 letras"
            },
            {
                maxLength: 40,
                maxLengthMessage: "No puede exceder 40 letras"
            }
        ], {
            displayMode: "classic",
        });

const especialidadValidator = addLiveValidation(especialidadInput, [
    {
        required: true,
        requiredMessage: "La especialidad es obligatoria"
    },
    {
        custom: (value) => value !== "",
        customMessage: "Debe seleccionar una especialidad válida"
    }
], {
    displayMode: "classic",
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

    <!--SweetAlert-->
    <?php

    if (isset($_SESSION['medico_alta']) && $_SESSION['medico_alta'] == true) {
        echo "<script>
        Swal.fire({
            title: 'Medico Agregado!',
            text: 'La informacion ha sido guardada correctamente.',
            icon: 'success',
            confirmButtonColor: '#8B0035'
        });
    </script>";
        unset($_SESSION['medico_alta']);
    }

    if (isset($_SESSION['medico_alta_error']) && $_SESSION['medico_alta_error'] == true) {

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

        unset($_SESSION['medico_alta_error']);
        unset($_SESSION['errores_lista']);
    }
    ?>
</body>

</html>