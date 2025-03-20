<?php

require_once "./constants/routing.php";
require_once "./shared/routing.php";
require_once  "./database.php";

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
        $_SESSION["error"] = $errors;
        $_SESSION["form_data"] = [
            "username" => $username,
            "email" => $email
        ];
        redirect(REGISTER);
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existed_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existed_user) {
            if ($existed_user["username"] === $username) {
                $errors[] = "Account with this username existed.";
            }
            if ($existed_user["email"] === $email) {
                $errors[] = "Account with this email existed.";
            }

            $_SESSION["error"] = $errors;
            $_SESSION["form_data"] = [
                "username" => $username,
                "email" => $email
            ];
            redirect(REGISTER);
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        $_SESSION["success"] = "Register success. Please login to access your profile.";
        redirect(LOGIN);
    } catch (PDOException $e) {
        $_SESSION["error"] = "An error occurred while trying to register: " . $e->getMessage() . ".";
        $_SESSION["form_data"] = [
            "username" => $username,
            "email" => $email
        ];
        redirect(REGISTER);
    }
}
