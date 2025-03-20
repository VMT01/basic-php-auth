<?php

require_once "./constants/routing.php";
require_once "./shared/routing.php";
require_once  "./database.php";

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
        $_SESSION["error"] = $errors;
        $_SESSION["form_data"] = [
            "username" => $username,
        ];
        redirect(LOGIN);
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION["error"] = "User not found";
            $_SESSION["form_data"] = [
                "username" => $username,
            ];
            redirect(LOGIN);
        }

        if (!password_verify($password, $user["password"])) {
            $_SESSION["error"] = "Invalid username or password.";
            $_SESSION["form_data"] = [
                "username" => $username,
            ];
            redirect(LOGIN);
        }

        session_regenerate_id(true);
        $_SESSION["user"] = $user;
        redirect(PROFILE);
    } catch (PDOException $e) {
        $_SESSION["error"] = "An error occurred while trying to login: " . $err->getMessage() . ".";
        $_SESSION["form_data"] = [
            "username" => $username,
        ];
        redirect(LOGIN);
    }
}
