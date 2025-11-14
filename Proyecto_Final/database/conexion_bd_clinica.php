<?php

class ConexionBDClinica {

    // Instancia única de la clase
    private static $instancia = null;

    // Conexión mysqli
    private $conexion;

    // Datos de conexión
    private $host = "localhost:3307";
    private $usuario = "oscar";
    private $password = "oscar";
    private $bd = "BD_clinica_2025";

    // 🔒 Constructor privado: evita crear objetos con new
    private function __construct() {
        $this->conexion = mysqli_connect(
            $this->host,
            $this->usuario,
            $this->password,
            $this->bd
        );

        if (!$this->conexion) {
            die("Error en la conexión a la BD: " . mysqli_connect_error());
        }
    }

    // 🏛️ Método público para obtener la única instancia
    public static function getInstancia() {
        if (self::$instancia == null) {
            self::$instancia = new ConexionBDClinica();
        }
        return self::$instancia;
    }

    // 👉 Obtener la conexión mysqli
    public function getConexion() {
        return $this->conexion;
    }
}

?>
