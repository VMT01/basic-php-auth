<?php

namespace app\models\user;

use app\core\Application;
use app\core\database\SQLBuilder;
use app\entities\User;
use app\models\Model;

class UserUpdateAvatar extends Model
{
    private readonly User $user;

    /**
     * @var array{
     *      name: string,
     *      full_path: string,
     *      type: string,
     *      tmp_name: string,
     *      error: bool,
     *      size: int
     *  } $avatar
     */
    protected array $avatar;

    public function __construct()
    {
        $this->rules = [
            'avatar' => [
                self::RULES['REQUIRED'],
                self::RULES['IMAGE']
            ]
        ];

        $this->user = Application::$SESSION->user;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function updateAvatar()
    {
        $avatar = '/media/' . $this->user->id . '-' . basename($this->avatar['name']);
        $targetFile = Application::$ROOT_PATH . '/public' . $avatar;
        move_uploaded_file($this->avatar['tmp_name'], $targetFile);

        /** @var array<string, string> $values */
        $values = ['avatar' => $avatar];

        $SQL = SQLBuilder::builder()
            ->update(array_keys($values))
            ->table(User::TABLE_NAME)
            ->where(['id = :id'])
            ->build();
        $statement = Application::$DATABASE->prepare($SQL);
        $statement->execute(array_merge($values, ['id' => $this->user->id]));
    }
}
