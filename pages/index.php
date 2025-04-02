<?php

require_once __DIR__ . "/../shared/routing.php";

session_start();
if (isset($_SESSION["user"])) {
    redirect("profile");
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Starter I | Home</title>
</head>

<body>
    <header style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Index Page</h2>
        <div>
            <button><a href="login">Login</a></button>
            <button><a href="register">Register</a></button>
        </div>
    </header>
</body>

</html>
