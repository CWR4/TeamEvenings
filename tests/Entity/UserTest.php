<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 */
class UserTest extends TestCase
{
    /**
     * Test set / get username.
     */
    public function testCanSetAndGetUsername(): void
    {
        $user = new User();
        $this->assertEmpty($user->getUsername());
        $testUsername = 'CWR4';
        $user->setUsername($testUsername);
        $this->assertSame($testUsername, $user->getUsername());
    }

    /**
     * Test set / get roles.
     *
     * @ToDo finish test. Problem: Roles here won't be overwritten like in database.
     */
    public function testCanSetAndGetRoles(): void
    {
        $this->markTestIncomplete();
        $user = new User();
        $this->assertSame(['ROLE_USER'], $user->getRoles(), '1');
        $testRole = ['ROLE_ADMIN'];
        $user->setRoles($testRole);
        dump($user->getRoles());
        $this->assertSame($testRole, $user->getRoles(), '2');
    }

    /**
     * Test set / get password.
     */
    public function testCanSetAndGetPassword(): void
    {
        $user = new User();
        $this->assertEmpty($user->getPassword());
        $testPassword = '123456';
        $user->setPassword($testPassword);
        $this->assertSame($testPassword, $user->getPassword());
    }
}
