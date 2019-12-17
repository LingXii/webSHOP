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
    if($uid != $_SESSION['uid']) die('访问错误：权限不足！')
?>

<?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="31")
        {
            $sql = "DELETE FROM sakura.deal WHERE deal_id = ".$_POST['did'];
            execute_sql($conn, $sql);
        }
        else if($_POST['call']=="32")
        {
            $sql = "UPDATE sakura.deal SET deal_state = 2 WHERE deal_id = ".$_POST['did'];
            execute_sql($conn, $sql);
            $time = time();
            $sql = "UPDATE sakura.deal SET deal_time = ".$time." WHERE deal_id = ".$_POST['did'];
            execute_sql($conn, $sql);
            $iid = query_a($conn,'deal_iid','sakura.deal',"deal_id = ".$_POST['did']);
            $sql = "UPDATE sakura.item SET item_state = 2 WHERE item_id = ".$iid;
            execute_sql($conn, $sql);
        }
        else if($_POST['call']=="33")
        {
            $sql = "DELETE FROM sakura.deal WHERE deal_state = 1 and deal_uid = ".$uid;
            execute_sql($conn, $sql);
        }
        else if($_POST['call']=="34")
        {
            $sql = "UPDATE sakura.deal SET deal_state = 2 WHERE deal_state = 1 and deal_uid = ".$uid;
            execute_sql($conn, $sql);
            $time = time();
            $sql = "UPDATE sakura.deal SET deal_time = ".$time." WHERE deal_state = 1 and deal_uid = ".$uid;
            execute_sql($conn, $sql);
            $sql = "UPDATE sakura.item SET item_state = 2 WHERE item_id in (SELECT deal_iid "
                    . "from sakura.deal WHERE deal_state = 1 and deal_uid = ".$uid.")";
            execute_sql($conn, $sql);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: shopcar.php?uid='.$uid);
    }
?>
    
<?php 
    include_once 'style.php';
    include 'header.php';
?>  
    
<br/>
<div>   
    <form method="post" action="" style="float:right">
    <input type="submit" class="button" value="清空所有"/>
    <input type="hidden" name="call" value="33"/>
    </form>
    <form method="post" action="" style="float:right">
    <input type="submit" class="button" value="购买所有"/>
    <input type="hidden" name="call" value="34"/>
    </form>
    <h1 style="margin:10px">购物车</h1>
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

$sql = "SELECT * FROM sakura.deal WHERE deal_state = 1 and deal_uid = ".$uid." ORDER BY deal_time DESC";
$item_val = mysqli_query($conn,$sql);
if(! $item_val) die("查询数据库失败：".mysqli_error($conn));
$row_cnt = 0;
while($item = mysqli_fetch_array($item_val))
{
    $did = $item[0];
    $iid = $item[1];
    $row_val = mysqli_query($conn,"SELECT * FROM sakura.item WHERE item_id = ".$iid);
    $row = mysqli_fetch_array($row_val);
    $row_cnt += 1;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    echo '<td width="10%">'.$row[1].'</td>';
    echo '<td width="15%">'.$row[2].'</td>';
    echo '<td width="25%">'.$row[3].'</td>';
    echo '<td width="10%">'.$row[5].'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[4]);
    echo '<td width="10%"><a href="/user_space.php?uid='.$row[4].'">'.$user_nickname.'</a></td>';
    
    $comments = '';    
    $sql = "SELECT deal_evaluation FROM sakura.deal WHERE deal_iid = ".$iid;
    $c_val = mysqli_query($conn,$sql);
    while($c_row = mysqli_fetch_array($c_val))
    {
        $com = $c_row[0];
        if($com != '') $comments = $comments.$com."<br/><br/>";
    }
    echo '<td width="22%">'.$comments.'</td>';     
    
    $operation = '';
    $shopcar_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="移出购物车"/>'
        .'<input type="hidden" name="call" value="31"/>'
        .'<input type="hidden" name="did" value="'.$did.'"/>'
        .'</form>';
    $buy_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="下单购买"/>'
        .'<input type="hidden" name="call" value="32"/>'
        .'<input type="hidden" name="did" value="'.$did.'"/>'
        .'</form>';
    echo '<td width="8%">'.$buy_form.$shopcar_form.'</td>';  
}
echo '</table>';
?>
</div>  
    
</body>
</html>