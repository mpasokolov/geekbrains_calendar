<?php namespace frontend\tests;

use common\models\User;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;
    
    protected function _before() {
    }

    protected function _after() {
    }

    public function testValidation() {
        $user = new User();

        $user->setName(null);
        $this->assertFalse($user->validate(['username']));

        $user->setName('toolooooooooooooooooooooooooongnaaaaaaaaaaaaaaaameeeetoolooooooooooooooooooooooooongnaaaaaaaaaaaaaaaameeee');
        $this->assertFalse($user->validate(['username']));

        $user->setName('davert');
        $this->assertTrue($user->validate(['username']));
    }
}