<?php

require_once __DIR__ . "/../constants/routing.php";
require_once __DIR__ . "/../constants/session.php";

require_once __DIR__ . "/../shared/routing.php";

session_start();

if (isset($_SESSION[USER])) {
    redirect(PROFILE);
}

$error = $_SESSION[ERROR] ?? null;
unset($_SESSION[ERROR]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <header style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Register Page</h2>
        <button><a href="<?php echo LOGIN ?>">Login</a></button>
    </header>
    <hr>

    <?php if (!empty($error)): ?>
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
    <?php endif; ?>

    <form action="<?php echo ACTION_REGISTER ?>" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>
</body>

</html>
