<?php

namespace app\models\user;

use app\core\Application;
use app\core\database\SQLBuilder;
use app\entities\User;
use app\models\Model;
use RuntimeException;

class UserUpdateProfile extends Model
{
    private static array $ATTRIBUTES = ['fullname', 'title', 'avatar', 'dob', 'phone', 'address'];
    private readonly User $user;
    private readonly string $email;
    private readonly string $username;
    /**
     * @var array{name: string,full_path: string,type: string,tmp_name: string,error: bool,size: int} $avatarPriv
     */
    private array $avatarPriv;

    protected string $fullname = '';
    protected string $title = '';
    protected string $avatar = '';
    protected string $dob = '';
    protected string $phone = '';
    protected string $address = '';

    public function __construct()
    {
        $this->rules = [
            'fullname' => [
                [self::RULES['MIN'], 'min' => 10],
                [self::RULES['MAX'], 'max' => 50],
            ],
            'title' => [
                [self::RULES['MAX'], 'max' => 50],
            ],
            'phone' => [
                self::RULES['PHONE'],
                [self::RULES['UNIQUE'], 'class' => User::class]
            ],
            'address' => [
                [self::RULES['MAX'], 'max' => 500],
            ],
        ];

        $this->user = Application::$SESSION->user;
        $this->email = $this->user->email;
        $this->username = $this->user->username;
    }

    public function __get(string $name)
    {
        return $this->{$name};
    }

    public function __set(string $name, mixed $value): void
    {
        $this->{$name} = $value;
    }

    public function updateProfile()
    {
        /** @var array<string, string> $values */
        $values = [];

        foreach (self::$ATTRIBUTES as $attribute) {
            if (empty($this->{$attribute}) || $this->{$attribute} === '' || $this->{$attribute} === []) continue;

            switch ($attribute) {
                case 'dob':
                    if ($this->dob !== date('Y-m-d', strtotime($this->user->dob))) $values['dob'] = $this->dob;
                    break;
                case 'avatar':
                    $avatar = '/media/' . $this->user->id . '-' . basename($this->avatarPriv['name']);
                    $targetFile = Application::$ROOT_PATH . '/public' . $avatar;
                    if (!move_uploaded_file($this->avatarPriv['tmp_name'], $targetFile)) throw new RuntimeException("Failed to move uploaded avatar");
                    $values['avatar'] = $avatar;
                    break;
                default:
                    if ($this->user->{$attribute} !== $this->{$attribute}) $values[$attribute] = $this->{$attribute};
                    break;
            }
        }

        if (empty($values)) throw new \Error('No field to update');

        $SQL = SQLBuilder::builder()
            ->update(array_keys($values))
            ->table(User::TABLE_NAME)
            ->where(['id = :id'])
            ->build();

        $statement = Application::$DATABASE->prepare($SQL);
        $statement->execute(array_merge($values, ['id' => $this->user->id]));
    }
}
