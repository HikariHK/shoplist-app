<?php declare(strict_types=1); 


namespace App;

require_once dirname(__DIR__) . '/database.php';

use Exception;

final class Item
{
    private int $_id;
    private string $_token;
    private string $_name;
    private string $_type;
    private string $_unit_type;
    private float $_quantity;
    private int $_is_deleted;
    private int $_is_done;


    private function __construct(
        int $id,
        string $token,
        string $name,
        string $type,
        string $unit_type,
        float $quantity,
        int $is_deleted,
        int $is_done
    ) {
        $this->_id = $id;
        $this->_token = $token;
        $this->_name = $name;
        $this->_type = $type;
        $this->_unit_type = $unit_type;
        $this->_quantity = $quantity;
        $this->_is_deleted = $is_deleted;
        $this->_is_done = $is_done;
    }

    function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception('Propriedade inexistente.');
    }

    static function create(
        string $token,
        string $name,
        string $type,
        string $unit_type,
        float $quantity,
        int $is_deleted = 0,
        int $is_done = 0
    ) {
        $conn = self::_connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO items (token, name, type, unit_type, quantity) 
                values (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$token, $name, $type, $unit_type, $quantity]);
            $id = $conn->lastInsertId();
            return new Item(
                (int) $id, $token, $name, $type, $unit_type,
                $quantity, $is_deleted, $is_done
            );
        } catch (\PDOException $e) {
            throw new Exception('Falha durante o adição de um novo item.', 0, $e);
        }
    }

    static function getAll(string $token)
    {
        $conn = self::_connect();
        try {
            $stmt = $conn->prepare(
                'SELECT rowid AS id, token, name, type, unit_type, 
                quantity, is_deleted, is_done FROM items where token=?'
            );
            $stmt->execute([$token]);
            $results = $stmt->fetchAll(\PDO::FETCH_NAMED);
            $items = [];
            foreach ($results as $result) {
                $item = new Item(...$result);
                array_push($items, $item);
            }
            return $items;
        } catch (\PDOException $e) {
            throw new Exception(
                'Falha durante a busca de items de um usuário.', 0, $e
            );
        }
    }

    static function update(string $token, int $id, array $properties)
    {
        $conn = self::_connect();
        try {
            $conn->beginTransaction();
            foreach ($properties as $key => $value) {
                $stmt = $conn->prepare(
                    "UPDATE items set $key='$value' WHERE token=? AND rowid=?"
                );
                $stmt->execute([$token, $id]);
            }
            $stmt2 = $conn->prepare(
                "SELECT rowid, * FROM items WHERE token=? AND rowid=?"
            );
            $stmt2->execute([$token, $id]);
            $conn->commit();
            return $stmt2->fetch(\PDO::FETCH_NAMED);
        } catch (\PDOException $e) {
            $conn->rollBack();
            throw new Exception('Falha durante a atualização de um item.', 0, $e);
        }
    }

    static function delete(string $token, int $id)
    {
        $conn = self::_connect();
        try {
            $conn->beginTransaction();
            $stmt1 = $conn->prepare(
                "UPDATE items set is_deleted=1 WHERE token=? AND rowid=?"
            );
            $stmt1->execute([$token, $id]);
            $stmt2 = $conn->prepare(
                "SELECT rowid, * FROM items WHERE token=? AND rowid=?"
            );
            $stmt2->execute([$token, $id]);
            $conn->commit();
            return $stmt2->fetch(\PDO::FETCH_NAMED);
        } catch (\PDOException $e) {
            $conn->rollBack();
            throw new Exception('Falha durante a exclusão de um item.', 0, $e);
        }
    }

    static private function _connect()
    {
        $db = new \App\Database;
        return $db->getConnection();
    }
}
