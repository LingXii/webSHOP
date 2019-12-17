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
    if(isset($_GET['mode'])) $mode = $_GET['mode'];
    else $mode = 0;
?>

<?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="51")
        {
            $mode = 1;
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: item_manage.php?uid='.$uid.'&mode='.$mode);
        }
        else if($_POST['call']=="52")
        {
            $mode = 2;
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: item_manage.php?uid='.$uid.'&mode='.$mode);
        }
        else if($_POST['call']=="53")
        {
            $mode = 3;
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: item_manage.php?uid='.$uid.'&mode='.$mode);
        }
        else if($_POST['call']=="54")
        {
            array_splice($_POST, 0, count($_POST));
            header('Location: item_editor.php?uid='.$uid);
        }
        else if($_POST['call']=="28")
        {
            $iid = $_POST['iid'];
            $sql = "DELETE FROM sakura.item WHERE item_id = ".$iid;
            execute_sql($conn, $sql);
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: item_manage.php?uid='.$uid.'&mode='.$mode);
        }
        else if($_POST['call']=="29")
        {
            $iid = $_POST['iid'];
            array_splice($_POST, 0, count($_POST));
            header('Location: item_editor.php?uid='.$_SESSION['uid'].'&iid='.$iid);
        }
    }
?>
    
<?php 
    include_once 'style.php';
    include 'header.php';
?>  
    
<br/>
<div>   
    <form method="post" action="" style="float:right">
    <input type="submit" class="button" value="只看违禁商品"/>
    <input type="hidden" name="call" value="53"/>
    </form>
    <form method="post" action="" style="float:right">
    <input type="submit" class="button" value="只看已售商品"/>
    <input type="hidden" name="call" value="52"/>
    </form>
    <form method="post" action="" style="float:right">
    <input type="submit" class="button" value="只看未售商品"/>
    <input type="hidden" name="call" value="51"/>
    </form>
    <form method="post" action="" style="float:right">
    <input type="submit" class="button" value="发布新商品"/>
    <input type="hidden" name="call" value="54"/>
    </form>
    <h1 style="margin:10px">商品列表</h1>
    <p style="margin:10px"><font color="blue">蓝字</font>为已售，<font color="red">红字</font>为违禁</p>
</div>

<br />
<div>
<?php
echo '<table border="1" id="posts"><tr>';
echo '<th><b>商品类型</b></th>';
echo '<th><b>商品名称</b></th>';
echo '<th><b>商品描述</b></th>';
echo '<th><b>商品价格￥</b></th>';
echo '<th><b>卖家</b></th>';
echo '<th><b>评价</b></th>';
echo '<th><b>操作</b></th>';
echo '</tr>';

if($mode == 0) 
    $sql = "SELECT * FROM sakura.item WHERE item_seller = ".$uid." ORDER BY item_id DESC";
else if($mode == 1) 
    $sql = "SELECT * FROM sakura.item WHERE item_state = 1 and item_seller = ".$uid." ORDER BY item_id DESC";
else if($mode == 2) 
    $sql = "SELECT * FROM sakura.item WHERE item_state = 2 and item_seller = ".$uid." ORDER BY item_id DESC";
else if($mode == 3) 
    $sql = "SELECT * FROM sakura.item WHERE item_state = 3 and item_seller = ".$uid." ORDER BY item_id DESC";
$item_val = mysqli_query($conn,$sql);
if(! $item_val) die("查询数据库失败：".mysqli_error($conn));
$row_cnt = 0;
while($row = mysqli_fetch_array($item_val))
{
    $row_cnt += 1;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    echo '<td width="10%">'.$row[1].'</td>';
    if($row[7] == 1) echo '<td width="15%">'.$row[2].'</td>';
    else if($row[7] == 2) echo '<td width="15%"><font color="blue">'.$row[2].'</font></td>';
    else if($row[7] == 3) echo '<td width="15%"><font color="red">'.$row[2].'</font></td>';
    echo '<td width="25%">'.$row[3].'</td>';
    echo '<td width="10%">'.$row[5].'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[4]);
    echo '<td width="10%"><a href="/user_space.php?uid='.$row[4].'">'.$user_nickname.'</a></td>';
    
    $comments = '';    
    $sql = "SELECT deal_evaluation FROM sakura.deal WHERE deal_iid = ".$row[0];
    $c_val = mysqli_query($conn,$sql);
    while($c_row = mysqli_fetch_array($c_val))
    {
        $com = $c_row[0];
        if($com != '') $comments = $comments.$com."<br/><br/>";
    }
    echo '<td width="22%">'.$comments.'</td>';   
    
    $operation = '';
    $delete_form = '<form method="post" action="">'
        .'<input type="submit" class="warn" value="下架此商品"/>'
        .'<input type="hidden" name="call" value="28"/>'
        .'<input type="hidden" name="iid" value="'.$row[0].'"/>'
        .'</form>';
    $change_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="修改信息"/>'
        .'<input type="hidden" name="call" value="29"/>'
        .'<input type="hidden" name="iid" value="'.$row[0].'"/>'
        .'</form>';
    if($row[7] == 1) $operation = $change_form.$delete_form;
    echo '<td width="8%">'.$operation.'</td>';  
}
echo '</table>';
?>
</div>  
    
</body>
</html>