<?php declare(strict_types=1);

namespace App;

require_once 'src/database.php';

use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
    function testGetsAConnection()
    {
        $db = new Database;
        $conn = $db->getConnection();
        $this->assertInstanceOf(\PDO::class, $conn);
    }

    function testTablesCreatedSuccessfully()
    {
        $db = new Database;
        $conn = $db->getConnection();
        $conn->beginTransaction();
        $conn->query('SELECT token FROM users');
        $conn->query(
            'SELECT token, name, type, unit_type, 
        quantity, is_deleted, is_done FROM items'
        );
        $result = $conn->commit();
        $this->assertEquals(true, $result);
    }

}


/*

int $id,
string $token,
string $name,
string $type,
string $unit_type,
float $quantity,
bool $is_deleted,
bool $is_done

*/
