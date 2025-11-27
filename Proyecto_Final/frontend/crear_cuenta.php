<?php

session_start();

if (!isset($_SESSION['error_crear']) && !isset($_SESSION['cuenta_creada'])) {
    unset($_SESSION['email']);  // ← Si no hay error, no mostrar nada guardado
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Clínica del Bienestar</title>
    <link rel="stylesheet" href="css/crear_cuenta.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- LiveValidation -->
    <script src="https://cdn.jsdelivr.net/gh/Formu8JS/LiveValidateJS@main/livevalidate.js"></script>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>

    <div class="container">

        <form id="formCrearCuenta" class="form-card" method="POST" action="../backend/controllers/alta_cuenta.php">

            <h2>Crear Cuenta</h2>
            <p class="subtitle">Regístrate para acceder a tu cuenta</p>
            <div>

                <label for="correo">Correo electrónico</label>
                <input type="text" id="caja_email" name="caja_email" placeholder="Ingresa tu correo"
                    value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '' ?>">

            </div>

            <div>
                <label for="password">Contraseña</label>
                <input type="password" id="caja_password" name="caja_password" placeholder="Ingresa tu contraseña">
            </div>

            <div class="g-recaptcha" data-sitekey="6LfwqhgsAAAAAK5tJnzOEkIz5HrFtXv_Lh32CxOx"></div>

            <button type="submit" class="btn-primary">Crear Cuenta</button>

            <p class="extra-info">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
            </p>
        </form>
    </div>

    <!-- Validaciones con LiveValidation -->
    <script>
        const emailInput = document.getElementById('caja_email');
        const passwordInput = document.getElementById('caja_password');

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

    if (isset($_SESSION['cuenta_creada']) && $_SESSION['cuenta_creada'] == true) {
        echo "<script>
        Swal.fire({
            title: '¡Cuenta creada!',
            text: 'Tu cuenta se registró correctamente.',
            icon: 'success',
            confirmButtonColor: '#8B0035'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'form_paciente.php';
            }
        });
    </script>";
        unset($_SESSION['cuenta_creada']);
    }

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

    <script>
        document.getElementById('formCrearCuenta').addEventListener('submit', function(e) {
            if (grecaptcha.getResponse() === "") {
                e.preventDefault();
                Swal.fire({
                    title: 'Completa el Captcha',
                    text: 'Por favor verifica que no eres un robot.',
                    icon: 'warning',
                    confirmButtonColor: '#8B0035'
                });
            }
        });
    </script>


</body>

</html>