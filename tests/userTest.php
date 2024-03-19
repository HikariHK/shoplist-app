<?php declare(strict_types=1);

namespace App;

require_once 'src/models/user.php';

use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    function testCreatesAnUser()
    {
        $user = User::create((string) time());
        $this->assertInstanceOf(User::class, $user);
    }
}
