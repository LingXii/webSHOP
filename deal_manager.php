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
    if($uid != $_SESSION['uid'] || $permission != 3) die('访问错误：权限不足！');
    
    if(isset($_GET['page'])) $page = $_GET['page'];
    else $page = 1;
    $item_per_page = 30;
?>

<?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="46")
        {
            $sql = "UPDATE sakura.deal SET deal_state = 6 WHERE deal_id = ".$_POST['did'];
            execute_sql($conn, $sql);
            $iid = query_a($conn,'deal_iid','sakura.deal',"deal_id = ".$_POST['did']);
            $sql = "UPDATE sakura.item SET item_state = 1 WHERE item_id = ".$iid;
            execute_sql($conn, $sql);
        }
        else if($_POST['call']=="47")
        {
            $sql = "UPDATE sakura.deal SET deal_state = 8 WHERE deal_id = ".$_POST['did'];
            execute_sql($conn, $sql);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: deal_manager.php?uid='.$uid);
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
$item_num = query_num($conn,'sakura.deal',"deal_id > 0");
$page_num = ceil($item_num/$item_per_page);
if($page_num <= 0) $page_num = 1;

echo '<table border="1" id="posts"><tr>';
echo '<th><b>订单状态</b></th>';
echo '<th><b>商品名称</b></th>';
echo '<th><b>商品描述</b></th>';
echo '<th><b>商品价格￥</b></th>';
echo '<th><b>买家</b></th>';
echo '<th><b>卖家</b></th>';
echo '<th><b>评价</b></th>';
echo '<th><b>操作</b></th>';
echo '</tr>';

$sql = "SELECT * FROM sakura.deal WHERE deal_state = 7 ORDER BY deal_state ASC, deal_time DESC";
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
    if($row_cnt <= ($page-1)*$item_per_page) continue;
    if($row_cnt > $page*$item_per_page) break;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    switch($state)
    {
        case 2: echo '<td width="10%">已下单</td>';break;
        case 3: echo '<td width="10%">卖家已发货</td>';break;
        case 4: echo '<td width="10%">已完成</td>';break;
        case 5: echo '<td width="10%">申请退货</td>';break;
        case 6: echo '<td width="10%">已退货</td>';break;
        case 7: echo '<td width="10%"><font color="red">申诉中</font></td>';break;
        case 8: echo '<td width="10%">交易已终结</td>';break;
    }   
    echo '<td width="15%">'.$row[2].'</td>';
    echo '<td width="23%">'.$row[3].'</td>';
    echo '<td width="10%">'.$row[5].'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$item[2]);
    echo '<td width="7%"><a href="/user_space.php?uid='.$item[2].'">'.$user_nickname.'</a></td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[4]);
    echo '<td width="7%"><a href="/user_space.php?uid='.$row[4].'">'.$user_nickname.'</a></td>';
    echo '<td width="20%">'.$item[4].'</td>';      
    $operation = '';
    $agree_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="支持买家退货"/>'
        .'<input type="hidden" name="call" value="46"/>'
        .'<input type="hidden" name="did" value="'.$did.'"/>'
        .'</form>';
    $reject_form = '<form method="post" action="">'
        .'<input type="submit" class="warn" value="驳回退货申请"/>'
        .'<input type="hidden" name="call" value="47"/>'
        .'<input type="hidden" name="did" value="'.$did.'"/>'
        .'</form>';
    switch($state)
    {
        case 7: $operation = $agree_form.$reject_form;break;
    }  
    echo '<td width="8%">'.$operation.'</td>';  
}

$sql = "SELECT * FROM sakura.deal WHERE deal_state <> 7 ORDER BY deal_state ASC, deal_time DESC";
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
    if($row_cnt <= ($page-1)*$item_per_page) continue;
    if($row_cnt > $page*$item_per_page) break;
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
    echo '<td width="23%">'.$row[3].'</td>';
    echo '<td width="10%">'.$row[5].'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$item[2]);
    echo '<td width="7%"><a href="/user_space.php?uid='.$item[2].'">'.$user_nickname.'</a></td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[4]);
    echo '<td width="7%"><a href="/user_space.php?uid='.$row[4].'">'.$user_nickname.'</a></td>';
    $comments = '';
    echo '<td width="20%">'.$comments.'</td>';     
    $operation = ''; 
    echo '<td width="8%">'.$operation.'</td>';  
}
echo '</table>';
?>
</div>  

<br />
<div>
<?php

if($page==1)
    echo '<a href="/deal_manager.php?uid='.$uid.'&page=1" class="npage_btn" style="margin-left:15px;">第一页</a>';
else
    echo '<a href="/deal_manager.php?uid='.$uid.'&page=1" class="page_btn" style="margin-left:15px;">第一页</a>';

if($page==1)
{
    echo '<a href="/deal_manager.php?uid='.$uid.'&page=1" class="npage_btn">1</a>';
    if($page_num>1)
        echo '<a href="/deal_manager.php?uid='.$uid.'&page=2" class="page_btn">2</a>';
}
else if($page==$page_num)
{
    if($page>1)
        echo '<a href="/deal_manager.php?uid='.$uid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/deal_manager.php?uid='.$uid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
}
else
{
    echo '<a href="/deal_manager.php?uid='.$uid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/deal_manager.php?uid='.$uid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
    echo '<a href="/deal_manager.php?uid='.$uid.'&page='.($page+1).'" class="page_btn">'.($page+1).'</a>';
}

if($page==$page_num)
    echo '<a href="/deal_manager.php?uid='.$uid.'&page='.$page_num.'" class="npage_btn">最后一页</a>';
else
    echo '<a href="/deal_manager.php?uid='.$uid.'&page='.$page_num.'" class="page_btn">最后一页</a>';
?>
</div>
    
</body>
</html>