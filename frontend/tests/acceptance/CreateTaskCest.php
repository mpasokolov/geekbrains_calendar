<?php namespace frontend\tests\acceptance;
use frontend\tests\AcceptanceTester;
use WebDriverKeys;

class CreateTaskCest
{
    public function _before(AcceptanceTester $I) {
        $loginTest = new LoginCest();
        $loginTest -> successAdminLogin($I);
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I -> wait(1);
        $I -> click('Панель администратора');
        $I -> wait(1);
        $I -> click('Управление задачами');
        $I -> wait(1);
        $I -> click('Создать задачу');
        $I -> wait(1);
        $I -> fillField('Tasks[name]', 'CreateTaskCest');
        $I -> wait(1);
        $I -> selectOption('Tasks[id_team]', 'backend');
        $I -> wait(1);
        $I -> fillField('Tasks[deadline]', '13.08.2020');
        $I -> wait(1);
        $I -> fillField('Tasks[description]', 'test');
        $I -> wait(1);
        $I -> click('Создать задачу');
        $I -> wait(1);
        $I -> see('Задача успешно создана!');
        $I -> wait(1);
        $I -> click('К списку задач');
        $I -> wait(1);
        $I -> fillField('TasksSearch[name]', 'CreateTaskCest');
        $I -> wait(1);
        $I -> pressKey('.form-control:nth-child(1)',WebDriverKeys::ENTER);
        $I -> wait(1);
        $I -> click('.glyphicon-trash');
        $I -> wait(1);
        $I -> acceptPopup();
        $I -> wait(10);
        $I -> see('Задача успешно удалена');
    }
}
