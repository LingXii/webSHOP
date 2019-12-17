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
    if(isset($_GET['page'])) $page = $_GET['page'];
    else $page = 1;
    $item_per_page = 30;
    $search = '';
    if(isset($_GET['search'])) $search = $_GET['search'];
?>

<?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="25")
        {
            $search = $_POST['search'];
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: index.php?search='.$search);
        }
        else if($_POST['call']=="26")
        {
            $iid = $_POST['iid'];
            if(query_num($conn,'sakura.deal',"deal_uid = ".$_SESSION['uid']." and deal_iid = ".$iid) > 0)
                die("无法加入购物车：商品已在购物车内或此商品的订单已存在且交易终结！");
            $time = time();
            $sql = "insert into sakura.deal (deal_iid,deal_uid,deal_time,deal_state) "
                        ."value (".$iid.",".$_SESSION['uid'].",".$time.",1)";
            execute_sql($conn, $sql);
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: index.php?search='.$search);
        }
        else if($_POST['call']=="27")
        {
            $iid = $_POST['iid'];
            $state = query_a($conn,'deal_state','sakura.deal',"deal_uid = ".$_SESSION['uid']." and deal_iid = ".$iid);
            if($state == 8) die("无法下单购买：此商品的订单已存在且交易终结！");
            if($state == 1)
            {
                $did = query_a($conn,'deal_id','sakura.deal',"deal_uid = ".$_SESSION['uid']." and deal_iid = ".$iid);
                $sql = "UPDATE sakura.deal SET deal_state = 2 WHERE deal_id = ".$did;
                execute_sql($conn, $sql);
                $time = time();
                $sql = "UPDATE sakura.deal SET deal_time = ".$time." WHERE deal_id = ".$did;
                execute_sql($conn, $sql);
                $sql = "UPDATE sakura.item SET item_state = 2 WHERE item_id = ".$iid;
                execute_sql($conn, $sql);
            }
            else
            {
                $iid = $_POST['iid'];
                $time = time();
                $sql = "insert into sakura.deal (deal_iid,deal_uid,deal_time,deal_state) "
                            ."value (".$iid.",".$_SESSION['uid'].",".$time.",2)";
                execute_sql($conn, $sql);
                $sql = "UPDATE sakura.item SET item_state = 2 WHERE item_id = ".$iid;
                execute_sql($conn, $sql);
            }  
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: index.php?search='.$search);
        }
        else if($_POST['call']=="28")
        {
            $iid = $_POST['iid'];
            $sql = "DELETE FROM sakura.item WHERE item_id = ".$iid;
            execute_sql($conn, $sql);
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: index.php?search='.$search);
        }
        else if($_POST['call']=="29")
        {
            $iid = $_POST['iid'];
            array_splice($_POST, 0, count($_POST));
            header('Location: item_editor.php?uid='.$_SESSION['uid'].'&iid='.$iid);
        }
        else if($_POST['call']=="30")
        {
            $iid = $_POST['iid'];
            $sql = "UPDATE sakura.item SET item_state = 3 WHERE item_id = ".$iid;
            execute_sql($conn, $sql);
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: index.php?search='.$search);
        }
    }
?>
    
<?php 
    include_once 'style.php';
    include 'header.php';
?>  
    
<br/>
<div class="editor">
    <form method="post" action="">
    <input type="text" class="button" name="search" placeholder="商品类型"/>
    <input type="submit" class="button" value="搜索"/>
    <input type="hidden" name="call" value="25"/>
    </form>
</div>

<br />
<div>
<?php
$item_num = query_num($conn,'sakura.item',"item_state = 1 and item_type like '%".$search."%'");
$page_num = ceil($item_num/$item_per_page);
if($page_num <= 0) $page_num = 1;

echo '<table border="1" id="posts"><tr>';
echo '<th><b>商品类型</b></th>';
echo '<th><b>商品名称</b></th>';
echo '<th><b>商品描述</b></th>';
echo '<th><b>商品价格￥</b></th>';
echo '<th><b>卖家</b></th>';
echo '<th><b>评价</b></th>';
echo '<th><b>操作</b></th>';
echo '</tr>';

$sql = "SELECT * FROM sakura.item WHERE item_state = 1 and item_type like '%".$search."%' ORDER BY item_type ASC";
$item_val = mysqli_query($conn,$sql);
if(! $item_val) die("查询数据库失败：".mysqli_error($conn));
$row_cnt = 0;
$permission = 0;
if($_SESSION['uid'] != 0)
    $permission = query_one($conn,'user_permission','sakura.user_info','user_id',$_SESSION['uid']);
while($row = mysqli_fetch_array($item_val))
{
    $row_cnt += 1;
    if($row_cnt <= ($page-1)*$item_per_page) continue;
    if($row_cnt > $page*$item_per_page) break;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    echo '<td width="10%"><a href="/index.php?search='.$row[1].'">'.$row[1].'</a></td>';
    echo '<td width="15%">'.$row[2].'</td>';
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
    $shopcar_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="加入购物车"/>'
        .'<input type="hidden" name="call" value="26"/>'
        .'<input type="hidden" name="iid" value="'.$row[0].'"/>'
        .'</form>';
    $buy_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="下单购买"/>'
        .'<input type="hidden" name="call" value="27"/>'
        .'<input type="hidden" name="iid" value="'.$row[0].'"/>'
        .'</form>';
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
    $forbid_form = '<form method="post" action="">'
        .'<input type="submit" class="warn" value="封禁此商品"/>'
        .'<input type="hidden" name="call" value="30"/>'
        .'<input type="hidden" name="iid" value="'.$row[0].'"/>'
        .'</form>';
    if($permission == 1) $operation = $shopcar_form.$buy_form;
    if($permission == 3) $operation = $shopcar_form.$buy_form.$forbid_form;
    if($permission == 2)
    {
        if($_SESSION['uid'] == $row[4]) $operation = $change_form.$delete_form;
        else $operation = $shopcar_form.$buy_form;
    }
    echo '<td width="8%">'.$operation.'</td>';  
}
echo '</table>';
?>
</div>  

<br />
<div>
<?php

if($page==1)
    echo '<a href="/index.php?search='.$search.'&page=1" class="npage_btn" style="margin-left:15px;">第一页</a>';
else
    echo '<a href="/index.php?search='.$search.'&page=1" class="page_btn" style="margin-left:15px;">第一页</a>';

if($page==1)
{
    echo '<a href="/index.php?search='.$search.'&page=1" class="npage_btn">1</a>';
    if($page_num>1)
        echo '<a href="/index.php?search='.$search.'&page=2" class="page_btn">2</a>';
}
else if($page==$page_num)
{
    if($page>1)
        echo '<a href="/index.php?search='.$search.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/index.php?search='.$search.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
}
else
{
    echo '<a href="/index.php?search='.$search.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/index.php?search='.$search.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
    echo '<a href="/index.php?search='.$search.'&page='.($page+1).'" class="page_btn">'.($page+1).'</a>';
}

if($page==$page_num)
    echo '<a href="/index.php?search='.$search.'&page='.$page_num.'" class="npage_btn">最后一页</a>';
else
    echo '<a href="/index.php?search='.$search.'&page='.$page_num.'" class="page_btn">最后一页</a>';
?>
</div>
    
</body>
</html>