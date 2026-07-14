<?php

// Requiere el archivo de configuración con las constantes de conexión (host, usuario, etc.)
require_once __DIR__ . '/../config/config.php';

// Clase Database: se encarga de establecer y devolver una conexión a la base de datos
class Database {
    // Propiedad privada que almacenará el objeto de conexión PDO
    private $connection;

    // Constructor de la clase: se ejecuta automáticamente cuando se crea una instancia de Database
    public function __construct() {
        try {
            // Define el DSN (Data Source Name), que indica el tipo de base de datos, el host, el nombre de la base de datos y el charset
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

            // Crea una nueva conexión PDO utilizando las constantes definidas en config.php
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);

            // Configura la conexión para lanzar excepciones en caso de errores (útil para depurar)
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            // En caso de fallo al conectar, termina la ejecución y muestra el error
            die("Fallo en la conexión a la base de datos: " . $e->getMessage());
        }
    }

    // Método público que devuelve la conexión para ser usada desde otras clases
    public function getConnection() {
        return $this->connection;
    }
}
