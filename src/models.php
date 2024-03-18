<?php

namespace App;

use PDO;

class User
{
    private PDO $conn;
    function __construct()
    {
        $conn = $this->connect();
        $conn->beginTransaction();
        $conn->query('INSERT INTO users (id) values ($token)');
    }


    private function connect (){
        $db = new \App\Database();
        return $db->getConnection();
    }
}

