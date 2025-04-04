<?php

require_once __DIR__ . "/../config/database.php";

require_once __DIR__ . "/../constants/routing.php";
require_once __DIR__ . "/../constants/session.php";

require_once __DIR__ . "/../entities/user.php";

require_once __DIR__ . "/../shared/routing.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    $username = isset($_POST["username"]) ? trim(htmlspecialchars($_POST["username"])) : null;
    if (empty($username)) {
        $errors[] = "Empty email provided";
    }

    $password = isset($_POST["password"]) ? trim(htmlspecialchars($_POST["password"])) : null;
    if (empty($password)) {
        $errors[] = "Empty password provided";
    }

    if (!empty($errors)) {
        $_SESSION[ERROR] = $errors;
        redirect(LOGIN);
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            throw new Exception("User not found");
        }

        $user = new User($user);
        if (!$user->verify_password($password)) {
            throw new Exception("Invalid username or password.");
        }

        session_regenerate_id(true);
        $_SESSION[USER] = $user;
        redirect(PROFILE);
    } catch (Throwable $e) {
        $_SESSION[ERROR] = $e->getMessage();
        redirect(LOGIN);
    }
}
