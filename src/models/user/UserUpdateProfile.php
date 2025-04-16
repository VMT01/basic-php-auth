<?php

namespace app\models\user;

use app\core\Application;
use app\core\database\SQLBuilder;
use app\entities\User;
use app\models\Model;

class UserUpdateProfile extends Model
{
    private static array $ATTRIBUTES = ['fullname', 'title', 'avatar', 'dob', 'phone', 'address'];
    private readonly User $user;
    private readonly string $email;
    private readonly string $username;

    protected string $fullname;
    protected string $title;
    /**
     * @var null|array{name: string,full_path: string,type: string,tmp_name: string,error: bool,size: int} $avatar
     */
    protected ?array $avatar = null;
    protected string $dob;
    protected string $phone;
    protected string $address;

    public function __construct()
    {
        $this->rules = [
            'fullname' => [
                [self::RULES['RANGE'], 'min' => 10, 'max' => 50],
            ],
            'title' => [
                [self::RULES['RANGE'], 'max' => 50],
            ],
            'avatar' => [
                self::RULES['IMAGE']
            ],
            'phone' => [
                self::RULES['PHONE'],
                [self::RULES['UNIQUE'], 'class' => User::class]
            ],
            'address' => [
                [self::RULES['RANGE'], 'max' => 300],
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
            if (!isset($this->{$attribute})) continue;

            switch ($attribute) {
                case 'dob':
                    if ($this->dob !== date('Y-m-d', strtotime($this->user->dob))) $values['dob'] = $this->dob;
                    break;
                case 'avatar':
                    if (empty($this->avatar)) break;
                    $avatar = '/media/' . $this->user->id . '-' . basename($this->avatar['name']);
                    $targetFile = Application::$ROOT_PATH . '/public' . $avatar;
                    move_uploaded_file($this->avatar['tmp_name'], $targetFile);
                    $values['avatar'] = $avatar;
                    break;
                default:
                    $userValue = $this->user->{$attribute} ?? null;
                    $currentValue = $this->{$attribute} ?? '';

                    if (empty($userValue) && $currentValue === '') break;
                    if ($currentValue === '') {
                        $values[$attribute] = null;
                    } else if (empty($userValue) || $userValue !== $currentValue) {
                        $values[$attribute] = $currentValue;  // Setting new or changed value
                    }
            }
        }

        if (empty($values)) throw new \Error('Không có trường nào để cập nhật');

        $SQL = SQLBuilder::builder()
            ->update(array_keys($values))
            ->table(User::TABLE_NAME)
            ->where(['id = :id'])
            ->build();

        $statement = Application::$DATABASE->prepare($SQL);
        $statement->execute(array_merge($values, ['id' => $this->user->id]));
    }
}
