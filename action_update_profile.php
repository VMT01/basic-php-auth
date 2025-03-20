<?php

require_once "./constants/routing.php";
require_once "./shared/routing.php";
require_once  "./database.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST["username"]) ? trim(htmlspecialchars($_POST["username"])) : null;
    $email = isset($_POST["email"]) ? trim(htmlspecialchars($_POST["email"])) : null;
    $user_id = $_SESSION["user"]["id"];
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
            $_SESSION["user"]["username"] = $username;
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
            $_SESSION["user"]["email"] = $email;
            $is_updated = true;
        }

        $pdo->commit();

        if ($is_updated) {
            $_SESSION["success"] = "Profile info updated successfully";
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $_SESSION["error"] = $e->getMessage();
    }

    redirect(PROFILE);
}
