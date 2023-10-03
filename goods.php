<?php
    session_start();
    include('menu.php');
    require('conn.php');
    date_default_timezone_set("Asia/Taipei");
?>
<html>
    <head>
        <title>週邊商品 | 機咪與恩波 Jimmy x NPO Channel</title>
        <style>
            td      {padding:1%; width:50%;}
            #manage {float:right;}
        </style>
    </head>
    <body align = 'center'>
        <div class = 'box'>
            <?php
                if (isset($_SESSION['acc']) && mysqli_fetch_row($conn -> query(sprintf("SELECT admin FROM members WHERE account = '%s';", $_SESSION['acc'])))[0]){ echo '<input type = "button" id = "manage" value = "管理" onclick = "location=\'/member.php?\'">'; }
                $SQL = $conn -> query("SELECT * FROM goods WHERE besold != 0");
                if (mysqli_num_rows($SQL) == 0){
                    echo '<b>籌備中，敬請期待！</b>';
                } else {
                    while ($row = mysqli_fetch_assoc($SQL)){
                        echo sprintf('
                            <table width = 100%%>
                                <tr>
                                    <td style = "font-size:1.875vw; font-weight:bold; line-height:2em;">
                                        <img width = 60%% height = 60%% src = "%s"></img>
                                        <div style = "padding:2%% 0;">%s<br>NT$%d</div>
                                    </td>
                                    <td style = "font-size:1.25vw; text-align:left;">%s</td>
                                </tr><tr><td colspan = 2>
                                    <input type = "button" value = "前往購買！" onclick = "window.open(\'%s\', \'_blank\')">
                                </td></tr>
                            </table>
                        ', $row['path'], $row['name'], $row['price'], nl2br($row['intro']), $row['link']);
                    }
                }
            ?>
        </div>
    </body>
</html>