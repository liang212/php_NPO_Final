<?php
    session_start();
    include('menu.php');
    date_default_timezone_set("Asia/Taipei");
    $sunshineCount = mysqli_fetch_row($conn -> query("SELECT COUNT(id) FROM members WHERE tribe = '陽光灑落的社區';"))[0];
    $forBlindCount = mysqli_fetch_row($conn -> query("SELECT COUNT(id) FROM members WHERE tribe = '琴聲繚繞的市集';"))[0];
    $sunshineVisible = $conn -> query("SELECT username FROM members WHERE tribe = '陽光灑落的社區' AND tribeVisible = 1;");
    $forBlindVisible = $conn -> query("SELECT username FROM members WHERE tribe = '琴聲繚繞的市集' AND tribeVisible = 1;");
?>
<html>
    <head>
        <title>部落 | 機咪與恩波 Jimmy x NPO Channel</title>
        <style>
            img                 {width:7.5vw; float:left}
            .leftbox, .rightbox {width:50%; padding:2% 2% 3%; margin:3% 7%; display:block; border-radius:3%; text-align:left;}
            .leftbox            {margin-right:50%; background-color:#FDF0C0; float:left;}
            .rightbox           {margin-left:50%; background-color:#C5E0FF; float:right;}
            .map                {background:url(img/map.png); background-size:70vw 39.375vw; margin:auto; width:70vw; height:39.375vw; box-shadow: inset 0 0 0.3vw 1vw #F0DDCF}
            .header             {text-align:center; padding:2.5vw 1vw 5vw; font-size:3.5vw; font-weight:bold; line-height:2.5vw;}
            .content            {margin:-2% 10%; word-break:break-all; line-height:2.5vw;}
        </style>
    </head>
    <body align = 'center'>
        <div class = 'box'><h1 >部落</h1>
            <div class = 'map' id = 'map'></div>
        </div>
        <div class = 'leftbox' id = 'tribe1'>
            <div class = 'header' style = 'color:#7D4D07'><img src = '/img/sunshine.png'></img><br>陽光灑落的社區</div>
            <div class = 'content'>
                目前成員數：<?php echo $sunshineCount; ?><br>
                成員：<br>
                <?php
                    if ($sunshineCount == 0) { echo '該部落目前沒有任何成員。'; }
                    else if (($sunshineVisibleCount = mysqli_num_rows($sunshineVisible)) == 0){ echo '該部落的成員都非常低調，不願透露名字。';
                    } else {
                        for ($i = 0; $i < $sunshineVisibleCount; $i++){
                            $row = mysqli_fetch_assoc($sunshineVisible);
                            echo $row['username'];
                            if ($i != $sunshineVisibleCount - 1) { echo ', ';}
                        }
                        if (($num = ($sunshineCount - $sunshineVisibleCount)) != 0){ echo '<br>有 '.$num.' 人決定保持低調，不透露名字。';}
                    }
                ?>
            </div>
        </div>
        <div class = 'rightbox' id = 'tribe2'>
            <div class = 'header' style = 'color:#1F0F82'><img src = '/img/forblind.png'></img><br>琴聲繚繞的市集</div>
            <div class = 'content'>
                目前成員數：<?php echo $forBlindCount; ?><br>
                成員：<br>
                <?php
                    if ($forBlindCount == 0) { echo '該部落目前沒有任何成員。'; }
                    else if (($forBlindVisibleCount = mysqli_num_rows($forBlindVisible)) == 0){ echo '該部落的成員都非常低調，不願透露名字。';
                    } else {
                        for ($i = 0; $i < $forBlindVisibleCount; $i++){
                            $row = mysqli_fetch_assoc($forBlindVisible);
                            echo $row['username'];
                            if ($i != $forBlindVisibleCount - 1) {echo ', ';}
                        }
                        if (($num = ($forBlindCount - $forBlindVisibleCount)) != 0){ echo '<br>有 '.$num.' 人決定保持低調，不透露名字。';}
                    }
                ?>
            </div>
        </div>
        <?php
            if (!isset($_SESSION['acc'])){
                echo '<div align = center>想加入部落？立即 <a href = "/member.php"><b>點我註冊</b></a><br><br></div>';
            }
        ?>
    </body>
</html>