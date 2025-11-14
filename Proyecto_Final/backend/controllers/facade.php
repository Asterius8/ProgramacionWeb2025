<?php

include_once __DIR__ . '/../../database/conexion_bd_user_clinica.php';

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
}
