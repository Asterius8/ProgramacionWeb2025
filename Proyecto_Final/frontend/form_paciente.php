<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos Personales - Clínica del Bienestar</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/form_paciente.css">
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

    <div class="container">
        <form class="form-card" method="POST" action="../backend/controllers/alta_paciente.php" id="datosForm">
            <h2>Datos Personales</h2>
            <p class="subtitle">Completa tu información personal</p>

            <div class="form-group">
                <label for="nombre">Nombre(s)</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu nombre">
            </div>

            <div class="form-group">
                <label for="primer_apellido">Primer Apellido</label>
                <input type="text" id="primer_apellido" name="primer_apellido" placeholder="Ingresa tu primer apellido">
            </div>

            <div class="form-group">
                <label for="segundo_apellido">Segundo Apellido</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido"
                    placeholder="Ingresa tu segundo apellido">
            </div>

            <div class="form-group">
                <label for="fecha_nac">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nac" name="fecha_nac">
            </div>

            <div class="form-group">
                <label>Sexo</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="sexo_m" name="sexo" value="M">
                        <label for="sexo_m">Masculino</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="sexo_f" name="sexo" value="F">
                        <label for="sexo_f">Femenino</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="sexo_o" name="sexo" value="O">
                        <label for="sexo_o">Otro</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" placeholder="Ingresa tu número de teléfono">
            </div>

            <div class="form-group">
                <label for="tipo_seguro">Tipo de Seguro</label>
                <select id="tipo_seguro" name="tipo_seguro">
                    <option value="" disabled selected>Selecciona una opción</option>
                    <option value="Privado">Privado</option>
                    <option value="Aseguradora">Aseguradora</option>
                    <option value="Gobierno">Gobierno</option>
                    <option value="Indigente">Indigente</option>
                    <option value="Ninguno">Ninguno</option>
                </select>
            </div>

            <div class="emergency-contact">
                <h3>Contacto de Emergencia</h3>

                <div class="form-group">
                    <label for="contacto_emergencia">Nombre del Contacto</label>
                    <input type="text" id="contacto_emergencia" name="contacto_emergencia"
                        placeholder="Nombre completo del contacto">
                </div>

                <div class="form-group">
                    <label for="telefono_emergencia">Teléfono del Contacto</label>
                    <input type="tel" id="telefono_emergencia" name="telefono_emergencia"
                        placeholder="Número de teléfono del contacto">
                </div>
            </div>

            <button type="submit" class="btn-primary">Guardar Datos</button>
        </form>
    </div>

    <!-- Validaciones con LiveValidation -->
    <script>
        addLiveValidation(document.getElementById('nombre'), [{
                required: true,
                requiredMessage: "El nombre es obligatorio"
            },
            {
                pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/,
                patternMessage: "Solo se permiten letras y espacios"
            }
        ], {
            displayMode: "classic"
        });

        addLiveValidation(document.getElementById('primer_apellido'), [{
                required: true,
                requiredMessage: "El apellido es obligatorio"
            },
            {
                pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/,
                patternMessage: "Solo se permiten letras y espacios"
            }
        ], {
            displayMode: "classic"
        });

        addLiveValidation(document.getElementById('segundo_apellido'), [{
                required: true,
                requiredMessage: "El segundo apellido es obligatorio"
            },
            {
                pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/,
                patternMessage: "Solo se permiten letras y espacios"
            }
        ], {
            displayMode: "classic"
        });

        addLiveValidation(document.getElementById('fecha_nac'), [{
            required: true,
            requiredMessage: "La fecha de nacimiento es obligatoria"
        }], {
            displayMode: "classic"
        });

        // SEXO — versión compatible con todas las versiones
        addLiveValidation(document.getElementById('sexo_m'), [{
            required: true,
            requiredMessage: "Selecciona un sexo"
        }], {
            displayMode: "classic"
        });

        addLiveValidation(document.getElementById('telefono'), [{
                required: true,
                requiredMessage: "El teléfono es obligatorio"
            },
            {
                pattern: /^[0-9]{10}$/,
                patternMessage: "Debe ser un número de 10 dígitos"
            }
        ], {
            displayMode: "classic"
        });

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

</body>

</html>