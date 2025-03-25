<?php

require_once __DIR__ . "/../config/database.php";

require_once __DIR__ . "/../constants/routing.php";
require_once __DIR__ . "/../constants/session.php";

require_once __DIR__ . '/../entities/user.php';

require_once __DIR__ . "/../shared/routing.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST["username"]) ? trim(htmlspecialchars($_POST["username"])) : null;
    $email = isset($_POST["email"]) ? trim(htmlspecialchars($_POST["email"])) : null;
    $user_id = $_SESSION[USER]->id();
    $is_updated = false;

    try {
        $pdo->beginTransaction();

        if (!empty($username)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? and id != ?");
            $stmt->execute([$username, $user_id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("User with this username existed.");
            }

            $stmt = $pdo->prepare("UPDATE users SET username = ? where id = ?");
            $stmt->execute([$username, $user_id]);
            $_SESSION[USER]->set_username($username);
            $is_updated = true;
        }

        if (!empty($email)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? and id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("User with this email existed.");
            }

            $stmt = $pdo->prepare("UPDATE users SET email = ? where id = ?");
            $stmt->execute([$email, $user_id]);
            $_SESSION[USER]->set_email($email);
            $is_updated = true;
        }

        $pdo->commit();

        if ($is_updated) {
            $_SESSION[SUCCESS] = "Profile info updated successfully";
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $_SESSION[ERROR] = $e->getMessage();
    } finally {
        redirect(PROFILE);
    }
}
