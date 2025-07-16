<?php
namespace App\Database;

// use App\Interfaces\IPDOConnection;
use App\Repositories\GenericRepository;
use PDO;

/**
 * Contexto de base de datos para la aplicación EbPhotoDelphinus.
 *
 * Esta clase se utiliza para encapsular la lógica de acceso a datos específica de la aplicación.
 * Actualmente, no contiene métodos específicos, pero puede ser extendida en el futuro.
 */

// class FotosContext extends GenericRepository
// {
//     private PDO $pdo;
//     public function __construct(IPDOConnection $connectionProvider)
//     {
//         $this->pdo = $connectionProvider::connect('FOTOS');
//         if (!$this->pdo) {
//             throw new \Exception("No se pudo establecer la conexión a la base de datos EBPHOTODELPHINUS");
//         }
//         parent::__construct($this->pdo);
//     }

// }
