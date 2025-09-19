<?php

// Detalles de la base de datos
$db_host = 'localhost'; // El servidor de la base de datos es local
$db_name = 'concurso_videos'; // Nombre de la base de datos que creamos
$db_user = 'root'; // Usuario por defecto de MySQL en XAMPP
$db_pass = ''; // Contraseña por defecto (vacía) en XAMPP

// Opciones de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Cadena de conexión (DSN)
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";

try {
    // Crear una nueva instancia de PDO
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    // Si la conexión falla, muestra un mensaje de error detallado
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

?>