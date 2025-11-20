<?php

include_once __DIR__ . '/../../database/conexion_bd_user_clinica.php';
include_once __DIR__ . '/../../database/conexion_bd_clinica.php';

//==================================================================================================================================================================================
//Clase Usuario
//==================================================================================================================================================================================

class usuarioDAO
{

    //Atributos
    private $conexion;


    public function __construct()
    {
        $this->conexion = ConexionBDUserClinica::getInstancia(); // guardas el SINGLETON

    }

    //=================================== METODOS ABCC USUARIOS (CRUD) ========================================

    //=================================== ALTAS =======================================
    public function agregarUsuario($email, $password)
    {
        // 1. Hashear contraseña de forma segura
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // 2. Query segura con placeholders
        $sql = "INSERT INTO cuentas (Correo, Password, Rol)
            VALUES (?, ?, 'Paciente')";

        // 3. Preparar sentencia
        $stmt = mysqli_prepare($this->conexion->getConexion(), $sql);

        if (!$stmt) {
            return false; // error al preparar
        }

        // 4. Vincular parámetros
        mysqli_stmt_bind_param($stmt, "ss", $email, $password_hash);

        // 5. Ejecutar
        $res = mysqli_stmt_execute($stmt);

        return $res;
    }

    //=================================== Consulta =======================================

    public function existeCorreo($email)
    {
        $sql = "SELECT COUNT(*) FROM cuentas WHERE Correo = ?";
        $stmt = mysqli_prepare($this->conexion->getConexion(), $sql);

        if (!$stmt) {
            return true; // si no se puede preparar, asumimos que existe para evitar registros incorrectos
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);

        return $count > 0; // si hay 1 o más, ya existe
    }

    public function obtenerEmailPassword($email)
    {
        $sql = "SELECT Correo, Password FROM cuentas WHERE Correo = ?";
        $stmt = mysqli_prepare($this->conexion->getConexion(), $sql);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $correoBD, $passwordBD);

        if (mysqli_stmt_fetch($stmt)) {
            return [
                "correo" => $correoBD,
                "password" => $passwordBD // <-- EL HASH ENCRIPTADO
            ];
        }

        return null;
    }

    public function obtenerIdCuentaPorEmail($email)
    {
        $sql = "SELECT Id_Cuenta FROM cuentas WHERE Correo = ?";
        $stmt = mysqli_prepare($this->conexion->getConexion(), $sql);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $idCuenta);
        mysqli_stmt_fetch($stmt);

        return $idCuenta;
    }
}

//==================================================================================================================================================================================
//Clase paciente
//==================================================================================================================================================================================

class pacienteDAO
{

    private $conexion2;

    public function __construct()
    {
        $this->conexion2 = ConexionBDClinica::getInstancia(); // guardas el SINGLETON
    }

    //=================================== METODOS ABCC Pacientes (CRUD) ========================================

    //=================================== ALTAS =======================================
    public function agregarPaciente($n, $ap, $am, $fn, $s, $t, $email, $ts, $cen, $cet, $idCuenta)
    {
        $sql = "INSERT INTO Pacientes (
        Nombre,
        Apellido_Paterno,
        Apellido_Materno,
        Fecha_Nac,
        Sexo,
        Telefono,
        Email,
        Tipo_Seguro,
        Contacto_Emergencia_Nombre,
        Contacto_Emergencia_Telefono,
        Id_Cuenta
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conexion2->getConexion(), $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssi",
            $n,     // Nombre
            $ap,    // Apellido paterno
            $am,    // Apellido materno
            $fn,    // Fecha de nacimiento
            $s,     // Sexo
            $t,     // Teléfono
            $email, // Email que viene de otra BD
            $ts,    // Tipo seguro
            $cen,   // Contacto emergencia nombre
            $cet,   // Contacto emergencia teléfono
            $idCuenta // Id_Cuenta de la BD de usuarios
        );

        return mysqli_stmt_execute($stmt);
    }
}
