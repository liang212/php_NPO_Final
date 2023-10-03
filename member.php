<?php
    session_start();
    ob_start();
    include('menu.php');
    date_default_timezone_set("Asia/Taipei");
    
    $en = ['account', 'password', 'username', 'email'];
    $cn = ['帳號', '密碼', '用戶名稱', 'email'];
    
    function checkDuplication($conn, $account, $username, $email){
        global $en, $cn;
        $post = [$account, '', $username, $email];
        for ($i = 0; $i < count($en); $i++){
            if ($post[$i] == '') {continue;}
            $result = $conn -> query(sprintf("SELECT * FROM members WHERE %s = '%s';", $en[$i], $post[$i]));
            if (mysqli_num_rows($result) != 0){
                echo '<script type="text/javascript">alert("此'.$cn[$i].'已被註冊！請重新輸入一個'.$cn[$i].'！");</script>';
                return false;
            }
        }
        return true;
    }
    
    function printGoods($id, $path, $name, $price, $intro, $link, $besold){
        return sprintf('
        <div>
            <table width = 100%%>
                <tr>
                    <td style = "width:40%%; font-size:1.875vw; font-weight:bold; text-align:right"><img width = 60%% src = "%s"></img></td>
                    <td>
                        <table style = "margin:0 1vw; text-align:left; font-size:1.25vw; width:70%%">
                            <tr><td style = "width:3em;">連結：</td><td><input id = "link%d" type = "url" value = "%s" pattern = "{,100}" style = "width:100%%"></td></tr>
                            <tr><td style = "width:3em;">名稱：</td><td><input id = "name%d" type = "text" value = "%s" pattern = "{,20}" style = "width:100%%"></td></tr>
                            <tr><td style = "width:3em;">價錢：</td><td>NT$<input id = "price%d" type = "number" value = "%d" pattern = "{,3}" style = "width:5em; text-align:right;"></td></tr>
                            <tr><td style = "width:3em;">簡介：</td><td><textarea id = "intro%d" pattern = "{,100}" style = "width:100%%; resize:vertical;">%s</textarea></td></tr>
                            <tr><td colspan = 2>
                                上架狀態：<input id = "besold%d" type = "checkbox"%s><br>
                                <input type = "button" value = "修改" onclick = "editGoods(%d)">
                            </td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        ', $path, $id, $link, $id, $name, $id, $price, $id, $intro, $id, (($besold) ? ' checked' : ''), $id);
    }

    function printMembers($id, $account, $password, $username, $email, $tribe, $tribeVisible, $verification, $activated, $admin){
        return sprintf('
            <tr>
                <td><input id = "adminEditConfirmed%d" onclick = "adminEditConfirmed(%d)" type = "checkbox"></td>
                <td><input id = "adminDelete%d" onclick = "adminDelete(%d)" type = "checkbox"></td>
                <td><input id = "account%d" type = "text" value = "%s"></td>
                <td><input id = "password%d" type = "password" value = "%s"></td>
                <td><input id = "username%d" type = "text" value = "%s"></td>
                <td><input id = "email%d" type = "email" value = "%s"></form></td>
                <td><span id = "tribe%d">%s</span><br>
                    <select id = "adminTribeEdit%d" onchange = "editTribe(%d)">
                        <option>在此可更改欲加入的部落</option>
                        <option>無</option>
                        <option>陽光灑落的社區</option>
                        <option>琴聲繚繞的市集</option>
                    </select>
                </td>
                <td><input id = "tribeVisible%d" onclick = "adminEdit(\'tribeVisible\', %d)" type = "checkbox"%s></td>
                <td><input id = "verification%d" onclick = "adminEdit(\'verification\', %d)" type = "checkbox"%s></td>
                <td><input id = "activated%d" onclick = "adminEdit(\'activated\', %d)" type = "checkbox"%s></td>
                <td><input id = "admin%d" onclick = "adminEdit(\'admin\', %d)" type = "checkbox"%s></td>
            </tr>
        ', $id, $id, $id, $id, $id, $account, $id, $password, $id, $username, $id, $email, $id, $tribe, $id, $id, $id, $id, (($tribeVisible) ? ' checked' : ''), $id, $id, (($verification) ? ' checked' : ''), $id, $id, (($activated) ? ' checked' : ''), $id, $id, (($admin) ? ' checked' : ''));
    }
    
    function printFeedback($id, $name, $email, $feedback){
        return sprintf('
            <tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td><input type = "button" value = "刪除" onclick = "deleteFeedback(%d)"></td>
            </tr>
        ', $name, $email, $feedback, $id);
    }
    if (isset($_GET['rand']) && isset($_SESSION['cid'.$_GET['cid']]) && $_GET['rand'] == $_SESSION['cid'.$_GET['cid']]){
        $conn -> query(sprintf("UPDATE members SET verification = 1 WHERE id = '%s';", $_GET['cid']));
        unset($_SESSION['cid'.$_GET['cid']]);
        echo '<script type="text/javascript">alert("驗證成功！"); location = "/member.php"</script>';
    }
    
    if (isset($_POST['edit'])){
        $result = mysqli_fetch_assoc($conn -> query(sprintf("SELECT * FROM members WHERE account = '%s';", $_POST['account'])));
        if ($_POST['username'] != $result['username'] && checkDuplication($conn, '', $_POST['username'], '')){
            $conn -> query(sprintf("UPDATE members SET username = '%s' WHERE username = '%s';", $_POST['username'], $result['username']));
        }
        if ($_POST['email'] != $result['email'] && checkDuplication($conn, '', '', $_POST['email'])){
            $conn -> query(sprintf("UPDATE members SET email = '%s' WHERE email = '%s';", $_POST['email'], $result['email']));
            if ($result['email'] != $_POST['email']){
                $conn -> query(sprintf("UPDATE members SET verification = 0 WHERE email = '%s';", $_POST['email']));
            }
        }
        $conn -> query(sprintf("UPDATE members SET password = '%s' WHERE account = '%s';", $_POST['password'], $_POST['account']));
        unset($_POST['edit']);
    }
    
    if (isset($_POST['signup'])){
        if (checkDuplication($conn, $_POST['account'], $_POST['username'], $_POST['email'])){
            $id = is_null($tmp = mysqli_fetch_assoc($conn -> query("SELECT MAX(id) AS id FROM members"))['id']) ? 1 : $tmp + 1;
            $conn -> query(sprintf("INSERT INTO members(id, account, password, username, email) VALUES('%d', '%s', '%s', '%s', '%s');", $id, $_POST['account'], $_POST['password'], $_POST['username'], $_POST['email']));
            echo '<script type="text/javascript">alert("註冊成功！\n已寄發 email 驗證，請完成驗證！\n\n若未收到驗證信，可於登入後要求重新寄發驗證信。");</script>';
            require('send.php');
            $_SESSION['cid'.$id] = rand(10000, 99999);
            $link = 'https://robotandcats-npo.000webhostapp.com/member.php?rand='.$_SESSION['cid'.$id].'&cid='.$id;
            $_SESSION['acc'] = $_POST['account'];
            send_mail($_POST['email'], $_POST['username'], '機咪與恩波 會員 email 驗證信', sprintf('親愛的用戶 %s 您好！<br>請點擊下列連結（或直接複製至網址列）以完成 email 驗證：<br>%s', $_POST['username'], $link));
            header('refresh:0');
        }
        unset($_POST['signup']);
    }
    if (isset($_POST['signin'])){
        if (mysqli_num_rows($conn -> query(sprintf("SELECT * FROM members WHERE account = '%s' AND password = '%s' AND activated = 1;", $_POST['account'], $_POST['password']))) != 0){
            $_SESSION['acc'] = $_POST['account'];
            header('location:/member.php');
        } else {
            echo '<script type="text/javascript">alert("帳號或密碼錯誤！");</script>';
        }
        unset($_POST['signin']);
    }
    if (isset($_POST['verify'])){
        $result = mysqli_fetch_assoc($conn -> query(sprintf("SELECT * FROM members WHERE account = '%s';", $_SESSION['acc'])));
        echo '<script type="text/javascript">alert("已寄發 email 驗證，請完成驗證！\n\n若未收到驗證信，可要求重新寄發驗證信。");</script>';
        require('send.php');
        $_SESSION['cid'.$result['id']] = rand(10000, 99999);
        $link = 'https://robotandcats-npo.000webhostapp.com/member.php?rand='.$_SESSION['cid'.$result['id']].'&cid='.$result['id'];
        send_mail($result['email'], $result['username'], '機咪與恩波 會員 email 驗證信', sprintf('親愛的用戶 %s 您好！<br>請點擊下列連結（或直接複製至網址列）以完成 email 驗證：<br>%s', $result['username'], $link));
        unset($_POST['verify']);
        header('refresh:0');
    }
?>
<html>
    <head>
        <title>會員 | 機咪與恩波 Jimmy x NPO Channel</title>
        <style>
            .box        {width:auto; display:inline-block;}
            .odd,.even  {padding:2vh 5vh; margin:1vh; width:60%; font-weight:bold; display:inline-block; border:0.2vh solid #B0CAFF; border-radius:1vh; cursor:pointer;}
            .odd        {background-color:#DEDEFF;}
            .even       {background-color:#E7E7FF;}
            .inner      {margin:2vh 5vh;}
            .font       {color:#999999; font-size:16px; cursor:pointer;}
            .options    {font-size:24px; cursor:pointer;}
            .header     {text-align:center; padding-bottom:3vh; font-size:3vh; font-weight:bold;}
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type = 'text/javascript'>
            var display = {'signin' : 1, 'signup' : 0};
            function formatting(id, state){
                var tmp = '<form action = "" method = "post"><table class = "box" id = "' + id +
                        '" style = "display:' + (display[id] ? 'inline-block' : 'none') + ';">' +
                        '<tr><td class = "header" colspan = 2>' + state + '頁面</td></tr>' +
                        '<tr><td>帳號：</td><td><input type = "text" name = "account" pattern = "[a-zA-Z0-9]{6,20}" placeholder = "6~20字元長之英數" required></td></tr>' +
                        '<tr><td>密碼：</td><td><input type = "password" name = "password" pattern = "[a-zA-Z0-9]{6,20}" placeholder = "6~20字元長之英數" required></td></tr>';
                if (id == 'signup'){
                    tmp += '<tr><td>用戶名稱：</td><td><input type = "text" name = "username" pattern = "{,20}" placeholder = "最多20字元長" required></td></tr>' + 
                            '<tr><td>email：</td><td><input type = "email" name = "email" pattern = "{,100}" placeholder = "請輸入有效的 email" required></td></tr>';
                }
                tmp += '<tr><td colspan = 2><br>' + (id == 'signup' ? '<input type = "reset" value = "重置">　' : '') +
                        '<input type = "submit" name = "' + id + '" value = "' + state + '"></td></tr>' +
                        '<tr><td colspan = 2><span class = "font" onclick = "toggle()"><br>' +
                        (id == 'signup' ?'回到登入畫面' : '還沒有帳號？') + '</td></tr>' +
                        '</span></table></form>';
                document.write(tmp);
            }
            function toggle(){
                for (var [id, boolean] of Object.entries(display)){
                    document.getElementById(id).style.display = (display[id] = !boolean) ? 'inline-block' : 'none';
                }
            }
            function toggleBy(id){
                document.getElementById(id).style.display = (document.getElementById(id).style.display == 'none') ? 'block' : 'none';
            }
            function deactivate(id){
                if (confirm('停用帳號後將無法使用相同的帳號、用戶名稱或 email 註冊新帳號！\n除非您向管理員申請重新啟用或是「刪除」帳號。\n您確定要停用帳號嗎？')){
                    $.ajax({
                        type: 'POST',
                        url: 'post_handler.php',
                        data: {
                            'deactivate' : true,
                            'id' : id
                        },
                        success:function(){
                            alert("您的帳號已被停用！");
                            location.reload();
                        }
                    })
                }
            }
            function pop(){
                while (true){
                    var index = Math.floor(Math.random() * 8) + 1;
                    if (document.getElementById('pop').getAttribute('index') != index){
                        document.getElementById('pop').setAttribute('index', index);
                        document.getElementById('pop').src = '/img/NPO_' + index + '.png';
                        break;
                    }
                }
            }
            function editGoods(id){
                var name = '#name' + id;
                var price = '#price' + id;
                var intro = '#intro' + id;
                var link = '#link' + id;
                var besold = '#besold' + id;
                $.ajax({
                    type: 'POST',
                    url: 'post_handler.php',
                    data: {
                        'editGoods' : true,
                        'id' : id,
                        'name' : $(name).val(),
                        'price' : $(price).val(),
                        'intro' : $(intro).val(),
                        'link' : $(link).val(),
                        'besold' : ($(besold).prop('checked') ? 1 : 0)
                    },
                    success:function(){
                        alert("修改成功");
                    }
                })
            }
            function editTribe(id){
                var tribe = '#tribe' + id;
                var admin = '#adminTribeEdit' + id + ' :selected';
                if ($(admin).val() != '在此可更改欲加入的部落'){
                    $.ajax({
                        type: 'POST',
                        url: 'post_handler.php',
                        data: {
                            'tribeEdit' : true,
                            'tribe' : $(admin).val(),
                            'id' : id
                        },
                        success:function(){
                            $(tribe).html($(admin).val());
                            console.log("tribe 修改成功");
                        }
                    })
                }
            }
            function adminEditConfirmed(id){
                var check = '#adminEditConfirmed' + id;
                if (($(check).prop('checked'))){
                    var account = '#account' + id;
                    var password = '#password' + id;
                    var username = '#username' + id;
                    var email = '#email' + id;
                    if ($(account).val().length < 6) {alert('帳號長度過短');}
                    else if ($(account).val().length > 20) {alert('帳號長度過長');}
                    else if ($(password).val().length < 6) {alert('密碼長度過短');}
                    else if ($(password).val().length > 20) {alert('密碼長度過長');}
                    else if ($(username).val().length == 0) {alert('用戶名稱不能為空');}
                    else if ($(username).val().length > 20) {alert('用戶名稱過長');}
                    else if ($(email).val().length == 0) {alert('email 不能為空');}
                    else if ($(email).val().length > 100) {alert('email 過長');}
                    else {
                        $.ajax({
                            type: 'POST',
                            url: 'post_handler.php',
                            data: {
                                'adminEditConfirmed' : true,
                                'account' : $(account).val(),
                                'password' : $(password).val(),
                                'username' : $(username).val(),
                                'email' : $(email).val(),
                                'id' : id
                            },
                            success:function(){
                                console.log("已傳送 POST 請求");
                            }
                        })
                    }
                }
            }
            function adminDelete(id){
                var check = '#adminDelete' + id;
                if (($(check).prop('checked'))){
                    var account = '#account' + id;
                    var acc = "<?php echo (isset($_SESSION['acc']) ? $_SESSION['acc'] : ''); ?>";
                    if (acc == $(account).val()){
                        alert('您不能刪除您當前使用的管理員帳號！');
                        $(check).prop('checked', false);
                    } else if (confirm('一旦刪除，操作將不可逆！\n確定刪除 id 為 ' + id + ' 的帳號嗎？')){
                        $.ajax({
                            type: 'POST',
                            url: 'post_handler.php',
                            data: {
                                'adminDelete' : true,
                                'id' : id
                            },
                            success:function(){
                                alert("該帳號已被刪除！");
                                location.reload();
                            }
                        })
                    } else {
                        $(check).prop('checked', false);
                    }
                }
            }
            function adminEdit(column, id){
                var columnID = '#' + column + id;
                $.ajax({
                    type: 'POST',
                    url: 'post_handler.php',
                    data: {
                        'adminEdit' : true,
                        'columnName' : column,
                        'column' : ($(columnID).prop('checked') ? 1 : 0),
                        'id' : id
                    },
                    success:function(){
                        console.log(column + " 修改成功");
                    }
                })
            }
            function deleteFeedback(id){
                if (confirm('一旦刪除，操作將不可逆！\n您確定要刪除此意見回函嗎？')){
                    $.ajax({
                        type: 'POST',
                        url: 'post_handler.php',
                        data: {
                            'deleteFeedback' : true,
                            'id' : id
                        },
                        success:function(){
                            alert("該意見回函已被刪除！");
                            location.reload();
                        }
                    })
                }
            }
            $(document).ready(function(){
                $('#passwordVisible').click(function(){
                    $('#password').prop('type', ($('#password').prop('type') == 'password') ? 'text' : 'password');
                })
                $('#edit').click(function(){
                    var readonly = ($('#password').prop('readonly') != null);
                    var elements = ['password', 'username', 'email'];
                    for (var id of elements){
                        var tmp = '#'+id
                        $(tmp).prop('readonly', !$(tmp).prop('readonly'))
                    }
                })
                $('#visible').click(function(){
                    $.ajax({
                        type: 'POST',
                        url: 'post_handler.php',
                        data: {
                            'changeVisible' : true,
                            'visible' : ($('#visible').prop('checked') ? 1 : 0),
                            'id' : $('#id').val()
                        },
                        success:function(){
                            alert("修改成功");
                        }
                    })
                })
                $('#tribeEdit').change(function(){
                    if ($('#tribeEdit :selected').val() != '在此可更改欲加入的部落'){
                        $.ajax({
                            type: 'POST',
                            url: 'post_handler.php',
                            data: {
                                'tribeEdit' : true,
                                'tribe' : $('#tribeEdit :selected').val(),
                                'id' : $('#id').val()
                            },
                            success:function(){
                                $('#tribeJoined').html($('#tribeEdit :selected').val());
                                alert("修改成功");
                            }
                        })
                    }
                })
                $('#pop').click(function(){
                    $.ajax({
                        type: 'POST',
                        url: 'post_handler.php',
                        data: 'click=true',
                        success:function(msg){
                            $('#clicks').html(msg);
                        }
                    })
                })
            });
        </script>
    </head>
    <body>
        <?php
            if(!isset($_SESSION['acc'])){
                echo '<script type = "text/javascript"> formatting(\'signup\', \'註冊\'); formatting(\'signin\', \'登入\'); </script>';
            } else {
                $result = $conn -> query(sprintf("SELECT * FROM members WHERE account = '%s';", $_SESSION['acc']));
                $row = mysqli_fetch_assoc($result);
                if ($row['admin'] == 0){
                    echo
                    '
                    <input id = "id" type = "hidden" value = "'.$row['id'].'">
                    <div class = "box" style = "display:block; text-align:left"><div class = "header">會員頁面</div>
                        <div class = "odd" onclick = "toggleBy(\'data\');"><b>查看／更改帳號資料、停用帳號</b></div>
                        <div class = "inner" id = "data" style = "display:none;"><form action = "" method = "post">
                            <table align = left>
                                <tr><td>帳號：</td><td><input id = "account" type = "text" name = "account" value = "'.$row['account'].'" readonly required></td></tr>
                                <tr><td>密碼：<br></td><td><input id = "password" type = "password" name = "password" pattern = "[a-zA-Z0-9]{6,20}" value = "'.$row['password'].'" readonly required></td></span></tr>
                                <tr><td>用戶名稱：</td><td><input id = "username" type = "text" name = "username" value = "'.$row['username'].'" readonly required></td></tr>
                                <tr><td>email：</td><td><input id = "email" type = "email" name = "email" pattern = "{,100}" value = "'.$row['email'].'" readonly required></td></tr><tr><td colspan="2"><br><input type = "button" onclick = "deactivate('.$row['id'].')" value = "停用帳號"><input type = "submit" name = "edit" value = "送出修改"></td></tr>
                            </table></form><input id = "passwordVisible" type = "checkbox">顯示<br><input type = "checkbox" id = "edit">修改<br>'.'
                            驗證狀態：<input type = "checkbox" onclick = "return false;" '.($row['verification'] == 0 ? '' : 'checked').'>'.
                            ($row['verification'] == 0 ? '<form action = "" method = "post"><input type = "submit" name = "verify" value = "寄發驗證信"></form>' : '').'
                            <hr width = 100% size = "1vh" color = "#BBBBBB">
                        </div>
                        <div class = "even" onclick = "toggleBy(\'tribeSetting\')"><b>部落設定</b></div>
                        <div class = "inner" id = "tribeSetting" style = "display:none;">
                            您目前所屬的部落為：<span id = "tribeJoined">'.$row['tribe'].'</span>'.'
                            <select id = "tribeEdit">
                                <option>在此可更改欲加入的部落</option>
                                <option>無</option>
                                <option>陽光灑落的社區</option>
                                <option>琴聲繚繞的市集</option>
                            </select><br>'.
                            '別人是否可見您所屬的部落：<input type = "checkbox" id = "visible" '.
                            ($row['tribeVisible'] == 0 ? '' : 'checked').'>（點擊即可更改）<br>
                        </div>
                        <div class = "odd" onclick = "toggleBy(\'NPOpCat\')"><b>NPOp Cat</b></div>
                        <div class = "inner" id = "NPOpCat" style = "display:none;">
                            <div>目前的總點擊數：<span id = "clicks">'.mysqli_fetch_assoc($conn -> query("SELECT * FROM NPOpCat;"))['clicks'].'</span></div>
                            <img index = "1" id = "pop" style = "width:12vw; cursor:pointer;" src = "/img/NPO_1.png" onclick = "pop();">
                            <hr width = 100% size = "1vh" color = "#BBBBBB">
                        </div>
                    ';
                } else {
                    $tmp = '
                        <div class = "box" style = "width:70vw;">
                            <h3>後臺管理頁面</h3>
                            <div class = "odd" onclick = "toggleBy(\'goodsEdit\')"><b>週邊商品資料修改、上下架</b></div>
                            <div id = "goodsEdit" style = " display:none;">
                    ';
                    $goodsQuery = $conn -> query("SELECT * FROM goods;");
                    while ($row = mysqli_fetch_assoc($goodsQuery)){
                        $tmp .= printGoods($row['gid'], $row['path'], $row['name'], $row['price'], $row['intro'], $row['link'], $row['besold']);
                    }
                    $tmp .= '<hr width = 100% size = "1vh" color = "#BBBBBB">
                            </div>
                        <div class = "even" onclick = "toggleBy(\'management\')"><b>會員資料管理</b></div>
                        <div id = "management" style = " display:none;">
                            <table width = 75%>
                                <tr><th>編輯</th><th>刪除</th><th>帳號</th><th>密碼</th><th>用戶名稱</th><th>email</th><th>部落</th><th>部落顯示</th><th>email 驗證</th><th>帳號啟用</th><th>管理員</th></tr>
                    ';
                    $member = $conn -> query("SELECT * FROM members;");
                    while ($row = mysqli_fetch_assoc($member)){
                        $tmp .= printMembers($row['id'], $row['account'], $row['password'], $row['username'], $row['email'], $row['tribe'], $row['tribeVisible'], $row['verification'], $row['activated'], $row['admin']);
                    }
                    $tmp .= '
                            </table>
                            <hr width = 100% size = "1vh" color = "#BBBBBB">
                        </div>
                        <div class = "odd" onclick = "toggleBy(\'receive\')"><b>接收意見回函</b></div>
                        <div id = "receive" style = " display:none;">
                            <table width = 75%>
                                <tr><th>姓名</th><th>email</th><th>意見回函</th><th></th></tr>
                    ';
                    $feedback = $conn -> query("SELECT * FROM feedback;");
                    while ($row = mysqli_fetch_assoc($feedback)){
                        $tmp .= printFeedback($row['fid'], $row['name'], $row['email'], $row['feedback']);
                    }
                    $tmp .= '
                            </table>
                            <hr width = 100% size = "1vh" color = "#BBBBBB">
                        </div>
                    ';
                    echo $tmp;
                }
                echo '<center><br><input type = "button" value = "登出" onclick = logout()></center></div>';
            }
        ?>
    </body>
</html>