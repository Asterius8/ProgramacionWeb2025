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
    //Transaccion =============================================================================================
    public function agregarUsuario($email, $password)
{
    $conn = $this->conexion->getConexion();
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    //Iniciar transacción
    mysqli_begin_transaction($conn);

    try {
        $sql = "INSERT INTO cuentas (Correo, Password, Rol)
                VALUES (?, ?, 'Paciente')";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            mysqli_rollback($conn);
            return false;
        }

        mysqli_stmt_bind_param($stmt, "ss", $email, $password_hash);

        $res = mysqli_stmt_execute($stmt);

        if (!$res) {
            //deshacer cambios
            mysqli_rollback($conn);
            return false;
        }

        //guardar cambios
        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        // excepción rollback
        mysqli_rollback($conn);
        return false;
    }
}


    //=================================== CONSULTAS =======================================

    public function existeCorreo($email)
    {
        $sql = "SELECT COUNT(*) FROM cuentas WHERE Correo = ?";
        $stmt = mysqli_prepare($this->conexion->getConexion(), $sql);

        if (!$stmt) {
            return true;
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
        $this->conexion2 = ConexionBDClinica::getInstancia();
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
            $n,
            $ap,
            $am,
            $fn,
            $s,
            $t,
            $email,
            $ts,
            $cen,
            $cet,
            $idCuenta
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
            "ssss",
            $ts,
            $cen,
            $cet,
            $email
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

    public function existePacienteDuplicado($nombre, $apPat, $apMat, $fechaNac, $sexo, $telefono)
    {
        $sql = "SELECT Id_Pacientes 
            FROM pacientes 
            WHERE Nombre = ? 
            AND Apellido_Paterno = ?
            AND Apellido_Materno = ?
            AND Fecha_Nac = ?
            AND Sexo = ?
            AND Telefono = ?
            LIMIT 1";

        $cnn = $this->conexion2->getConexion();
        $stmt = $cnn->prepare($sql);

        $stmt->bind_param("ssssss", $nombre, $apPat, $apMat, $fechaNac, $sexo, $telefono);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function existePacientePorCorreo($email)
    {
        $conexion = $this->conexion2->getConexion();

        $conexion->begin_transaction();

        $sql = "SELECT Id_Pacientes FROM pacientes WHERE Email = ? FOR UPDATE";
        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            $conexion->rollback();
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $existe = $result->num_rows > 0;

        $conexion->commit();

        return $existe;
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

        $this->conexion2 = ConexionBDClinica::getInstancia();
    }

    //=================================== METODOS ABCC Medico (CRUD) ========================================
    //=================================== ALTAS =======================================
    public function agregarMedico($n, $ap, $am, $esp)
    {

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
            $n,
            $ap,
            $am,
            $esp
        );

        return mysqli_stmt_execute($stmt);
    }

    //=================================== BAJAS =======================================

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

        return $resultado;
    }

    //=================================== CAMBIOS =======================================

    public function editarMedico($id, $nombre, $ap, $am, $especialidad)
    {

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

    //=================================== METODOS ABCC Medico (CRUD) ========================================
    //=================================== ALTAS =======================================
    //=================================== PROCEDIMIENTO =======================================
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
                    "message" => $msg
                ];
            }
        }
    }

    //=================================== BAJAS =======================================

    public function eliminarCita($id)
    {
        $sql = "DELETE FROM citas WHERE Id_Citas = ?";

        $stmt = mysqli_prepare($this->conexion2->getConexion(), $sql);

        mysqli_stmt_bind_param($stmt, "i", $id);

        $ok = mysqli_stmt_execute($stmt);

        return $ok;
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
            $sql = "SELECT Fecha, Hora, Pacientes_Id_Pacientes ,Especialidad_Nombre FROM citas";
        } else {
            $sql = "SELECT Fecha, Hora, Especialidad_Nombre 
                FROM citas
                WHERE Fecha LIKE '%$filtro%' 
                OR Hora LIKE '%$filtro%' 
                Or Pacientes_Id_Pacientes LIKE '%$filtro%'
                OR Especialidad_Nombre LIKE '%$filtro%' ";
        }

        return mysqli_query($this->conexion2->getConexion(), $sql);
    }

    //=================================== CAMBIOS =======================================

    public function editarCitas($id, $hora, $fecha, $pacienteid, $medicoid, $especialidad)
    {
        $sql = "UPDATE citas 
            SET Hora = ?, 
                Fecha = ?, 
                Pacientes_Id_Pacientes = ?, 
                Medicos_Id_Medicos = ?, 
                Especialidad_Nombre = ?
            WHERE Id_Citas = ?";

        $conn = $this->conexion2->getConexion();
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param("ssiiii", $hora, $fecha, $pacienteid, $medicoid, $especialidad, $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function consultarIdsCitas($idCita)
    {
        $sql = "SELECT 
                Pacientes_Id_Pacientes,
                Medicos_Id_Medicos
            FROM citas
            WHERE Id_Citas = ?";

        $conn = $this->conexion2->getConexion();

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparando consulta: " . $conn->error);
        }

        $stmt->bind_param("i", $idCita);
        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado->fetch_assoc();
    }

    //=================================== VISTA =======================================
    public function consultaCitasVista($filtro)
    {
        if ($filtro == 'x' || $filtro == '') {

            $sql = "SELECT 
                    c.Id_Citas,
                    c.Fecha,
                    c.Hora,
                    c.Especialidad_Nombre,

                    CONCAT(p.Nombre, ' ', p.Apellido_Paterno, ' ', p.Apellido_Materno) AS Paciente,

                    CONCAT(m.Nombre, ' ', m.Apellido_Paterno, ' ', m.Apellido_Materno) AS Medico

                FROM citas c
                INNER JOIN pacientes p
                    ON c.Pacientes_Id_Pacientes = p.Id_Pacientes
                INNER JOIN medicos m
                    ON c.Medicos_Id_Medicos = m.Id_Medicos";
        } else {

            $sql = "SELECT 
                    c.Id_Citas,
                    c.Fecha,
                    c.Hora,
                    c.Especialidad_Nombre,

                    CONCAT(p.Nombre, ' ', p.Apellido_Paterno, ' ', p.Apellido_Materno) AS Paciente,

                    CONCAT(m.Nombre, ' ', m.Apellido_Paterno, ' ', m.Apellido_Materno) AS Medico

                FROM citas c
                INNER JOIN pacientes p
                    ON c.Pacientes_Id_Pacientes = p.Id_Pacientes
                INNER JOIN medicos m
                    ON c.Medicos_Id_Medicos = m.Id_Medicos
                WHERE 
                    c.Fecha LIKE '%$filtro%'
                    OR c.Hora LIKE '%$filtro%'
                    OR c.Especialidad_Nombre LIKE '%$filtro%'
                    OR p.Nombre LIKE '%$filtro%'
                    OR p.Apellido_Paterno LIKE '%$filtro%'
                    OR p.Apellido_Materno LIKE '%$filtro%'
                    OR m.Nombre LIKE '%$filtro%'
                    OR m.Apellido_Paterno LIKE '%$filtro%'
                    OR m.Apellido_Materno LIKE '%$filtro%'";
        }

        return mysqli_query($this->conexion2->getConexion(), $sql);
    }
}
