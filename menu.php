<?php
    if ($_SERVER['PHP_SELF'] == '/menu.php'){
        header('location:/index.php');
    } else {
            require('conn.php');
        if (isset($_GET['logout'])){
            unset($_GET['logout']);
            unset($_SESSION['acc']);
            header(sprintf('Refresh:0; url = "%s"', $_SERVER['PHP_SELF']));
        }
    }
?>

<html>
    <head>
        <link rel = 'icon' href = '/img/icon.ico' type = 'image/x-icon'>
        <link rel = 'shortcut icon' href = '/img/icon.ico' type = 'image/x-icon'>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <style>
            input               {border:0.2vh solid #000000; border-radius:10px;}
            a                   {color:#000000; text-decoration:none;}
            body                {margin:0; font-size:1.5vw;}
            table               {margin-left:auto; margin-right:auto; text-align:center;}
            input[type = 'button'], input[type = 'submit'], input[type = 'reset'], .button
            {background-color: #FFF2DE; margin: auto; width: auto; height: auto; font-size: 1.57vw; font-weight: bold; opacity: 0.8; border: 0.3vh solid #D78F29; border-radius: 10px; cursor: pointer; display: inline-block; padding: 0 0.9vw;}

            #float              {width:auto; height:auto; background-color:#FFBE62; position:fixed; right:1vw; bottom:3.5vw; opacity:0.9; font-size:1.25vw; font-weight:bold; border-radius:0.5vw; cursor:pointer;}
            #float a            {width:100%; display:block; padding:1.4vh 0;}
            #float a:hover      {background-color:#E79F39; border-radius:1vh;}

            .disclaimer         {display:none;}
            
            .box                {background-color:#F0DDCF; padding:2% 2% 3%; margin:3% 7%; display:block; border-radius:3%;}

            .menu               {background-color:#FFBE62; font-size:1.5vw; font-weight:bold;}

            .main-menu          {background-color:#FFBE62; padding:0.4vw; margin:0; cursor:pointer; display:inline-block;}
            .main-menu:hover    {background-color:#E79F39;}

            .submenu            {background-color:#FFF2DE; padding:0; margin:0.4vw -0.4vw; list-style-type:none; position:absolute; text-align:left; border-radius:0 0 0.5vw 0.5vw; display:none;}
            .submenu a          {display:block; padding:1vh; border-radius:0 0 0.5vw 0.5vw;}
            .submenu a:hover    {background-color:#D8AE6E; color:#FFFFFF; border-radius:0 0 0.5vw 0.5vw;}
        </style>
        <script type='text/javascript'>
            function setMenu(id, name, hasSub){
                var tmp = '<span class = "main-menu" id = "' + id + '"';
                if (!hasSub){
                    tmp += 'onclick = "location = \'' + id + '.php\'">' + name + '</span>';
                } else {
                    tmp += 'onmouseover = "showMenu(this, \'sub_' + id + '\')" onmouseout = "hideMenu()">' +
                    '<span onclick = "location = \'' + id + '.php\'">' + name +
                    ' <font size = 1>▼　</font></span>' +
                    '<ul id = "sub_' + id + '" class = "submenu"></ul></span>';
                }
                document.getElementById('menu').innerHTML += tmp;
            }

            function setSubmenu(parent, id, name){
                document.getElementById('sub_' + parent).innerHTML += '<li><a href = "' + parent + '.php#' + id + '">' + name + '</a></li>';
            }

            function logout(){
                var href = location.href + '?logout=true';
                location = href;
            }

            function showMenu(main, sub){
                var submenu = document.getElementById(sub);
                submenu.style.minWidth = main.clientWidth;
                submenu.style.display = 'block';
                menuShown = sub;
            }

            function hideMenu(){
                if(menuShown != ''){
                    document.getElementById(menuShown).style.display = 'none';
                }
                menuShown = '';
            }
        </script>
    </head>
    <body align = center>
        <div id = 'float'><a href = '#top'>　回頁首　</a></div>
        <div class = 'menu' id = 'menu'>
                </ul>
            </span>
                </ul>
            </span>
        </div>

        <script type='text/javascript'>
            var menus = [['index', '　首頁　', 0], ['about', '　關於我們', 1],
                        ['NPO_channel', '　NPO Channel 專區', 1], ['tribe', '　部落　', 0],
                        ['goods', '　週邊商品　', 0], ['member', '　會員　', 0],
                        ['message_board', '　留言板　', 0]];
            for (var menu of menus){
                setMenu(menu[0], menu[1], menu[2]);
            }
            var submenus = {'beliefs;核心理念' : 'about', 'story;故事背景' : 'about',
                            'contact_us;聯絡我們' : 'about',
                            'what_is_NPO_channel;NPO Channel 是什麼？' : 'NPO_channel',
                            'donate;立即捐款' : 'NPO_channel'};
            for (var [key, value] of Object.entries(submenus)){
                var tmp = key.split(';');
                setSubmenu(value, tmp[0], tmp[1]);
            }
            var media = ['Facebook', 'Instagram'];
            var menuShown = '';
            var arr = ['index', 'about', 'NPO_channel', 'tribe', 'goods', 'member', 'message_board'];
            if (location.href == 'https://robotandcats-npo.000webhostapp.com/'){
                document.getElementById('index').style.color = '#FFFFFF';
            } else {
                for (let i = 0; i < arr.length; i++){
                    if (location.href.includes(arr[i])){
                        document.getElementById(arr[i]).style.color = '#FFFFFF';
                        break;
                    }
                }
            }
        </script>
    </body>
</html>