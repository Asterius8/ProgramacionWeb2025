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

    public function cambiarPaciente($ts, $cen, $cet, $email)
    {
        $sql = "UPDATE pacientes SET
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
            "ssss",   // 10 parámetros tipo string
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

    public function consultarIdPaciente($filtro)
    {

        if ($filtro == 'x' || $filtro == '') {

            $sql = "SELECT Id_Pacientes, Nombre, Apellido_Paterno, Apellido_Materno, Fecha_Nac, Sexo, Telefono, Email, Tipo_Seguro, Contacto_Emergencia_Nombre, Contacto_Emergencia_Telefono, Id_Cuenta FROM pacientes";
        } else {

            $sql = "SELECT Id_Pacientes, Nombre, Apellido_Paterno, Apellido_Materno, Fecha_Nac, Sexo, Telefono, Email, Tipo_Seguro, Contacto_Emergencia_Nombre, Contacto_Emergencia_Telefono, Id_Cuenta
                FROM pacientes
                WHERE Email LIKE '%$filtro%'";
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
        // Evitar insertar la opción inválida del SELECT
        if ($esp === "Seleccione una especialidad") {
            return false;
        }

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

    public function eliminarMedico($id)
    {

        $sql = "DELETE FROM medicos WHERE Id_Medicos = ?";

        $stmt = mysqli_prepare($this->conexion2->getConexion(), $sql);

        mysqli_stmt_bind_param($stmt, "i", $id);

        $ok = mysqli_stmt_execute($stmt);

        return $ok;
    }

    //=================================== CONSULTAS =======================================

    public function consultarMedicos($filtro)
    {
        if ($filtro == 'x' || $filtro == '') {

            $sql = "SELECT Id_Medicos, Nombre, Apellido_Paterno, Apellido_Materno, Especialidad 
                FROM medicos";
        } else {

            $sql = "SELECT Id_Medicos, Nombre, Apellido_Paterno, Apellido_Materno, Especialidad 
                FROM medicos
                WHERE Id_Medicos LIKE '%$filtro%'
                OR Nombre LIKE '%$filtro%'
                OR Apellido_Paterno LIKE '%$filtro%'
                OR Apellido_Materno LIKE '%$filtro%'
                OR Especialidad LIKE '%$filtro%'";
        }

        return mysqli_query($this->conexion2->getConexion(), $sql);
    }


    public function hayMedicos()
    {
        $sql = "SELECT COUNT(*) AS total FROM medicos";
        $stmt = mysqli_prepare($this->conexion2->getConexion(), $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($resultado) {
            $fila = mysqli_fetch_assoc($resultado);
            return $fila['total'] > 0;
        }

        return false;
    }

    public function obtenerMedicos()
    {
        $sql = "SELECT Id_Medicos, Nombre, Apellido_Paterno, Apellido_Materno, Especialidad FROM medicos";
        $resultado = mysqli_query($this->conexion2->getConexion(), $sql);

        return $resultado; // regresamos el mysqli_result para recorrerlo
    }

    public function editarMedico($id, $nombre, $ap, $am, $especialidad)
    {
        // Query UPDATE
        $sql = "UPDATE medicos SET 
                Nombre = ?, 
                Apellido_Paterno = ?, 
                Apellido_Materno = ?, 
                Especialidad = ?
            WHERE Id_Medicos = ?";

        $stmt = mysqli_prepare($this->conexion2->getConexion(), $sql);

        if (!$stmt) {
            return false;
        }

        // s = string, i = integer
        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $nombre,
            $ap,
            $am,
            $especialidad,
            $id
        );

        $resultado = mysqli_stmt_execute($stmt);

        return $resultado;
    }
}

//==================================================================================================================================================================================
//Clase Cita
//==================================================================================================================================================================================
class citaDAO
{
    private $conexion2;

    public function __construct()
    {

        $this->conexion2 = ConexionBDClinica::getInstancia();
    }


    public function agregarCita($fecha, $hora, $idPaciente, $idMedico, $nombreMedico)
    {
        $sql = "CALL CrearCita(?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conexion2->getConexion(), $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssiss",
            $fecha,
            $hora,
            $idPaciente,
            $idMedico,
            $nombreMedico
        );
        try {
            mysqli_stmt_execute($stmt);

            return [
                "success" => true
            ];
        } catch (mysqli_sql_exception $e) {

            $code = $e->getCode();
            $msg  = $e->getMessage();

            if ($code == 1062) {
                // Error UNIQUE
                return [
                    "success" => false,
                    "type" => "unique_violation",
                    "message" => "Cita duplicada: el paciente ya tiene una cita con ese médico a esa hora."
                ];
            }

            if ($code == 1644) {
                // Error del SIGNAL en el procedimiento
                return [
                    "success" => false,
                    "type" => "procedure_validation",
                    "message" => $msg  // Mensaje generado en el SP
                ];
            }
        }
    }
    //=================================== CONSULTAS =======================================
    public function consultarCitasPorPaciente($idPaciente)
    {
        $sql = "SELECT * FROM citas WHERE Pacientes_Id_Pacientes = ?";

        $stmt = $this->conexion2->getConexion()->prepare($sql);
        $stmt->bind_param("i", $idPaciente);
        $stmt->execute();
        $result = $stmt->get_result();

        $citas = [];
        while ($row = $result->fetch_assoc()) {
            $citas[] = $row;
        }

        return $citas;
    }

    public function consultaCitas($filtro)
    {

        if ($filtro == 'x' || $filtro == '') {
            $sql = "SELECT Fecha, Hora, Especialidad_Nombre FROM citas";
        } else {
            $sql = "SELECT Fecha, Hora, Especialidad_Nombre 
                FROM citas
                WHERE Fecha LIKE '%$filtro%' 
                OR Hora LIKE '%$filtro%' 
                OR Especialidad_Nombre LIKE '%$filtro%' ";
        }

        return mysqli_query($this->conexion2->getConexion(), $sql);
    }
}
