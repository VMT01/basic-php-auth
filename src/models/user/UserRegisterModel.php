<?php

namespace app\models\user;

use app\core\Application;
use app\core\database\SQLBuilder;
use app\entities\User;
use app\models\Model;

class UserRegisterModel extends Model
{
    protected string $fullname;
    protected string $dob;
    protected string $email;
    protected ?string $phone;
    protected string $password;
    protected string $password_confirm;

    public function __construct()
    {
        $this->rules = [
            'fullname' => [
                self::RULES['REQUIRED'],
                [self::RULES['MIN'], 'min' => 10],
                [self::RULES['MAX'], 'max' => 50],
            ],
            'dob' => [
                self::RULES['REQUIRED']
            ],
            'email' => [
                self::RULES['REQUIRED'],
                self::RULES['EMAIL'],
                [self::RULES['UNIQUE'], 'class' => User::class]
            ],
            'phone' => [
                self::RULES['PHONE'],
                [self::RULES['UNIQUE'], 'class' => User::class]
            ],
            'password' => [
                self::RULES['REQUIRED'],
                self::RULES['COMPLEX'],
                [self::RULES['MIN'], 'min' => 6],
                [self::RULES['MAX'], 'max' => 20],
            ],
            'password_confirm' => [
                self::RULES['REQUIRED'],
                [self::RULES['MATCH'], 'match' => 'password']
            ],
        ];
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function register(): void
    {
        $fields = ['email', 'fullname', 'password', 'username', 'dob'];
        $values = [
            'email' => $this->email,
            'fullname' => $this->fullname,
            'password' => password_hash($this->password, PASSWORD_DEFAULT),
            'username' => $this->buildUsername(),
            'dob' => $this->dob,
        ];
        if ($this->phone) {
            $fields[] = 'phone';
            $values['phone'] = $this->phone;
        }

        $SQL = SQLBuilder::builder()
            ->insert($fields)
            ->table(User::TABLE_NAME)
            ->where(array_map(fn(string $field) => ":$field", $fields))
            ->build();

        $statement = Application::$DATABASE->prepare($SQL);
        $statement->execute($values);
    }

    private function buildUsername(): string
    {
        $nameParts = explode(' ', strtolower($this->normalize($this->fullname)));
        $username = '@' . end($nameParts) . $nameParts[0];

        $SQL = SQLBuilder::builder()
            ->select(['id', 'username'])
            ->table(User::TABLE_NAME)
            ->where(['username LIKE :username'])
            ->filter(['order_by' => ['direction' => 'DESC', 'key' => 'id']])
            ->build();

        $statement = Application::$DATABASE->prepare($SQL);
        $statement->execute(['username' => "$username%"]);
        $existedUser = $statement->fetchObject(User::class);

        if ($existedUser) {
            if (preg_match('/(\d+)$/', $existedUser->username, $matches)) {
                $username .= ((int)$matches[1] + 1);
            } else {
                $username .= 1;
            }
        }

        return $username;
    }
}
