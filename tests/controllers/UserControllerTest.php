<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../controllers/UserController.php';

class UserControllerTest extends TestCase
{
    public function test_forgot_password(): void
    {
        $controller = new controllers\UserController();
        $actual = $controller->forgot_password($args);
        $expected = 'lalal';
        $this->assertEquals($expected, $actual, "Welp... forgotPassword test on line " . (__LINE__ - 2) . ' failed miserably.');
    }
}