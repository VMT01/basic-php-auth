<!DOCTYPE HTML>

<head>
    <title>Error</title>
</head>

<body>
    <h3><?php echo $exception->getCode() ?> - <?php echo $exception->getMessage() ?></h3>
    <button><a href="login">Back to login page</a></button>
</body>
