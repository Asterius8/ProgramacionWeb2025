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
        $sql = "SELECT Correo, Password, Rol FROM cuentas WHERE Correo = ?";
        $stmt = mysqli_prepare($this->conexion->getConexion(), $sql);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $correoBD, $passwordBD, $rolBD);

        if (mysqli_stmt_fetch($stmt)) {
            return [
                "correo" => $correoBD,
                "password" => $passwordBD, // <-- EL HASH ENCRIPTADO
                "rol" => $rolBD
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

    //=================================== CAMBIOS =======================================

    public function cambiarPaciente($n, $ap, $am, $fn, $s, $t, $ts, $cen, $cet, $email)
    {
        $sql = "UPDATE pacientes SET
        Nombre = ?,
        Apellido_Paterno = ?,
        Apellido_Materno = ?,
        Fecha_Nac = ?,
        Sexo = ?,
        Telefono = ?,
        Tipo_Seguro = ?,
        Contacto_Emergencia_Nombre = ?,
        Contacto_Emergencia_Telefono = ?
        WHERE Email = ?";

        $stmt = mysqli_prepare($this->conexion2->getConexion(), $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssss",   // 10 parámetros tipo string
            $n,            // Nombre
            $ap,           // Apellido paterno
            $am,           // Apellido materno
            $fn,           // Fecha nacimiento
            $s,            // Sexo
            $t,            // Teléfono
            $ts,           // Tipo seguro
            $cen,          // Contacto emergencia nombre
            $cet,          // Contacto emergencia teléfono
            $email         // Email (WHERE)
        );

        return mysqli_stmt_execute($stmt);
    }


    //=================================== CONSULTAS =======================================
    public function consultarPaciente($filtro)
    {

        if ($filtro == 'x' || $filtro == '') {

            $sql = "SELECT Nombre, Apellido_Paterno, Apellido_Materno, Fecha_Nac, Sexo, Telefono, Email, Tipo_Seguro, Contacto_Emergencia_Nombre, Contacto_Emergencia_Telefono, Id_Cuenta FROM pacientes";
        } else {

            $sql = "SELECT Nombre, Apellido_Paterno, Apellido_Materno, Fecha_Nac, Sexo, Telefono, Email, Tipo_Seguro, Contacto_Emergencia_Nombre, Contacto_Emergencia_Telefono, Id_Cuenta
                FROM pacientes
                WHERE Nombre LIKE '%$filtro%' 
                OR Apellido_Paterno LIKE '%$filtro%' 
                OR Apellido_Materno LIKE '%$filtro%' 
                OR Fecha_Nac LIKE '%$filtro%'
                OR Sexo LIKE '%$filtro%'
                OR Telefono LIKE '%$filtro%'
                OR Email LIKE '%$filtro%'
                OR Tipo_Seguro LIKE '%$filtro%'
                OR Contacto_Emergencia_Nombre LIKE '%$filtro%'
                OR Contacto_Emergencia_Telefono LIKE '%$filtro%'
                OR Id_Cuenta LIKE '%$filtro%'";
        }

        return mysqli_query($this->conexion2->getConexion(), $sql);
    }
}

//==================================================================================================================================================================================
//Clase Medico
//==================================================================================================================================================================================

class medicoDAO
{

    private $conexion2;

    public function __construct()
    {

        $this->conexion2 = ConexionBDClinica::getInstancia(); // guardas el SINGLETON

    }

    //=================================== METODOS ABCC Medico (CRUD) ========================================
    //=================================== ALTAS =======================================
    public function agregarMedico($n, $ap, $am, $esp)
    {
        $sql = "INSERT INTO medicos (
        Nombre,
        Apellido_Paterno,
        Apellido_Materno,
        Especialidad
    ) VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conexion2->getConexion(), $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $n,    // Nombre
            $ap,   // Apellido Paterno
            $am,   // Apellido Materno
            $esp   // Especialidad
        );

        return mysqli_stmt_execute($stmt);
    }
}
