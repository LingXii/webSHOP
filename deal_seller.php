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
?>

<?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="40")
        {
            $sql = "UPDATE sakura.deal SET deal_state = 3 WHERE deal_id = ".$_POST['did'];
            execute_sql($conn, $sql);
        }
        else if($_POST['call']=="41")
        {
            $sql = "UPDATE sakura.deal SET deal_state = 8 WHERE deal_id = ".$_POST['did'];
            execute_sql($conn, $sql);
            $iid = query_a($conn,'deal_iid','sakura.deal',"deal_id = ".$_POST['did']);
            $sql = "UPDATE sakura.item SET item_state = 1 WHERE item_id = ".$iid;
            execute_sql($conn, $sql);
        }        
        else if($_POST['call']=="42")
        {
            $sql = "UPDATE sakura.deal SET deal_state = 6 WHERE deal_id = ".$_POST['did'];
            execute_sql($conn, $sql);
            $iid = query_a($conn,'deal_iid','sakura.deal',"deal_id = ".$_POST['did']);
            $sql = "UPDATE sakura.item SET item_state = 1 WHERE item_id = ".$iid;
            execute_sql($conn, $sql);
        }
        else if($_POST['call']=="43")
        {
            $sql = "UPDATE sakura.deal SET deal_state = 7 WHERE deal_id = ".$_POST['did'];
            execute_sql($conn, $sql);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: deal_seller.php?uid='.$uid);
    }
?>
    
<?php 
    include_once 'style.php';
    include 'header.php';
?>  
    
<br/>
<div>   
    <h1 style="margin:10px">交易订单</h1>
</div>

<br />
<div>
<?php
echo '<table border="1" id="posts"><tr>';
echo '<th><b>订单状态</b></th>';
echo '<th><b>商品名称</b></th>';
echo '<th><b>商品描述</b></th>';
echo '<th><b>商品价格￥</b></th>';
echo '<th><b>买家</b></th>';
echo '<th><b>评价</b></th>';
echo '<th><b>操作</b></th>';
echo '</tr>';

$sql = "SELECT * FROM sakura.deal WHERE deal_state in (2,5) and deal_iid in ("
        ."SELECT item_id FROM sakura.item WHERE item_seller = ".$uid.")"
        ." ORDER BY deal_state ASC, deal_time DESC";
$item_val = mysqli_query($conn,$sql);
if(! $item_val) die("查询数据库失败：".mysqli_error($conn));
$row_cnt = 0;
while($item = mysqli_fetch_array($item_val))
{
    $did = $item[0];
    $iid = $item[1];
    $state = $item[5];
    $row_val = mysqli_query($conn,"SELECT * FROM sakura.item WHERE item_id = ".$iid);
    $row = mysqli_fetch_array($row_val);
    $row_cnt += 1;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    switch($state)
    {
        case 2: echo '<td width="10%">已下单</td>';break;
        case 3: echo '<td width="10%">卖家已发货</td>';break;
        case 4: echo '<td width="10%">已完成</td>';break;
        case 5: echo '<td width="10%">申请退货</td>';break;
        case 6: echo '<td width="10%">已退货</td>';break;
        case 7: echo '<td width="10%">申诉中</td>';break;
        case 8: echo '<td width="10%">交易已终结</td>';break;
    }   
    echo '<td width="15%">'.$row[2].'</td>';
    echo '<td width="25%">'.$row[3].'</td>';
    echo '<td width="10%">'.$row[5].'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$item[2]);
    echo '<td width="10%"><a href="/user_space.php?uid='.$item[2].'">'.$user_nickname.'</a></td>';
    echo '<td width="22%">'.$item[4].'</td>';       
    $operation = '';
    $send_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="发货"/>'
        .'<input type="hidden" name="call" value="40"/>'
        .'<input type="hidden" name="did" value="'.$did.'"/>'
        .'</form>';
    $cancel_form = '<form method="post" action="">'
        .'<input type="submit" class="warn" value="取消交易"/>'
        .'<input type="hidden" name="call" value="41"/>'
        .'<input type="hidden" name="did" value="'.$did.'"/>'
        .'</form>';
    $agree_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="同意退货"/>'
        .'<input type="hidden" name="call" value="42"/>'
        .'<input type="hidden" name="did" value="'.$did.'"/>'
        .'</form>';
    $reject_form = '<form method="post" action="">'
        .'<input type="submit" class="warn" value="拒绝退货"/>'
        .'<input type="hidden" name="call" value="43"/>'
        .'<input type="hidden" name="did" value="'.$did.'"/>'
        .'</form>';
    switch($state)
    {
        case 2: $operation = $send_form.$cancel_form;break;
        case 5: $operation = $agree_form.$reject_form;break;
    }  
    echo '<td width="8%">'.$operation.'</td>';  
}

$sql = "SELECT * FROM sakura.deal WHERE deal_state in (3,4,6,7,8) and deal_iid in ("
        ."SELECT item_id FROM sakura.item WHERE item_seller = ".$uid.")"
        ." ORDER BY deal_state ASC, deal_time DESC";
$item_val = mysqli_query($conn,$sql);
if(! $item_val) die("查询数据库失败：".mysqli_error($conn));
while($item = mysqli_fetch_array($item_val))
{
    $did = $item[0];
    $iid = $item[1];
    $state = $item[5];
    $row_val = mysqli_query($conn,"SELECT * FROM sakura.item WHERE item_id = ".$iid);
    $row = mysqli_fetch_array($row_val);
    $row_cnt += 1;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    switch($state)
    {
        case 2: echo '<td width="10%">已下单</td>';break;
        case 3: echo '<td width="10%">卖家已发货</td>';break;
        case 4: echo '<td width="10%">已完成</td>';break;
        case 5: echo '<td width="10%">申请退货</td>';break;
        case 6: echo '<td width="10%">已退货</td>';break;
        case 7: echo '<td width="10%">申诉中</td>';break;
        case 8: echo '<td width="10%">交易已终结</td>';break;
    }   
    echo '<td width="15%">'.$row[2].'</td>';
    echo '<td width="25%">'.$row[3].'</td>';
    echo '<td width="10%">'.$row[5].'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$item[2]);
    echo '<td width="10%"><a href="/user_space.php?uid='.$item[2].'">'.$user_nickname.'</a></td>';
    $comments = '';
    echo '<td width="22%">'.$comments.'</td>';      
    $operation = ''; 
    echo '<td width="8%">'.$operation.'</td>';  
}
echo '</table>';
?>
</div>  
    
</body>
</html>