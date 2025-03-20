<?php

require_once "./constants/routing.php";
require_once "./shared/routing.php";

session_start();

$user = $_SESSION["user"];
if (!$user) {
    redirect(LOGIN);
}


$error = $_SESSION["error"];
unset($_SESSION["error"]);

$status = $_SESSION["status"];
unset($_SESSION["status"]);

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
        <button><a href="<?php echo LOGOUT ?>">Logout</a></button>
    </header>
    <hr>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif (isset($status)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($status); ?></p>
    <?php endif; ?>

    <p>Username: <?php echo htmlspecialchars($user["username"]); ?></p>
    <p>Email: <?php echo htmlspecialchars($user["email"]); ?></p>

    <section>
        <form action="<?php echo ACTION_UPDATE_PROFILE ?>" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="email" name="email" placeholder="Email">
            <button type="submit">Update</button>
        </form>
    </section>
</body>

</html>
