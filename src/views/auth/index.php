<?php

use app\core\Application;

$flashSuccess = Application::$SESSION->getFlash('success');
$flashError = Application::$SESSION->getFlash('error');

?>

<!DOCTYPE HTML>
<html lang="vi">

<head>
    <title>Base Account - Auth</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://static-gcdn.basecdn.net/account/image/fav.png" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:500,400,300,400italic,700,700italic,400italic,300italic&subset=vietnamese,latin">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/css/auth.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="/js/PasswordVisibilityManager.js"></script>
    <script src="/js/FlashMessage.js"></script>
    <script src="/js/auth.js"></script>
</head>

<body>
    <NOSCRIPT>Javascript must be enabled to process the website.</NOSCRIPT>

    <div class="bg-img"></div>
    <div class="container">
        <div class="wrapper">
            <div class="auth-logo"><a href="https://base.vn/"><img src="https://share-gcdn.basecdn.net/brand/logo.full.png"></a></div>
            {{content}}
        </div>
    </div>

    <script>
        const flash = new FlashMessage();
        <?php if ($flashSuccess): ?>
            flash.show('<?php echo $flashSuccess ?>');
        <?php elseif ($flashError): ?>
            flash.show('<?php echo json_encode($flashError) ?>', 'error');
        <?php endif; ?>
    </script>
</body>
