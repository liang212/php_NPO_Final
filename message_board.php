<?php
    session_start();
    include('menu.php');
    date_default_timezone_set("Asia/Taipei");
    require('conn.php');

    if (isset($_GET['rand']) && $_GET['rand'] == $_SESSION['rand']){
        $cid = $_GET['cid'];
        $conn -> query("UPDATE comment SET mid = NULL, name = '該留言已被刪除', comment = '該留言已被刪除' WHERE cid = '$cid';");
        unset($_GET['rand']);
        unset($_SESSION['rand']);
        echo '<meta http-equiv = REFRESH CONTENT = 0; url = message_board.php#comment>';
    }
    $name = isset($_SESSION['acc']) ? $_SESSION['acc'] : '匿名';

    if (!isset($_SESSION['rand'])){
        $_SESSION['rand'] = rand(10000, 99999);
    }

    function check($mid){
       return ($mid != NULL) ? '<img length = 25 width = 25 style = "vertical-align:sub;" src = "./img/check.png"> ' : '';
    }

    function comment($cid, $mid, $name, $comment, $replyto, $time){
        include('conn.php');
        if ($replyto != NULL){
            $replied = mysqli_fetch_assoc($conn -> query("SELECT * FROM comment WHERE cid = '".$replyto."';"));
            echo '<script>console.log('.$replyto.')</script>';
            printf('<div class = "sub">%s %s　回覆 %s %s：', check($mid), $name, check($replied['mid']), $replied['name']);
        } else{
            printf('<div class = "comment"> %s <b>%s</b> 說：', check($mid), $name);
        }
        printf('<div class = "float">%s</div>', substr($time, 0, 19))
        .printf('<div class = "content">%s<br></div><div align = right>', $comment);

        if (isset($_SESSION['acc']) && mysqli_fetch_row($conn -> query(sprintf("SELECT admin FROM members WHERE account = '%s';", $_SESSION['acc'])))[0]){
            printf('<div class = "button" id = "button" onclick = deleteComment(%d)>%s</div>',$cid , '刪除');
        }
        replyto($cid);
        printf('</div>');
        $subquery = $conn -> query("SELECT * FROM comment WHERE replyto = '$cid' ORDER BY time DESC;");
        while ($sub = mysqli_fetch_assoc($subquery)){
            comment($sub['cid'], $sub['mid'], $sub['name'], $sub['comment'], $cid, $sub['time']);
            echo '</div>';
        }
    }

    function replyto($cid){
        printf('<div class = "button" onclick = replyto(%d)>%s</div>', $cid, '回覆')
        .printf('<div id = %d style = "display:none"><form action = "" method = "post">', $cid)
        .printf('<input type = "hidden" name = "replyto" value = %d>', $cid)
        .printf('暱稱（選填）：<input type = "text" name = "replyname"><br><br>')
        .printf('<textarea cols = 100 rows = 10 name = "replycomment" required></textarea>')
        .printf('<input type = "submit" name = "reply"></form></div>');
    }
?>
<html>
    <head>
        <title>留言板 | 機咪與恩波 Jimmy x NPO Channel</title>
        <style>
            .box        {padding:1% 0% 1% 0%;}
            .comment    {background-color:#FFC8C8; padding:1% 5%; margin:3% 7%; display:block; text-align:left; border-radius:10px;}
            .float      {padding:1% 0%; display:block; font-size:16px; float:right;}
            .button     {margin:1%;}
            .content    {padding:1% 6% 1% 6%; display:block; text-align:left;}
            .sub        {background-color:#A79292; padding:1% 5%; display:block;}
        </style>
        <script type = 'text/javascript'>
            function replyto(cid){
                var display = document.getElementById(cid).style.display;
                display = display == 'none' ? 'block' : 'none';
                document.getElementById(cid).style.display = display;
            }

            function deleteComment(cid){
                if (confirm('確定刪除？')){
                    var rand = <?php echo $_SESSION['rand']; ?>;
                    location = 'message_board.php?rand=' + rand + '&cid=' + cid;
                }
            }
        </script>
    </head>
    <body align = 'center'>
        <div class = 'box'>
            <h1>留言板</h1>
            用戶　<b><?php echo $name;?></b>　您好。<br>您可以在留言板留下文字與他人討論喔～<br><br>
            <form action = '' method = 'post'>
                暱稱（選填）：<input type = 'text' name = 'name'><br><br>
                留言：<br><textarea cols = 100 rows = 10 name = 'comment' required></textarea><br><br>
                <input type = 'reset' value = '重置'>　<input type = 'submit' name = 'submit' value = '送出'>
            </form>
        </div>
        <div class = 'box'><a name = 'comment'>
            <?php
                $SQL = $conn -> query("SELECT * FROM comment WHERE replyto IS NULL ORDER BY time DESC");
                if (mysqli_num_rows($SQL) == 0){
                    echo '看來目前沒有留言喔。<br>成為第一個留言的人吧！';
                } else {
                    while ($row = mysqli_fetch_assoc($SQL)){
                        comment($row['cid'], $row['mid'], $row['name'], $row['comment'], NULL, $row['time']);
                        $cid = $row['cid'];
                        echo '</div>';
                    }
                }
            ?>
        </div>
    </body>
</html>

<?php
    if (isset($_POST['submit']) || isset($_POST['reply'])){
        if (isset($_POST['submit'])){
            $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? $_POST['name'] : $name;
            $comment = nl2br($_POST['comment']);
        } else {
            $name = (isset($_POST['replyname']) && trim($_POST['replyname']) != '') ? $_POST['replyname'] : $name;
            $comment = nl2br($_POST['replycomment']);
        }
        $replyto = (isset($_POST['reply'])) ? $_POST['replyto'] : '';
        $cid = is_null($tmp = mysqli_fetch_assoc($conn -> query("SELECT MAX(cid) AS cid FROM comment"))['cid']) ? 1 : $tmp + 1;
        $mid = mysqli_fetch_row($conn -> query(sprintf("SELECT id FROM members WHERE account = '%s';", $_SESSION['acc'])))[0];
        $time = date('Y-m-d H:i:s',time());
        $conn -> query("INSERT INTO comment(cid, mid, name, comment, replyto, time) VALUES('$cid', NULLIF('$mid', ''), '$name', '$comment', NULLIF('$replyto', ''), '$time');");

        echo '<meta http-equiv = REFRESH CONTENT = 0; url = http://localhost/message_board.php#comment>';

        unset($_POST['submit']);
        unset($_POST['reply']);
    }
?>