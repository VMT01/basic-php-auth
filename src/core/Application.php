<?php

namespace app\core;

use app\controllers\ErrorController;
use app\core\database\Database;
use app\core\database\SQLBuilder;
use app\entities\User;

class Application
{
    public static string $ROOT_PATH;
    public static Database $DATABASE;
    public static Request $REQUEST;
    public static Response $RESPONSE;
    public static Session $SESSION;

    private Router $router;

    public function __construct(array $config)
    {
        self::$ROOT_PATH = $config['root_path'];

        self::$DATABASE = new Database($config['database']);
        self::$REQUEST = new Request();
        self::$RESPONSE = new Response();
        self::$SESSION = new Session();

        $userId = self::$SESSION->get('user');
        if ($userId) {
            $SQL = SQLBuilder::builder()
                ->select()
                ->table(User::TABLE_NAME)
                ->where(['id = :id'])
                ->build();
            $statement = Application::$DATABASE->prepare($SQL);
            $statement->execute(['id' => $userId]);
            /** @var ?User $user */
            $user = $statement->fetchObject(User::class);
            self::$SESSION->login($user);
        }

        $this->router = new Router();
    }

    /**
     * @param array{class: class,action: callable,middlewares?:array<int,class>} $callback
     */
    public function get(string $path, array $callback): self
    {
        $this->router->assign('get', $path, (object)$callback);
        return $this;
    }

    /**
     * @param array{class: class,action: callable,middlewares?:array<int,class>} $callback
     */
    public function post(string $path, array $callback): self
    {
        $this->router->assign('post', $path, (object)$callback);
        return $this;
    }

    public function run(): void
    {
        try {
            $this->router->resolve();
        } catch (\Exception $e) {
            Application::$RESPONSE->setStatusCode($e->getCode());
            call_user_func_array([ErrorController::getInstance(), "_error"], [$e]);
        }
    }
}
