<?php
namespace App\Database;

// use App\Interfaces\IPDOConnection;
// use PDO;
// use PDOException; // Import for specific exception handling

// class SqlSrvConnectionProvider implements IPDOConnection
// {
//     private string $dsn;
//     private string $username;
//     private string $password;

//     public function __construct(string $dsn, string $username, string $password)
//     {
//         $this->dsn = $dsn;
//         $this->username = $username;
//         $this->password = $password;
//     }

//     public function getConnection(): PDO
//     {
//         try {
//             $pdo = new PDO($this->dsn, $this->username, $this->password);
//             $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Crucial for error handling
//             // Set other attributes as needed, e.g., default fetch mode
//             // $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//             return $pdo;
//         } catch (PDOException $e) {
//             // Log the error or handle it as appropriate for your application
//             throw new PDOException("Error de conexión a la base de datos: " . $e->getMessage(), (int)$e->getCode(), $e);
//         }
//     }
// }