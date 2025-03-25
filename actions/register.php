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
        $errors[] = "Empty username provided";
    }

    $email = isset($_POST["email"]) ? trim(htmlspecialchars($_POST["email"])) : null;
    if (empty($email)) {
        $errors[] = "Empty email provided";
    }

    $password = isset($_POST["password"]) ? trim(htmlspecialchars($_POST["password"])) : null;
    if (empty($password)) {
        $errors[] = "Empty password provided";
    }

    if (!empty($errors)) {
        $_SESSION[ERROR] = $errors;
        redirect(REGISTER);
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user = new User($user);
            if ($user->username() === $username) {
                throw new Error("Account with this username existed.");
            }

            if ($user->email() === $email) {
                throw new Error("Account with this email existed.");
            }
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        $_SESSION[SUCCESS] = "Register success. Please login to access your profile.";
        redirect(LOGIN);
    } catch (Throwable $e) {
        $_SESSION[ERROR] = $e->getMessage();
        redirect(REGISTER);
    }
}
