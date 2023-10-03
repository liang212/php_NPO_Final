<?php
    ob_start();
    include('menu.php');
    date_default_timezone_set("Asia/Taipei");
?>
<html>
    <head>
        <title>首頁 | 機咪與恩波 Jimmy x NPO Channel</title>
        <style>
            img {margin:1.7% 0 0;}
        </style>
        <meta name = 'description' content = '機咪與恩波 Jimmy x NPO Channel 官方網站。以漫畫講公益！'/>
        <meta name = 'keywords' content = 'Jimmy, NPO, robot, cat, robotandcats, 機咪與恩波, Jimmy x NPO Channel'/>
    </head>
    <body align = center>
        <img width = 60% src = 'img/banner.png'></img>
        <hr width = 90% size = '3px' color = '#CCCCCC'>
        <h2>歡迎<?php
                    //unset($_COOKIE['welcome']);
                    if (isset($_COOKIE['welcome'])){
                        echo sprintf('回來，這是你本週第 %d 次回來了', $_COOKIE['welcome']);
                        setcookie('welcome', ($_COOKIE['welcome'] + 1));
                    } else {
                        setcookie('welcome', 1, strtotime('next Monday'));
                    }
                    ob_end_flush();
                ?>！</h2>
    </body>
</html>