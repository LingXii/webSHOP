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
        if($_POST['call']=="47")
        {
            $sql = "UPDATE sakura.user_info SET user_state = 2 WHERE user_id = ".$_POST['uid'];
            execute_sql($conn, $sql);      
        }
        else if($_POST['call']=="48")
        {
            $sql = "UPDATE sakura.user_info SET user_state = 1 WHERE user_id = ".$_POST['uid'];
            execute_sql($conn, $sql);      
        }
        else if($_POST['call']=="49")
        {
            $sql = "UPDATE sakura.user_info SET user_state = 1, user_permission = 2 WHERE user_id = ".$_POST['uid'];
            execute_sql($conn, $sql);   
        }
        else if($_POST['call']=="50")
        {
            $sql = "UPDATE sakura.user_info SET user_state = 1, user_permission = 2 WHERE user_id = ".$_POST['uid'];
            execute_sql($conn, $sql);   
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: user_manage.php?uid='.$uid);
    }
?>
    
<?php 
    include_once 'style.php';
    include 'header.php';
?>  
    
<br/>
<div>   
    <h1 style="margin:10px">用户管理系统</h1>
</div>

<br />
<div>
<?php
$item_num = query_num($conn,'sakura.user_info',"user_id > 0");
$page_num = ceil($item_num/$item_per_page);
if($page_num <= 0) $page_num = 1;

echo '<table border="1" id="posts"><tr>';
echo '<th><b>用户id</b></th>';
echo '<th><b>用户身份</b></th>';
echo '<th><b>用户状态</b></th>';
echo '<th><b>用户名</b></th>';
echo '<th><b>昵称</b></th>';
echo '<th><b>邮箱</b></th>';
echo '<th><b>手机号</b></th>';
echo '<th><b>创建时间</b></th>';
echo '<th><b>操作</b></th>';
echo '</tr>';

$sql = "SELECT * FROM sakura.user_info WHERE user_id > 0 ORDER BY user_state DESC, user_id DESC";
$item_val = mysqli_query($conn,$sql);
if(! $item_val) die("查询数据库失败：".mysqli_error($conn));
$row_cnt = 0;
while($row = mysqli_fetch_array($item_val))
{
    $row_cnt += 1;
    if($row_cnt <= ($page-1)*$item_per_page) continue;
    if($row_cnt > $page*$item_per_page) break;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    echo '<td width="10%">'.$row[0].'</td>';
    if($row[6] == 1) echo '<td width="10%">买家</td>';
    else if($row[6] == 2) echo '<td width="10%">卖家</td>';
    else if($row[6] == 3) echo '<td width="10%">管理员</td>';
    if($row[11] == 1) echo '<td width="10%">正常</td>';
    else if($row[11] == 2) echo '<td width="10%">封禁</td>';
    else if($row[11] == 3) echo '<td width="10%">卖家审核</td>';
    echo '<td width="15%">'.$row[1].'</td>';
    echo '<td width="15%"><a href="/user_manage.php?uid='.$row[0].'">'.$row[4].'</a></td>';
    echo '<td width="10%">'.$row[3].'</td>';
    echo '<td width="10%">'.$row[9].'</td>';
    $createtime = date('Y-n-j H:i:s',$row[10]);
    echo '<td width="12%">'.$createtime.'</td>';    
    $operation = '';
    $forbid_form = '<form method="post" action="">'
        .'<input type="submit" class="warn" value="封禁此用户"/>'
        .'<input type="hidden" name="call" value="47"/>'
        .'<input type="hidden" name="uid" value="'.$row[0].'"/>'
        .'</form>';
    $unforbid_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="解封此用户"/>'
        .'<input type="hidden" name="call" value="48"/>'
        .'<input type="hidden" name="uid" value="'.$row[0].'"/>'
        .'</form>';
    $accept_form = '<form method="post" action="">'
        .'<input type="submit" class="items" value="审核通过"/>'
        .'<input type="hidden" name="call" value="49"/>'
        .'<input type="hidden" name="uid" value="'.$row[0].'"/>'
        .'</form>';
    $reject_form = '<form method="post" action="">'
        .'<input type="submit" class="warn" value="审核不通过"/>'
        .'<input type="hidden" name="call" value="50"/>'
        .'<input type="hidden" name="uid" value="'.$row[0].'"/>'
        .'</form>';
    if($row[11] == 1) $operation = $forbid_form;
    else if($row[11] == 2) $operation = $unforbid_form;
    else if($row[11] == 3) $operation = $accept_form.$reject_form.$forbid_form;
    echo '<td width="8%">'.$operation.'</td>';  
}
echo '</table>';
?>
</div>  

<br />
<div>
<?php

if($page==1)
    echo '<a href="/user_manage.php?uid='.$uid.'&page=1" class="npage_btn" style="margin-left:15px;">第一页</a>';
else
    echo '<a href="/user_manage.php?uid='.$uid.'&page=1" class="page_btn" style="margin-left:15px;">第一页</a>';

if($page==1)
{
    echo '<a href="/user_manage.php?uid='.$uid.'&page=1" class="npage_btn">1</a>';
    if($page_num>1)
        echo '<a href="/user_manage.php?uid='.$uid.'&page=2" class="page_btn">2</a>';
}
else if($page==$page_num)
{
    if($page>1)
        echo '<a href="/user_manage.php?uid='.$uid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/user_manage.php?uid='.$uid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
}
else
{
    echo '<a href="/user_manage.php?uid='.$uid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/user_manage.php?uid='.$uid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
    echo '<a href="/user_manage.php?uid='.$uid.'&page='.($page+1).'" class="page_btn">'.($page+1).'</a>';
}

if($page==$page_num)
    echo '<a href="/user_manage.php?uid='.$uid.'&page='.$page_num.'" class="npage_btn">最后一页</a>';
else
    echo '<a href="/user_manage.php?uid='.$uid.'&page='.$page_num.'" class="page_btn">最后一页</a>';
?>
</div>
    
</body>
</html>