<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/form_paciente.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- LiveValidation -->
    <script src="https://cdn.jsdelivr.net/gh/Formu8JS/LiveValidateJS@main/livevalidate.js"></script>
    <title>Editar - Clinica del Bienestar</title>
</head>

<body>

    <?php 

    require_once('navbar_paciente.php');

    $pacienteDAO = new pacienteDAO();

    $datos = $pacienteDAO->consultarPaciente($_SESSION['email']);

    $paciente = mysqli_fetch_assoc($datos);

    ?>
    <div class="container">
        <form class="form-card" method="POST" action="../backend/controllers/cambios_paciente.php" id="datosForm">
            <h2>Datos Personales</h2>
            <p class="subtitle">Modifica tu información personal</p>

            <div class="form-group">
                <label for="nombre">Nombre(s)</label>
                <input type="text" id="nombre" name="nombre"
                    value="<?php echo $paciente['Nombre']; ?>"
                    placeholder="Ingresa tu nombre" readonly>
            </div>

            <div class="form-group">
                <label for="primer_apellido">Primer Apellido</label>
                <input type="text" id="primer_apellido" name="primer_apellido"
                    value="<?php echo $paciente['Apellido_Paterno']; ?>"
                    placeholder="Ingresa tu primer apellido" readonly>

            </div>

            <div class="form-group">
                <label for="segundo_apellido">Segundo Apellido</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido"
                    value="<?php echo $paciente['Apellido_Materno']; ?>"
                    placeholder="Ingresa tu segundo apellido" readonly>

            </div>

            <div class="form-group">
                <label for="fecha_nac">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nac" name="fecha_nac"
                    value="<?php echo $paciente['Fecha_Nac']; ?>" readonly>
            </div>

            <div class="form-group">
                <label>Sexo</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="sexo_m" name="sexo" value="M"
                            <?php echo ($paciente['Sexo'] == 'M') ? 'checked' : ''; ?> disabled>
                        <label for="sexo_m">Masculino</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="sexo_f" name="sexo" value="F"
                            <?php echo ($paciente['Sexo'] == 'F') ? 'checked' : ''; ?> disabled> 
                        <label for="sexo_f">Femenino</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="sexo_o" name="sexo" value="O"
                            <?php echo ($paciente['Sexo'] == 'O') ? 'checked' : ''; ?> disabled>
                        <label for="sexo_o">Otro</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono"
                    value="<?php echo $paciente['Telefono']; ?>"
                    placeholder="Ingresa tu número de teléfono" readonly>
            </div>

            <div class="form-group">
                <label for="tipo_seguro">Tipo de Seguro</label>
                <select id="tipo_seguro" name="tipo_seguro">
                    <option value="" disabled>Selecciona una opción</option>

                    <option value="Privado"
                        <?php echo ($paciente['Tipo_Seguro']) === "Privado" ? "selected" : ""; ?>>
                        Privado
                    </option>

                    <option value="Aseguradora"
                        <?php echo ($paciente['Tipo_Seguro']) === "Aseguradora" ? "selected" : ""; ?>>
                        Aseguradora
                    </option>

                    <option value="Gobierno"
                        <?php echo ($paciente['Tipo_Seguro']) === "Gobierno" ? "selected" : ""; ?>>
                        Gobierno
                    </option>

                    <option value="Indigente"
                        <?php echo ($paciente['Tipo_Seguro']) === "Indigente" ? "selected" : ""; ?>>
                        Indigente
                    </option>

                    <option value="Ninguno"
                        <?php echo ($paciente['Tipo_Seguro']) === "Ninguno" ? "selected" : ""; ?>>
                        Ninguno
                    </option>
                </select>
            </div>

            <div class="emergency-contact">
                <h3>Contacto de Emergencia</h3>

                <div class="form-group">
                    <label for="contacto_emergencia">Nombre del Contacto</label>
                    <input type="text" id="contacto_emergencia" name="contacto_emergencia"
                        value="<?php echo $paciente['Contacto_Emergencia_Nombre']; ?>"
                        placeholder="Nombre completo del contacto">
                </div>

                <div class="form-group">
                    <label for="telefono_emergencia">Teléfono del Contacto</label>
                    <input type="tel" id="telefono_emergencia" name="telefono_emergencia"
                        value="<?php echo $paciente['Contacto_Emergencia_Telefono']; ?>"
                        placeholder="Número de teléfono del contacto">
                </div>
            </div>

            <button type="submit" class="btn-primary">Guardar Datos</button>
        </form>
    </div>

    <!-- Validaciones con LiveValidation -->
    <script>
        addLiveValidation(document.getElementById('tipo_seguro'), [{
            required: true,
            requiredMessage: "Selecciona un tipo de seguro"
        }], {
            displayMode: "classic"
        });

        addLiveValidation(document.getElementById('contacto_emergencia'), [{
                required: true,
                requiredMessage: "El nombre del contacto es obligatorio"
            },
            {
                pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/,
                patternMessage: "Solo se permiten letras y espacios"
            }
        ], {
            displayMode: "classic"
        });

        addLiveValidation(document.getElementById('telefono_emergencia'), [{
                required: true,
                requiredMessage: "El teléfono del contacto es obligatorio"
            },
            {
                pattern: /^[0-9]{10}$/,
                patternMessage: "Debe ser un número de 10 dígitos"
            }
        ], {
            displayMode: "classic"
        });
    </script>

    <!-- Elimina LOS MENSAJES DE ÉXITO que LiveValidate crea -->
    <script>
        const observer = new MutationObserver(() => {
            document.querySelectorAll(".success-message").forEach(msg => msg.remove());
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    </script>

    <!-- Alerta de retroalimentacion -->
    <?php
    if (isset($_SESSION['paciente_editado']) && $_SESSION['paciente_editado'] == true) {
        echo "<script>
        Swal.fire({
            title: 'Paciente editado!',
            text: 'Tu informacion se modifico correctamente.',
            icon: 'success',
            confirmButtonColor: '#8B0035'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'landing_paciente.php';
            }
        });
    </script>";
        unset($_SESSION['paciente_editado']);
    }

    if (isset($_SESSION['error_modificar_paciente']) && $_SESSION['error_modificar_paciente'] == true) {

        $lista = "";
        if (isset($_SESSION['errores_lista_m'])) {
            foreach ($_SESSION['errores_lista_m'] as $err) {
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

        unset($_SESSION['error_modificar_paciente']);
        unset($_SESSION['errores_lista_m']);
    }
    ?>

</body>

</html>