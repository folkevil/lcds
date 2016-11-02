<?php

namespace tests\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        expect_that($user = User::findIdentity('admin'));
        expect($user->username)->equals('admin');
        expect_not($user->fromLdap);

        expect_not(User::findIdentity('non-existing-user'));
    }

    /**
     * @depends testFindUserById
     */
    public function testValidateUser($user)
    {
        $user = User::findIdentity('admin');

        expect_that($user->validatePassword('admin'));
        expect_not($user->validatePassword('123456'));
    }

    public function testCreateUser()
    {
        $user = User::create('test', 'test');
        expect_that($user);

        $user = User::findIdentity('test');
        expect_that($user);
        expect_that($user->authkey);
        expect_that($user->access_token);
        expect_not($user->fromLdap);
        expect_that($user->validatePassword('test'));
    }

    public function testAuthenticate()
    {
        $user = User::findIdentity('admin');

        expect_that($user->authenticate('admin'));
    }

    public function testFindInLdap()
    {
        // We cannot test this method, only proper error return
        $user = User::findInLdap('non-existing-ldap-user');

        expect_not($user);
    }
}
