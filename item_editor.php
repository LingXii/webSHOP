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
    $permission = query_one($conn,'user_permission','sakura.user_info','user_id',$uid);
    if($uid != $_SESSION['uid'] || $permission != 2) die('访问错误：权限不足！');
    if(isset($_GET['iid']))
    {
        $seller_uid = query_one($conn,'item_seller','sakura.item','item_id',$_GET['iid']);
        if($uid != $seller_uid) die('访问错误：权限不足！');
        $item_state = query_one($conn,'item_state','sakura.item','item_id',$_GET['iid']);
        if($item_state == 2) die('已售商品无法修改信息！');
    }
?>

    
<?php
    if(isset($_POST['call']) and $_POST['call']=="19")
    {
        if(!is_numeric($_POST['item_price']))
        {
            die('<font color="red">请输入正确的价格！</font>');
        }
        if(!isset($_GET['iid']))
        {
            $time = time();
            $sql = "insert into sakura.item (item_type,item_name,item_description,"
                   . "item_seller,item_price,item_createtime,item_state) "
                   ."value ('".$_POST['item_type']."','".$_POST['item_name']."','"
                   .$_POST['item_descr']."'," .$uid."," .$_POST['item_price']."," .$time.",1)";
            execute_sql($conn, $sql);     
        }
        else
        {
            $sql = "UPDATE sakura.item SET item_type = '".$_POST['item_type']
                    ."',item_name = '".$_POST['item_name']."',item_description = '".$_POST['item_descr']
                    ."',item_price = ".$_POST['item_price']." WHERE item_id = ".$_GET['iid'];
                execute_sql($conn, $sql);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: item_manage.php?uid='.$uid);
    }
    
    include_once 'style.php';
    include 'header.php';
?>

<br/>
<div class="form">
    <?php
        $s1 = 'oninvalid="setCustomValidity('."'";
        $s2 = "'".')" oninput="setCustomValidity('."''".')"';
        if(isset($_GET['iid']))
        {
            $iid = $_GET['iid'];
            $row_val = mysqli_query($conn,"SELECT * FROM sakura.item WHERE item_id = ".$iid);
            $row = mysqli_fetch_array($row_val);
            echo '<form method="post" action="">
                类型: <input type="text" class="login" name="item_type" value="'.$row[1].
                    '" required '.$s1.'不可为空'.$s2.'/>
                名称: <input type="text" class="login" name="item_name" value="'.$row[2].
                    '" required '.$s1.'不可为空'.$s2.'/>
                描述: <input type="text" class="login" name="item_descr" value="'.$row[3].
                    '" required '.$s1.'不可为空'.$s2.'/>
                价格: <input type="text" class="login" name="item_price" value="'.$row[5].
                    '" required '.$s1.'不可为空'.$s2.'/>
                <input type="submit" class="login" value="提交商品信息"/>
                <input type="hidden" name="call" value="19"/>
                </form>';
        }
        else
        {
            echo '<form method="post" action="">
                类型: <input type="text" class="login" name="item_type" required '.$s1.'不可为空'.$s2.'/>
                名称: <input type="text" class="login" name="item_name" required '.$s1.'不可为空'.$s2.'/>
                描述: <input type="text" class="login" name="item_descr" required '.$s1.'不可为空'.$s2.'/>
                价格: <input type="text" class="login" name="item_price" required '.$s1.'不可为空'.$s2.'/>
                <input type="submit" class="login" value="提交商品信息"/>
                <input type="hidden" name="call" value="19"/>
                </form>';
        }
    ?>
</div>

</body>
</html>