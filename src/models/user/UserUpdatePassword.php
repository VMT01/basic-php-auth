<?php

namespace app\models\user;

use app\core\Application;
use app\core\database\SQLBuilder;
use app\entities\User;
use app\models\Model;

class UserUpdatePassword extends Model
{
    private readonly User $user;

    protected string $current_password = '';
    protected string $new_password = '';
    protected string $confirm_password = '';

    public function __construct()
    {
        $this->rules = [
            'current_password' => [
                self::RULES['REQUIRED'],
            ],
            'new_password' => [
                self::RULES['REQUIRED'],
                self::RULES['COMPLEX'],
                [self::RULES['MIN'], 'min' => 6],
                [self::RULES['MAX'], 'max' => 20],
            ],
            'password_confirm' => [
                self::RULES['REQUIRED'],
                [self::RULES['MATCH'], 'match' => 'new_password']
            ],
        ];
        $this->user = Application::$SESSION->user;
    }

    public function __get($name): string
    {
        return $this->{$name};
    }

    public function updatePassword()
    {
        $password = password_hash($this->new_password, PASSWORD_DEFAULT);
        $values = ['password' => $password];

        $SQL = SQLBuilder::builder()
            ->update(array_keys($values))
            ->table(User::TABLE_NAME)
            ->where(['id = :id'])
            ->build();
        $statement = Application::$DATABASE->prepare($SQL);
        $statement->execute(array_merge($values, ['id' => $this->user->id]));
    }
}
