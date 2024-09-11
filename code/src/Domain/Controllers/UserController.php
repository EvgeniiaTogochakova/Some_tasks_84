<?php

namespace Geekbrains\Application1\Domain\Controllers;

use Geekbrains\Application1\Application\Render;
use Geekbrains\Application1\Domain\Models\User;

class  UserController
{

    public function actionIndex(): string
    {
        $users = User::getAllUsersFromStorage();

        $render = new Render();

        if (!$users) {
            return $render->renderPage(
                'user-empty.twig',
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список пуст или не найден"
                ]);
        } else {
            return $render->renderPage(
                'user-index.twig',
                [
                    'title' => 'Список пользователей в хранилище',
                    'users' => $users
                ]);
        }
    }

    public function actionSave(): string
    {
        if (User::validateRequestData()) {
            $user = new User();
            $user->setParamsFromRequestData();
            $user->saveToStorage();

            $render = new Render();

            return $render->renderPage(
                'user-created.twig',
                [
                    'title' => 'Пользователь создан',
                    'message' => "Создан пользователь " . $user->getUserName() . " " . $user->getUserLastName()
                ]);
        } else {
            throw new \Exception("Переданные данные некорректны");
        }
    }

    public function actionShow(): string
    {
        $id = $this->checkId();

        if (User::exists($id)) {
            $user = User::getUserFromStorageById($id);
            $render = new Render();

            return $render->renderPage(
                'user-page.twig',
                [
                    'title' => 'Пользователь',
                    'user' => $user
                ]);

        } else {
            throw new \Exception('Пользователь с таким id не существует');
        }
    }

    public function actionUpdate(): string
    {
        $id = $this->checkId();
        if (User::exists($id)) {
            $user = User::getUserFromStorageById($id);

            $arrayData = [];
            $arrayData['id_user'] = $_GET['id'];
            $arrayData['user_name'] = $_GET['name'] ?? $user->getUserName();
            $arrayData['user_lastname'] = $_GET['lastname'] ?? $user->getUserLastName();

            $user->updateUser($arrayData);
        } else {
            throw new \Exception("Пользователя с id={$_GET['id']} не существует");
        }

        $render = new Render();
        return $render->renderPage(
            'user-created.twig',
            [
                'title' => 'Данные пользователя обновлены',
                'message' => "Обновлены данные пользователя c id = " . $user->getUserId()
            ]);
    }

    public function actionDelete(): string
    {
        $id = $this->checkId();
        if (User::exists($id)) {
            User::deleteFromStorage($id);

            $render = new Render();

            return $render->renderPage(
                'user-removed.twig',
                ['id' => $id]
            );
        } else {
            throw new \Exception("Пользователь с таким id не существует");
        }
    }

    public function checkId(): int
    {
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
        return $id;
    }
}