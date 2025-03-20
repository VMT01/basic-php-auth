<?php

require_once "./constants/routing.php";
require_once "./shared/routing.php";

session_start();

if (isset($_SESSION["user"])) {
    redirect(PROFILE);
}

$error = $_SESSION["error"];
unset($_SESSION["error"]);

$success = $_SESSION["success"];
unset($_SESSION["success"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <header style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Login Page</h2>
        <button><a href="<?php echo REGISTER ?>">Register</a></button>
    </header>
    <hr>

    <?php if (isset($error)): ?>
        <p style="color: red;">
            <?php
            if (is_array($error)) {
                foreach ($error as $err) {
                    echo htmlspecialchars($err) . "<br>";
                }
            } else {
                echo htmlspecialchars($error);
            };
?>
        </p>
    <?php elseif (isset($success)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form action="<?php echo ACTION_LOGIN ?>" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>

</html>
