<?php
    session_start();
    if(!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title>Sakura</title>
</head>

<body>
<?php 
    $title="Sakura";
    $show_buttons = TRUE;
?>
<?php 
    include_once 'database_util.php';
    include_once 'image_util.php';
?>
<?php
    if (!isset($_GET['uid'])) die("拒绝访问！");
    $uid = $_GET['uid'];
    if (query_one($conn,'user_id','sakura.user_info','user_id', $uid) == NULL)
        die("查无此人！");
?>
    
<?php
    if(isset($_POST['call']))
    {
        if ($_POST['call']=="14")
        {
            $sql = "UPDATE sakura.user_info SET user_state = 3 WHERE user_id = ".$_POST['uid'];
            execute_sql($conn, $sql);
            array_splice($_POST, 0, count($_POST));
            header('Location: user_space.php?uid='.$_GET['uid']);
        }
        else if ($_POST['call']=="15")
        {
            $_SESSION['uid'] = 0;
            header('Location: index.php');
        }
        
        else if($_POST['call']=="34")
        {
            if ($_FILES["file"]["error"] == 1)
                die('文件大小不可超过2MB');
            if ($_FILES["file"]["error"] > 1)
                die("Error: " . $_FILES["file"]["error"] . "<br />");
            $division = pathinfo($_FILES['file']['name']);
            $extensionName = $division['extension']; 
            if ($extensionName != "jpg" && $extensionName != "jpeg" && $extensionName != "png")
                die('请上传jpg, jpeg, png格式文件');
            deal(200,200,$_FILES['file']['tmp_name'],$_SESSION['uid']."_200");
            deal(60,60,$_FILES['file']['tmp_name'],$_SESSION['uid']."_60");
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            array_splice($_FILES, 0, count($_POST));
            header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );  
            header("Cache-Control: no-cache, must-revalidate" );  // 清除浏览器缓存，否则显示出错
            header('Location: user_space.php?uid='.$_GET['uid']);
        }      
    }
    include_once 'style.php';
    include 'header.php';  
?>

<?php 
    $user_name = query_one($conn,'user_name','sakura.user_info','user_id',$uid);
    $nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$uid);   
    echo '<div class="userright">';
    echo '<p>账号：'.$user_name.'</p>';
    echo '<p>uid：'.$uid.'</p>';
    
    $email = query_one($conn,'user_email','sakura.user_info','user_id',$uid);
    $phone = query_one($conn,'user_phone','sakura.user_info','user_id',$uid);
    $sex = query_one($conn,'user_sex','sakura.user_info','user_id',$uid);
    $birthday = query_one($conn,'user_birthday','sakura.user_info','user_id',$uid);
    $permission = query_one($conn,'user_permission','sakura.user_info','user_id',$uid);
    $state = query_one($conn,'user_state','sakura.user_info','user_id',$uid); 
    $person_info = '<p>邮箱：'.$email.'</p>'.'<p>手机号：'.$phone.'</p>'.'<p>性别：'.$sex.'</p>'.'<p>生日：'.$birthday.'</p>';
    
    if($permission == 1) echo '<p>身份：买家</p>';
    if($permission == 2) echo '<p>身份：卖家</p>';
    if($permission == 3) echo '<p>身份：管理员</p>';
    if($state == 1) echo '<p>状态：正常</p>';
    if($state == 2) echo '<p>状态：封禁</p>';
    if($state == 3) echo '<p>状态：卖家资质审核中</p>';
    
    if ($_SESSION['uid'] == $_GET['uid'] || $permission == 2)
    {
        echo $person_info;
    }
    if ($_SESSION['uid'] == $_GET['uid'])
    {
        echo '<form method="post" action="whisper.php?uid='.$_GET['uid'].'">
        <input type="submit" class="button" value="查看消息" />
        </form>';
    }
    if ($_SESSION['uid'] > 0 && $_SESSION['uid'] != $_GET['uid'])
    {
        echo '<form method="post" action="whisper.php?uid='.$_GET['uid'].'">
        <input type="submit" class="button" value="发消息" />
        <input type="hidden" name="to" value='.$_GET['uid'].' />
        <input type="hidden" name="entrance_uid" value='.$_GET['uid'].' />
        </form>';
    }

    if ($_SESSION['uid'] == $_GET['uid'])
    {
        echo '<form method="get" action="shopcar.php">
        <input type="submit" class="button" value="查看购物车" />
        <input type="hidden" name="uid" value="'.$_GET['uid'].'" />
        </form>';
        echo '<form method="get" action="deal_buyer.php">
        <input type="submit" class="button" value="查看交易订单" />
        <input type="hidden" name="uid" value="'.$_GET['uid'].'" />
        </form>';
        
        if($permission == 1)
        {
            echo '<form method="post" action="">
            <input type="submit" class="button" value="申请成为卖家" />
            <input type="hidden" name="call" value="14" />
            <input type="hidden" name="uid" value="'.$_GET['uid'].'" />
            </form>';
        }
        
        if($permission == 2)
        {
            echo '<form method="get" action="item_manage.php">
            <input type="submit" class="button" value="管理商品" />
            <input type="hidden" name="uid" value="'.$_GET['uid'].'" />
            </form>';
            echo '<form method="get" action="deal_seller.php">
            <input type="submit" class="button" value="管理交易订单" />
            <input type="hidden" name="uid" value="'.$_GET['uid'].'" />
            </form>';
        }
        
        if($permission == 3)
        {
            echo '<form method="get" action="user_manage.php">
            <input type="submit" class="button" value="管理用户" />
            <input type="hidden" name="uid" value="'.$_GET['uid'].'" />
            </form>';
            echo '<form method="get" action="deal_manager.php">
            <input type="submit" class="button" value="管理交易" />
            <input type="hidden" name="uid" value="'.$_GET['uid'].'" />
            </form>';
        }
        
        echo '<form method="post" action="">
        <input type="submit" class="button" value="退出登录" />
        <input type="hidden" name="call" value="15" />
        </form>';
    }
    echo '</div>';
    
    echo '<div class="userleft">';
    show_headpic_200($_GET['uid']);  
    if ($_SESSION['uid'] == $_GET['uid'])
    {
        echo '<form action="" method="post" enctype="multipart/form-data">
            <input type="file"name="file"/> 
            <input type="submit" value="更改头像"/>
            <input type="hidden" name="call" value="34"/>
            </form>';
    }
    echo '</div>';
?>

</body>
</html>