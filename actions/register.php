<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../constants/routing.php";
require_once __DIR__ . "/../constants/session.php.php";
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

        // TODO: We can map this into class instead of native var
        $existed_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existed_user) {
            if ($existed_user[USERNAME] === $username) {
                $errors[] = "Account with this username existed.";
            }
            if ($existed_user[EMAIL] === $email) {
                $errors[] = "Account with this email existed.";
            }

            $_SESSION[ERROR] = $errors;
            redirect(REGISTER);
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        $_SESSION[SUCCESS] = "Register success. Please login to access your profile.";
        redirect(LOGIN);
    } catch (PDOException $e) {
        $_SESSION[ERROR] = "An error occurred while trying to register: " . $e->getMessage() . ".";
        redirect(REGISTER);
    }
}
