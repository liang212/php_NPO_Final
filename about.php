<?php
    session_start();
    include('menu.php');
    date_default_timezone_set("Asia/Taipei");
?>
<html>
    <head>
        <title>關於我們 | 機咪與恩波 Jimmy x NPO Channel</title>
        <style>
            table       {margin:0; text-align:left;}

            .box        {text-align:left;}
            .header     {font-size:3.125vw; padding:0 2% 3%; font-weight:bold; display:block;}
            .content    {font-size:1.25vw; padding:2% 0 0; margin:-3% 2% 1% 8%; display:block; line-height:2.95vw;}
            .subheader  {font-size:1.875vw; margin:0% 0% 3% 2%; font-weight:bold; display:block;}
            
            #media      {font-size:1.25vw;}
            #media td   {padding:0.625vw;}
            #media img  {width:3.125vw;}

            img         {width: 37.5vw; float: left;}
            #carousel   {margin:1.875vw auto 0; overflow:hidden; position:relative; box-shadow:0.625vw 0.625vw 0.3125vw #888;}
            #pics       {width:300vw; position:absolute; z-index:1;}
            #dots       {width:7.5vw; height: 0.625vw; position: absolute; bottom:1.5625vw; left:50%; z-index:2;}
            #dots span  {width:0.625vw; height:0.625vw; float:left; margin-right:0.3125vw; background:#333; border:solid 1px #FFF; border-radius: 50%; cursor: pointer;}
            #dots .on   {background: orangered;}
            #dots span:hover    {background: orangered;}
            .turn       {width:2.5vw; height:2.5vw; color:#fff; background:orangered; line-height:2.4375vw;
            text-align:center; font-size:2.25vw; font-weight:bold; opacity:0.5; position:absolute;
            top:50%; display:none; z-index:2; cursor:pointer; border-radius:50%;}
            .turn:hover {opacity: 0.8;}
            #carousel:hover .turn   {display: block}
            #pre        {left: 1.25vw;}
            #next       {right: 1.25vw;}
        </style>
        <script type = 'text/javascript'>
            function formatting(id, header){
                var format =
                '<div class = "box"><div class = "header" id = "' + id + '">' + header + '</div>' +
                '<div class = "content" id = "' + id + 'Content"></div></div>';
                document.write(format);
            }

            function subheader(parentId, id, subheader){
                var format = '<div class = "subheader" id = "' + id + '">' + subheader + '</div><div class = "content" id = "' + id + 'Content"></div>';
                document.getElementById(parentId).innerHTML += format;
            }

            function mediaTable(id){
                var format = '<table id = "media">';
                for (var i of media){
                    format +=
                    '<tr><td>' + i + ':　</td><td><a target = "_blank" href = "https://www.' +
                    i.toLowerCase() + '.com/robotandcats.npo/">' + '<img src = "img/icon_' +
                    i + '.png"></img></a></td></tr>';
                }
                format +=
                '<tr><td>e-mail:　</td><td>team07.npo@gmail.com</td></tr>' +
                '<tr><td colspan = 2 align = center><hr size = "3px" color = "#D8AE6E">' +
                '<?php
                    if (isset($_POST['submit'])){
                        $number = mysqli_fetch_row($conn -> query("SELECT MAX(fid) FROM feedback;"))[0] + 1;
                        $SQL = sprintf("INSERT INTO feedback(fid, name, email, feedback) VALUES('%d', '%s', '%s', '%s');", $number, $_POST['name'], $_POST['email'], nl2br($_POST['feedback']));
                        $conn -> query($SQL);
                        echo '我們已收到您的意見！<meta http-equiv = REFRESH CONTENT = 1; url = http://localhost/about.php#contact_us>';
                        unset($_POST['submit']);
                    } else {
                        echo
                        '<b><h3>意見回函</h3></b>'
                        .'<form action = "about.php#contact_us" method = "post">'
                        .'<table style = "font-size:1.25vw;" cellpadding = 0.3125vw>'
                        .'<tr><td>您的姓名：</td><td><input type = "text" name = "name" required></td></tr>'
                        .'<tr><td>您的聯絡 e-mail：</td><td><input type = "email" name = "email" required><br></td></tr>'
                        .'<tr><td>您的意見：</td><td><textarea maxlength = 200 name = "feedback" placeholder = "最多輸入 200 個字符" required></textarea><br></td></tr>'
                        .'</table><input type = "submit" name = "submit"></form>';
                    }
                ?>
                </td></tr></table>';
                document.getElementById(id + 'Content').innerHTML += format;
            }

            function check(textarea, max){
                if (textarea.value.length > max){
                    textarea.value = textarea.value.substring(0, max); 
                }
            }
            window.onload = function(){
            var width = '37.5vw';
            var height = '34.5625vw';
            var carousel = document.getElementById('carousel');
            carousel.style.width = width;
            carousel.style.height = height;
            var pics = document.getElementById('pics');
            pics.style.width = width * pics.length;
            pics.style.height = height;
            var dots = document.getElementById('dots').getElementsByTagName('span');
            var pre = document.getElementById('pre');
            var next = document.getElementById('next');
            var index = 1;
            var turned = false;
            var time;

            function turn(offset){
                turned = true;
                var abs = Math.abs(offset)
                var new_left = parseFloat(pics.style.left) + offset;
                var total_time = 300;
                var interval = 10;
                var speed = offset / (total_time / interval);
        
                function go(){
                    if((speed < 0 && parseFloat(pics.style.left) > new_left) || (speed > 0 && parseFloat(pics.style.left) < new_left)){
                        pics.style.left = parseFloat(pics.style.left) + speed + 'vw';
                        setTimeout(go, interval);
                    } else {
                        turned = false;
                        pics.style.left = new_left + 'vw';
                        if (new_left < -(abs * dots.length)){
                            pics.style.left = -abs + 'vw';
                        }
                        else if (new_left > -abs(offset)){
                            pics.style.left = -(abs * dots.length) + 'vw';
                        }
                    }
                }
                go();
            }

            function show_dots(){
                for(var i = 0; i < dots.length; i++){
                    if (dots[i].className == 'on'){
                        dots[i].className = '';
                        break;
                    }
                }
                dots[index - 1].className = 'on';
            }

            for(var i = 0; i < dots.length; i++){
                if(!turned){
                    dots[i].onclick= function(){
                        if(this.className != 'on'){
                            var onclick_index = parseInt(this.getAttribute('index'));
                            var offset = -parseFloat(width) * (onclick_index - index);
                            turn(offset);
                            index = onclick_index;
                            show_dots();
                        }

                    }
                }
            }

            next.onclick = function(){
                if(!turned){
                    index = (index == dots.length) ? 1 : index + 1;
                    show_dots();
                    turn(-parseFloat(width));
                }
            };

            pre.onclick = function(){
                if(!turned){
                    index = (index == 1) ? dots.length : index - 1;
                    show_dots();
                    turn(parseFloat(width));
                }
            };
        
            function play(){
                time = setInterval(function(){
                    next.onclick();
                }, 3000);
            }
            function stop(){
                clearInterval(time);
            }
        
            play();
        
        };
        </script>
    </head>
    <body>
        <script type='text/javascript'>
            var headers = {'beliefs' : '核心理念', 'story' : '故事背景', 'contact_us' : '聯絡我們'};
            for (var [key, val] of Object.entries(headers)){
                formatting(key, val);
            }
            document.getElementById('beliefsContent').innerHTML = '以輕鬆有趣的漫畫形式宣導公益理念與小知識。<br>透過成員精心繪製的內容與故事帶你了解公益組織的<b>服務對象</b>以及<b>提供的服務</b>。<br><br>故事中的 NPO 協會以 NPO Channel 為原型。<br>讓我們看看<b>機器人「機咪」</b>以及來自 NPO 協會的<b>貓咪特派員「恩波」</b>接到了什麼任務吧！';
            var subheaders = {'background' : '故事背景', 'character' : '角色介紹', 'plot' : '故事發展'};
            for (var [key, val] of Object.entries(subheaders)){
                subheader('storyContent', key, val);
            }
            document.getElementById('backgroundContent').innerHTML = '在世界上，有許多弱勢族群需要幫助，<br>也有許多想要奉獻心力而加入非營利組織(nonprofit organization, NPO)的善心人士。<br>NPO 協會正是為了將兩者進行連結而生。<br>NPO 協會與許多 NPOs 合作，陪伴、傾聽那些需要幫助的對象，並為其提供服務。';
            document.getElementById('characterContent').innerHTML =
            '<div id = "carousel" onmouseover = "stop()" onmouseout = "play()">' +
            '<div id = "pics" style = "left: -37.5vw">' +
            '<img src = "img/Jimmy.png" alt = "1"/>' +
            '<img src = "img/NPO.png" alt = "1"/>' +
            '<img src = "img/Jimmy.png" alt = "2"/>' +
            '<img src = "img/NPO.png" alt = "2"/>' +
            '</div><div id = "dots"><span index = "1" class = "on"></span><span index = "2"></span></div>' +
            '<div class="turn" id="pre">&lt;</div><div class="turn" id="next">&gt;</div></div>';
            document.getElementById('plotContent').innerHTML = '　　在可愛貓咪星球上，有部分貓咪因為身體的缺陷而備受歧視，為此，NPO 協會派出了探員「恩波」去拜訪這些貓咪們，希望能夠瞭解他們的處境，進而協助其走向新的未來。<br>　　但是，在找到第一隻貓咪以前，一個會發光的箱子卻先把恩波嚇得魂飛魄散……';
            mediaTable('contact_us');
        </script>
    </body>
</html>