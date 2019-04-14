<?php namespace frontend\tests\acceptance;
use Codeception\Step\Argument\PasswordArgument;
use frontend\tests\AcceptanceTester;

class LoginCest {
    public function _before(AcceptanceTester $I) {
    }

    // tests
    public function successAdminLogin(AcceptanceTester $I) {
        $I -> amOnPage('site/login');
        $I -> wait(1);
        $I -> fillField('LoginForm[username]', 'admin');
        $I -> wait(1);
        $I -> fillField('LoginForm[password]', new PasswordArgument('admin'));
        $I -> wait(1);
        $I -> click('Войти');
        $I -> wait(1);
        $I -> see('Панель администратора');
        $I -> wait(1);
    }

    public function incorrectPassAdminLogin(AcceptanceTester $I) {
        $I -> amOnPage('site/login');
        $I -> wait(1);
        $I -> fillField('LoginForm[username]', 'admin');
        $I -> wait(1);
        $I -> fillField('LoginForm[password]', new PasswordArgument('user'));
        $I -> wait(1);
        $I -> click('Войти');
        $I -> wait(1);
        $I -> dontSee('Панель администратора');
        $I -> wait(1);
    }
}
