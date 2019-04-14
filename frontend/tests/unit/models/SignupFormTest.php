<?php
namespace frontend\tests\unit\models;

use common\fixtures\UserFixture;
use common\models\User;

class SignupFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    public function _before()
    {
        /*
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ]);
        */
    }

    public function testCorrectSignup()
    {
        $model = new User([
            'username' => 'some_username',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
            'password_repeat' => 'some_password'
        ]);

        expect($model) -> isInstanceOf('common\models\User');

        $model -> save();
        $newUser = User::findByUsername('some_username');

        expect($newUser -> username) -> equals('some_username');
        expect($newUser -> email) -> equals('some_email@example.com');
        expect($newUser -> validatePassword('some_password'))->true();
    }

    public function testNotCorrectSignup()
    {
        $model = new User([
            'username' => 'some_username',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
            'password_repeat' => 'some_password'
        ]);

        $model -> save();

        $model = new User([
            'username' => 'some_username',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
            'password_repeat' => 'some_password'
        ]);

        $model -> save();

        expect_that($model->getErrors('username'));
        expect_that($model->getErrors('email'));

        expect($model->getFirstError('username'))
            -> equals('Пользователь с данным логином уже существует!');
        expect($model->getFirstError('email'))
            -> equals('Пользователь с данным email уже зарегистрирован!');
    }
}
