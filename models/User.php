<?php
//models/User.php
// Clase User: gestiona la autenticación de usuarios
class User
{
    // Propiedad privada para almacenar la conexión a la base de datos
    private $db;

    // Constructor de la clase: se ejecuta automáticamente cuando se crea una nueva instancia de User
    public function __construct()
    {
        // Incluye el archivo que contiene la clase Database para poder usarla
        require_once __DIR__ . '/Database.php';

        // Crea una instancia de la clase Database
        $database = new Database();

        // Obtiene la conexión PDO y la guarda en la propiedad $db
        $this->db = $database->getConnection();
    }

    // Método para autenticar al usuario
    // Recibe el nombre de usuario y la contraseña como parámetros
    public function authenticate($username, $password)
    {
        // Consulta SQL para buscar al usuario por su nombre
        // Se hace LEFT JOIN con la tabla levels_users para obtener el nombre del nivel
        // desription_level es un campo adicional que describe el nivel de usuario
        $sql = "SELECT users.*, levels_users.description_level 
                FROM users 
                LEFT JOIN levels_users ON users.level_user = levels_users.level 
                WHERE users.username = :username 
                LIMIT 1";

        // Prepara la consulta para evitar inyecciones SQL
        $stmt = $this->db->prepare($sql);

        // Asocia el valor del parámetro :username con la variable $username
        $stmt->bindParam('username', $username);

        // Ejecuta la consulta
        $stmt->execute();

        // Obtiene los resultados como un arreglo asociativo
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica si encontró un usuario y si la contraseña es válida
        // password_verify compara la contraseña escrita por el usuario ($password)
        // con el hash almacenado en la base de datos ($user['password'])
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Si coincide, retorna los datos del usuario
        }

        // Si no coincide o no encuentra usuario, retorna false
        return false;
    }
}
