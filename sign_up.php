<?php
    session_start();
    if(!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title>Sakura - 注册页面</title>
</head>

<body>
<?php 
    $title="Sakura";
    $show_buttons = FALSE;
?>
<?php 
    include_once 'style.php';
    include 'header.php';
    include_once 'database_util.php';
?>

<br/>
<div>
<?php
    $f_username = '';
    $f_pwd = '';
    $f_pwd2 = '';
    $f_email = '';
    $f_nickname = '';
    $f_phone = '';
    $f_sex = '';
    $f_birthday = '';
    if(isset($_POST['user_name'])) $f_username = $_POST['user_name'];
    if(isset($_POST['user_pwd'])) $f_pwd = $_POST['user_pwd'];
    if(isset($_POST['user_pwd2'])) $f_pwd2 = $_POST['user_pwd2'];
    if(isset($_POST['user_email'])) $f_email = $_POST['user_email'];
    if(isset($_POST['user_nickname'])) $f_nickname = $_POST['user_nickname'];
    if(isset($_POST['user_phone'])) $f_phone = $_POST['user_phone'];
    if(isset($_POST['user_sex'])) $f_sex = $_POST['user_sex'];
    if(isset($_POST['user_birthday'])) $f_birthday = $_POST['user_birthday'];
    echo '<div class="form">
        <form method="post" action="">
        帐号: <input type="text" class="login" name="user_name" required oninvalid="setCustomValidity('."'请填写账号'".');" '
            . 'oninput="setCustomValidity('."''".')" value="'.$f_username.'"/>
        密码: <input type="password" class="login" name="user_pwd" required oninvalid="setCustomValidity('."'请填写密码'".');" '
            . 'oninput="setCustomValidity('."''".')" value="'.$f_pwd.'"/>
        确认密码：<input type="password" class="login" name="user_pwd2" required oninvalid="setCustomValidity('."'请填写密码'".');" '
            . 'oninput="setCustomValidity('."''".')" value="'.$f_pwd2.'"/>
        邮箱: <input type="text" class="login" name="user_email" required oninvalid="setCustomValidity('."'请填写邮箱'".');" '
            . 'oninput="setCustomValidity('."''".')" value="'.$f_email.'"/>
        手机号: <input type="text" class="login" name="user_phone" required oninvalid="setCustomValidity('."'请填写邮箱'".');" '
            . 'oninput="setCustomValidity('."''".')" value="'.$f_phone.'"/>
        昵称: <input type="text" class="login" name="user_nickname" value="'.$f_nickname.'"/>
        生日: <input type="text" class="login" name="user_birthday" value="'.$f_birthday.'"/>
        性别: <input type="text" class="login" name="user_sex" value="'.$f_sex.'"/>      
        <input type="submit" class="login" value="注册"/>
        <input type="hidden" name="call" value="12"/>
        </form>';

    if(isset($_POST['call']) and $_POST['call']=="12")
    {
        while(True)
        {
            if(!preg_match("/^[a-zA-Z0-9_]{1,32}$/", $_POST['user_name']))
            {
                echo '<font color="red">用户名请使用32字符以内的英文字母、数字、下划线！</font>';
                break;
            }
            if(!preg_match("/^[a-zA-Z0-9_]{6,32}$/", $_POST['user_pwd']))
            {
                echo '<font color="red">密码请使用6-32字符的英文字母、数字、下划线！</font>';
                break;
            }
            if(!preg_match("/^[a-zA-Z0-9_.]+@[a-zA-Z0-9_.]+$/", $_POST['user_email']) || 
                    !preg_match("/^.{3,32}$/", $_POST['user_email']))
            {
                echo '<font color="red">请输入正确的邮箱地址！</font>';
                break;
            }
            if(!preg_match("/^[0-9]{1,32}$/", $_POST['user_phone']))
            {
                echo '<font color="red">请输入正确的手机号！</font>';
                break;
            }
            if(!preg_match("/^.{0,32}$/", $_POST['user_nickname']))
            {
                echo '<font color="red">昵称长度不得超过32！</font>';
                break;
            }
            if($_POST['user_pwd'] != $_POST['user_pwd2']) 
            {
                echo '<font color="red">两次输入的密码不同！</font>';
                break;
            }

            $uid = query_one($conn,'user_id','sakura.user_info',
                    'user_name','"'.$_POST['user_name'].'"');
            if($uid != NULL)
            {
                echo '<font color="red">注册失败：账号已存在！</font>';
                break;
            }
            $uid = query_one($conn,'user_email','sakura.user_info',
                    'user_email','"'.$_POST['user_email'].'"');
            if($uid != NULL) 
            {
                echo '<font color="red">注册失败：该邮箱已使用！</font>';
                break;
            }
            $uid = query_one($conn,'user_phone','sakura.user_info',
                    'user_phone','"'.$_POST['user_phone'].'"');
            if($uid != NULL) 
            {
                echo '<font color="red">注册失败：该手机号已使用！</font>';
                break;
            }

            if($_POST['user_nickname'] == '') $_POST['user_nickname'] = '萝卜';
            $perm = '1';
            $time = time();
            execute_sql($conn, "insert into sakura.user_info "
                    . "(user_name,user_pwd,user_email,user_nickname,user_permission,"
                    . "user_birthday,user_sex,user_phone,user_createtime,user_state) "
                    . "values ('".
                    $_POST['user_name']."',PASSWORD('".
                    $_POST['user_pwd']."'),'".
                    $_POST['user_email']."','".$_POST['user_nickname']."',"
                    .$perm.",'".$_POST['user_birthday']."','".$_POST['user_sex']
                    ."','".$_POST['user_phone']."',".$time.",1)");
            $_SESSION['user_name'] = $_POST['user_name'];
            echo "注册成功！";
            echo '<form method="post" action="/sign_in.php">
                    <input type="submit" class="login2" value="点此跳转至登录页面"/>
                    <input type="hidden" name="user_name" value="'.$_POST['user_name'].'"/>
                    <input type="hidden" name="user_pwd" value="'.$_POST['user_pwd'].'"/>
                    </form>';
            break;
        }       
    }
?>

</div>
</body>
</html>