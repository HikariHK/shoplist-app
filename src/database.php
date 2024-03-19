<?php declare(strict_types=1);

namespace App;

require_once 'vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;
use Exception;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

final class Database
{
    private PDO $_conn;
    function __construct()
    {
        try {
            $filename = "database.sqlite3";
            if ($_ENV["PHP_ENV"] !== "production") {
                $filename = "testing.sqlite3";
            }
            $this->_conn = new PDO("sqlite:" . dirname(__DIR__) . "/$filename");
        } catch (\PDOException $e) {
            throw new Exception(
                "Não foi possível se conectar ao bando de dados.", 0, $e
            );
        }

        try {
            $this->_conn->beginTransaction();
            $this->_conn->query(
                "PRAGMA foreign_keys=1"
            );
            $this->_conn->query(
                "CREATE TABLE IF NOT EXISTS users (token TEXT, PRIMARY KEY (token))"
            );
            $this->_conn->query(
                "CREATE TABLE IF NOT EXISTS items (
                token TEXT NOT NULL, name TEXT NOT NULL, type TEXT NOT NULL, 
                unit_type TEXT NOT NULL, quantity FLOAT NOT NULL, 
                is_deleted INT DEFAULT 0,
                is_done INT DEFAULT 0, 
                FOREIGN KEY (token) REFERENCES users (id))"
            );
            $this->_conn->commit();
        } catch (\PDOException $e) {
            $this->_conn->rollBack();
            throw new Exception(
                "Não foi possível criar as tabelas no banco de dados.", 0, $e
            );
        }
    }
    function getConnection(): PDO
    {
        return $this->_conn;
    }
}
