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
    include_once 'database_util.php';
?>
<?php    
    $title="Sakura";
    $show_buttons = TRUE;
    if(!isset($_GET['uid'])) header('Location: sign_in.php');
    $uid = $_GET['uid'];
    if($uid != $_SESSION['uid']) die('访问错误：权限不足！');
    if(!isset($_GET['did'])) die('访问错误：权限不足！');
    $did = $_GET['did'];
    $buyer_uid = query_one($conn,'deal_uid','sakura.deal','deal_id',$did);
    if($uid != $buyer_uid) die('访问错误：权限不足！');
?>

<div>
    <?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="51")
        {
            $time = time();
            $state = '1';           
            $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$uid);            
            $content = '<a href="/user_space.php?uid='.$uid.'">'.$user_nickname.'</a>：';
            $content = $content.$_POST['content'];
            $content = str_replace("\n","<br/>",$content); // 在网页端正确显示换行符
            if(isset($_FILES["files"]) && $_FILES["files"]["name"][0]!='')
            {
                $content = $content.'<br/>附加文件：';
                for($i=0;$i<count($_FILES["files"]["name"]);$i++) // 依次上传文件
                {
                    $division = pathinfo($_FILES['files']['name'][$i]);
                    $extensionName = $division['extension']; 
                    $file_url = 'files/'.md5(uniqid(microtime(true),true)).'.'.$extensionName;
                    move_uploaded_file($_FILES["files"]["tmp_name"][$i], $file_url);
                    $content = $content.'<br/><a href="/'.$file_url.'">'.$_FILES["files"]["name"][$i].'</a>';
                }
            }
            $sql = "UPDATE sakura.deal SET deal_evaluation = '".$content."' WHERE deal_id = ".$did;
            execute_sql($conn, $sql);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        array_splice($_FILES, 0, count($_POST));
        header('Location: deal_buyer.php?uid='.$uid);
    }
    
    include_once 'style.php';
    include 'header.php';  
?>
</div>

<br />
<div id="editor" class="editor">
<?php
echo '<form method="post" action="" enctype="multipart/form-data">';
echo '评论内容<br />'.
    '<textarea cols="50" rows="10" name="content"></textarea>'; 
echo '<input type="hidden" name="call" value="51"/>'.
    '<p style="display:inline-block">上传附件：请一次性选择需要上传的所有文件</p>'.
    '<input type="file" name="files[]" multiple=""/> ';
echo '<input type="submit" value="发表评论"/>'; 
echo '</form>';
?>
</div>

</body>
</html>