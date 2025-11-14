<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Clínica del Bienestar</title>
    <link rel="stylesheet" href="css/crear_cuenta.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <div class="container">

        <form class="form-card" method="POST" action="../backend/controllers/alta_cuenta.php">
            <h2>Crear Cuenta</h2>
            <p class="subtitle">Regístrate para acceder a tu cuenta</p>

            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="caja_email" placeholder="Ingresa tu correo" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="caja_password" placeholder="Ingresa tu contraseña" required>

            <button type="submit" class="btn-primary">Crear Cuenta</button>

            <p class="extra-info">
                ¿Ya tienes cuenta? <a href="login.html">Inicia sesión</a>
            </p>
        </form>
    </div>

    <?php
    session_start();

    if (isset($_SESSION['cuenta_creada']) && $_SESSION['cuenta_creada'] == true) {
        echo "<script>
    Swal.fire({
        title: '¡Cuenta creada!',
        text: 'Tu cuenta se registró correctamente.',
        icon: 'success',
        confirmButtonColor: '#8B0035'
    });
    </script>";

        unset($_SESSION['cuenta_creada']); // limpiar
    }

    if (isset($_SESSION['error_crear']) && $_SESSION['error_crear'] == true) {
        echo "<script>
    Swal.fire({
        title: 'Error',
        text: 'Hubo un problema al crear la cuenta.',
        icon: 'error',
        confirmButtonColor: '#8B0035'
    });
    </script>";

        unset($_SESSION['error_crear']); // limpiar
    }
    ?>

</body>

</html>