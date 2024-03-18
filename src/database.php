<?php

namespace App;

use PDO, PDOException;
use Exception;

class Database
{
    private PDO $conn;
    function __construct()
    {
        try {
            $this->conn = new PDO('sqlite:' . dirname(__DIR__) . '/database.sqlite3');
        } catch (PDOException $e) {
            throw new Exception('Não foi possível se conectar ao bando de dados.', 0, $e);
        }

        try {
            $this->conn->beginTransaction();
            $this->conn->query(
                "PRAGMA foreign_keys=1"
            );
            $this->conn->query(
                "CREATE TABLE IF NOT EXISTS users (id TEXT, 
        PRIMARY KEY (id))"
            );
            $this->conn->query(
                "CREATE TABLE IF NOT EXISTS items (
        user_id TEXT NOT NULL, name TEXT NOT NULL, type TEXT NOT NULL, 
        unit_type TEXT NOT NULL, quantity TEXT NOT NULL, is_deleted BOOLEAN DEFAULT FALSE, 
        FOREIGN KEY (user_id) REFERENCES users (id))"
            );
            $this->conn->commit();
        } catch (PDOException $e) {
            throw new Exception('Não foi possível criar as tabelas no banco de dados.', 0, $e);
        }
    }
    function getConnection(): PDO
    {
        return $this->conn;
    }
}



