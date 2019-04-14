<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-01-08
 * Time: 23:29
 */

namespace console\controllers;

use console\rbac\rules\AuthorRule;
use yii\console\Controller;
use yii\console\ExitCode;

class RbacController extends Controller {
    /**
     * @throws \Exception
     */
    public function actionIndex() {
        $auth = \Yii::$app -> authManager;

        $auth -> removeAll();

        $admin = $auth -> createRole('admin');
        $teamLead = $auth -> createRole('teamLead');
        $user = $auth -> createRole('user');
        $guest = $auth -> createRole('guest');

        try {
            $auth -> add($admin);
            $auth -> add($teamLead);
            $auth -> add($user);
            $auth -> add($guest);
        } catch (\Exception $exception) {
            echo 'Произошла ошибка при добавлении роли' . PHP_EOL;
        }

        //Создаем разрешения
        $createTask = $auth -> createPermission('createTask');
        $createTask -> description = 'Создание новой задачи';

        $finishTask = $auth -> createPermission('finishTask');
        $finishTask -> description = 'Завершение задачи';

        $updateTask = $auth -> createPermission('updateTask');
        $updateTask -> description = 'Редактирование задачи';

        $deleteTask = $auth -> createPermission('deleteTask');
        $deleteTask -> description = 'Завершение задачи';

        $viewTask = $auth -> createPermission('viewTask');
        $viewTask -> description = 'Просмотр таска';

        $viewAllTask = $auth -> createPermission('viewAllTasks');
        $viewAllTask -> description = 'Просмотр всех тасков';

        $deleteUserFromTeam = $auth -> createPermission('deleteUserFromTeam');
        $deleteUserFromTeam -> description = 'Удаление участника команды';

        $disbandTeam = $auth -> createPermission('disbandTeam');
        $disbandTeam -> description = 'Расформирование команды';

        $inviteInTeam =  $auth -> createPermission('inviteInTeam');
        $inviteInTeam -> description = 'Приглашение пользователя в команду';

        $manageTeamTasks =  $auth -> createPermission('manageTeamTasks');
        $manageTeamTasks -> description = 'Управление задачами команды';

        $adminAccess = $auth -> createPermission('adminAccess');
        $adminAccess -> description = 'Максимальные права';

        try {
            $auth -> add($adminAccess);
            $auth -> add($createTask);
            $auth -> add($finishTask);
            $auth -> add($updateTask);
            $auth -> add($deleteTask);
            $auth -> add($viewTask);
            $auth -> add($viewAllTask);
            $auth -> add($deleteUserFromTeam);
            $auth -> add($disbandTeam);
            $auth -> add($inviteInTeam);
            $auth -> add($manageTeamTasks);
        } catch (\Exception $exception) {
            echo 'Произошла ошибка при добавлении разрешений' . PHP_EOL;
        }

        //Работаем с правилами наследования
        try {
            $auth -> addChild($user, $guest);
            $auth -> addChild($user, $finishTask);
            $auth -> addChild($user, $viewTask);
            $auth -> addChild($user, $viewAllTask);
            $auth -> addChild($teamLead, $user);
            $auth -> addChild($teamLead, $createTask);
            $auth -> addChild($teamLead, $updateTask);
            $auth -> addChild($teamLead, $deleteTask);
            $auth -> addChild($teamLead, $deleteUserFromTeam);
            $auth -> addChild($teamLead, $disbandTeam);
            $auth -> addChild($teamLead, $inviteInTeam);
            $auth -> addChild($teamLead, $manageTeamTasks);
            $auth -> addChild($admin, $teamLead);
            $auth -> addChild($admin, $adminAccess);
        } catch (\Exception $exception) {
            echo 'Произошла ошибки при добавлении разрешений ролям' . PHP_EOL;
        }

        //Добавляем роли пользователям
        try {
            $auth -> assign($admin, 1);
            $auth -> assign($teamLead, 2);
            $auth -> assign($user, 3);
            $auth -> assign($guest, 4);
        } catch (\Exception $exception) {
            echo 'Роли для конкретных пользователей назначены неверно' . PHP_EOL;
        }

        ExitCode::OK;
    }
}