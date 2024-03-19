<?php declare(strict_types=1);

namespace App;

require_once dirname(__DIR__) . '/database.php';

use Exception;

final class User
{
    private string $_token;
    private function __construct(string $token)
    {
        $this->_token = $token;
    }
    
    function __get(string $name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        throw new Exception('Propriedade inexistente.');
    }

    static function create(string $token)
    {
        $db = new \App\Database;
        $conn = $db->getConnection();
        try {
            $stmt = $conn->prepare('INSERT INTO users (token) values (?)');
            $stmt->execute([$token]);
            return new User($token);
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception(
                    'O impossível aconteceu. Este token já está registrado.',
                    0, $e
                );
            }
            throw new Exception('Falha durante o registro de novo usuário.', 0, $e);
        }
    }

}