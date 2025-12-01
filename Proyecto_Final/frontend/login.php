<?php

session_start();


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Clínica del Bienestar</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
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
    <div class="login-container">
        <div class="login-hero">
            <h1>Bienvenido a Clínica del Bienestar</h1>
            <p>Cuidamos de tu salud integral con un enfoque humano y profesional. Accede a tu cuenta para gestionar tus
                citas.</p>
            <p>¿Aún no tienes cuenta? <a href="crear_cuenta.php" style="color: white; font-weight: 500;">Regístrate
                    aquí</a></p>
        </div>

        <div class="login-form">
            <div class="logo">
                <img src="icono_clinicas.png" alt="Logo Clínica del Bienestar">
            </div>

            <div class="form-title">
                <h2>Iniciar Sesión</h2>
                <p>Ingresa tus credenciales para acceder a tu cuenta</p>
            </div>

            <form action="../backend/controllers/validar_usuario.php" method="POST">

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" placeholder="tucorreo@example.com" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="••••••••">
                </div>


                <button type="submit" class="btn-login">Iniciar Sesión</button>

                <div class="register-link">
                    ¿No tienes una cuenta? <a href="crear_cuenta.php">Regístrate aquí</a>
                </div>
            </form>

            <script>
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');

                const emailValidator = addLiveValidation(emailInput, [{
                        required: true,
                        requiredMessage: "El correo es obligatorio"
                    },
                    {
                        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                        patternMessage: "Formato de correo inválido"
                    }
                ], {
                    displayMode: "classic",

                });

                const passwordValidator = addLiveValidation(passwordInput, [{
                        required: true,
                        requiredMessage: "La contraseña es obligatoria"
                    },
                    {
                        minLength: 6,
                        minLengthMessage: "Debe tener al menos 6 caracteres"
                    },
                    {
                        maxLength: 20,
                        maxLengthMessage: "No puede superar los 20 caracteres"
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

            <!-- SweetAlert para retroalimentar al usuario-->
            <?php

            if (isset($_SESSION['error_crear']) && $_SESSION['error_crear'] == true) {

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

                unset($_SESSION['error_crear']);
                unset($_SESSION['errores_lista']);
            }

            ?>

            <?php if (isset($_SESSION['falta_datos_personales'])): ?>
                <script>
                    Swal.fire({
                        title: "Faltan tus datos personales",
                        text: "Parece que creaste tu cuenta pero aún no llenas tu información.",
                        icon: "warning",
                        confirmButtonText: "Ir ahora"
                    }).then(() => {
                        window.location.href = "form_paciente.php";
                    });
                </script>
            <?php
                unset($_SESSION['falta_datos_personales']);
            endif;
            ?>


        </div>
    </div>
</body>

</html>