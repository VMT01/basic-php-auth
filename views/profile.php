<?php

require_once __DIR__ . "/../constants/routing.php";
require_once __DIR__ . "/../constants/session.php";

require_once __DIR__ . '/../entities/user.php';

require_once __DIR__ . "/../shared/routing.php";

session_start();

$user = $_SESSION[USER];
if (!$user) {
    redirect(LOGIN);
}

$error = $_SESSION[ERROR] ?? null;
$status = $_SESSION[SUCCESS] ?? null;
unset($_SESSION[ERROR]);
unset($_SESSION[SUCCESS]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>

<body>
    <header style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Profile Page</h2>
        <button><a href="<?php echo ACTION_LOGOUT ?>">Logout</a></button>
    </header>
    <hr>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif (isset($status)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($status); ?></p>
    <?php endif; ?>

    <p>Username: <?php echo htmlspecialchars($user->username()); ?></p>
    <p>Email: <?php echo htmlspecialchars($user->email()); ?></p>

    <section>
        <form action="<?php echo ACTION_UPDATE_PROFILE ?>" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="email" name="email" placeholder="Email">
            <button type="submit">Update</button>
        </form>
    </section>
</body>

</html>
