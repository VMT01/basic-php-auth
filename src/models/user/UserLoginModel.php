<?php

namespace app\models\user;

use app\core\Application;
use app\core\database\SQLBuilder;
use app\entities\User;
use app\models\Model;

class UserLoginModel extends Model
{
    protected string $email;
    protected string $password;

    public function __construct()
    {
        $this->rules = [
            'email' => [
                self::RULES['REQUIRED'],
                self::RULES['EMAIL'],
            ],
            'password' => [
                self::RULES['REQUIRED'],
            ],
        ];
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function login(): int
    {
        $SQL = SQLBuilder::builder()
            ->select(['id', 'password'])
            ->table(User::TABLE_NAME)
            ->where(['email = :email'])
            ->build();
        $statement = Application::$DATABASE->prepare($SQL);
        $statement->execute(['email' => $this->email]);
        /** @var ?User $user */
        $user = $statement->fetchObject(User::class);

        if (!$user) throw new \Error('User does not exist with this email');
        if (!password_verify($this->password, $user->password)) throw new \Error('Password is incorrect');

        return $user->id;
    }
}
