<?php declare(strict_types=1);

namespace App;

require_once 'src/models/user.php';

use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    function testCreatesAnUser()
    {
        $token = (string) time();
        $user = User::create($token);
        $this->assertEquals($user->_token, $token);
    }
}
