<?php
    if ($_SERVER['PHP_SELF'] == '/conn.php'){
        header('location:/index.php');
    } else {
        $hostname = 'localhost';
        $username = 'id19067424_jimmyandnpo';
        $password = '-IwQQz<2)?>I^AmG';
        $dbname = 'id19067424_robotandcats_npo';
        
        $conn = mysqli_connect($hostname, $username, $password, $dbname);
        $conn -> set_charset('UTF8');
    }
?>