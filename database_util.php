<?php
date_default_timezone_set('Asia/Shanghai');
$conn = connect_db('localhost', 'web_user', '');

function connect_db($host,$user_name,$password)
{
    $conn = mysqli_connect($host, $user_name, $password);
    if(!$conn) die("数据库服务器发生错误：".mysqli_connect_error());
    mysqli_set_charset($conn,'utf8');
    return $conn;
}

function create_db($conn, $name)
{
    $sql = "CREATE DATABASE ".$name;
    if(! mysqli_query($conn,$sql))
        die("创建数据库失败：".mysqli_error($conn));
    echo "数据库".$name."创建成功";
}

function delete_db($conn, $name)
{
    $forbid = array("mysql","sys","information_schema","performance_schema"); // 禁止删除的数据库
    foreach($forbid as $f) if(strcasecmp($name,$f)==0) die("禁止删除此数据库！");
    $sql = "DROP DATABASE ".$name;
    if(! mysqli_query($conn,$sql))
        die("删除数据库失败：".mysqli_error($conn));
    echo "数据库".$name."已删除";
}

function select_db($conn, $name)
{
    if(! mysqli_select_db($conn,$name))
        die("进入数据库失败：".mysqli_error($conn));
    echo "当前数据库：".$name;
    $_SESSION['use_db'] = $name;
}

function echo_table($val)
{
    echo '<br />';  
    $first_in = 1;
    $n = 0;
    while($row = mysqli_fetch_array($val))
    {
        if($first_in)
        {
            $keys = array_keys($row);
            $n = count($keys);      
            if($n == 0) break;
            echo '<table border="1"><tr>';
            for($i=1;$i<$n;$i+=2) echo '<td><b>'.$keys[$i].'</b></td>';
            echo '</tr>';
            $first_in = 0;
        }
        echo '<tr>';
        for($i=1;$i<$n;$i+=2) echo '<td>'.$row[$keys[$i]].'</td>';
        echo '</tr>';      
    }
    if($n == 0) echo "No content.";
    else echo '</table>';
}

function show_dbs($conn)
{
    $sql = "SHOW DATABASES";
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询数据库失败：".mysqli_error($conn));
    echo_table($retval);
}

function show_tbs($conn)
{
    select_db($conn, $_SESSION['use_db']);
    $sql = "SHOW TABLES";
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询数据表失败：".mysqli_error($conn));
    echo_table($retval);
}

function show_tb($conn,$name)
{
    select_db($conn, $_SESSION['use_db']);
    $sql = "SELECT * FROM ".$name;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询失败：".mysqli_error($conn));
    echo "→".$name;
    echo_table($retval);
}

function show_tb_attr($conn,$name)
{
    select_db($conn, $_SESSION['use_db']);
    $sql = "SHOW COLUMNS FROM ".$name;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询失败：".mysqli_error($conn));
    echo "→".$name;
    echo_table($retval);
}

function execute_sql_debug($conn,$sql)
{
    select_db($conn, $_SESSION['use_db']);
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />语句执行错误：".mysqli_error($conn));  
    if(strcasecmp(substr($sql,0,6),"SELECT")==0)
    {
        $begin = stripos($sql,"FROM ") + 5;
        $end = stripos($sql," ",$begin);
        if(!$end) $end = strlen($sql);
        $name = substr($sql,$begin,$end-$begin);
        echo "→".$name;
        echo_table($retval);
    }
    echo "<br />执行语句成功：".$sql;
}

function query_one($conn,$select,$from,$where_key,$where_value)
{   //调用此函数须确保查询的结果唯一
    $sql = 'select '.$select.' from '.$from.' where '.$where_key.'='.$where_value;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />查询失败：".mysqli_error($conn));
    $row = mysqli_fetch_array($retval);
    if(!$row) return NULL;
    else return $row[0];
}

function find($conn,$select,$from,$where_key,$where_value,$value)
{   //查找是否存在符合条件的值
    $sql = 'select '.$select.' from '.$from.' where '.$where_key.'='.$where_value;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />查询失败：".mysqli_error($conn));
    while($row = mysqli_fetch_array($retval))
    {
        if($value == $row[0]) return True;
    }
    return False;
}

function execute_sql($conn, $sql)
{
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />语句执行错误：".mysqli_error($conn));
    #echo "<br />执行语句成功：".$sql;
    return $retval;
}

function build_web_database($conn)
{
    // TODO: 主码，外码，check，自增等等
    
    // 用户表：uid，账号，密码，邮箱，昵称，头像地址，权限(1买2卖3管理员)，生日，性别，手机号，
    //        创建时间，用户状态（1正常，2封禁，3审核）
    $table = 'user_info(
            user_id int auto_increment,
            user_name varchar(32) unique,
            user_pwd blob(128),
            user_email varchar(32) unique,
            user_nickname varchar(32),
            user_headpic_url varchar(256),                
            user_permission int,
            user_birthday varchar(32),
            user_sex varchar(4),
            user_phone varchar(32) unique,
            user_createtime bigint,
            user_state int,
            primary key (user_id)) ENGINE=InnoDB;';
    execute_sql($conn, 'CREATE table if not exists sakura.'.$table);
    $sql = 'ALTER table sakura.user_info CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
    $sql = "INSERT into sakura.user_info (user_name,user_pwd,user_email,user_nickname,user_permission,user_createtime,user_state) 
            values ('boss', PASSWORD('boss'), 'boss@x.com', '博士', 3,752472045,1),
                    ('bokoblin', PASSWORD('bokoblin'), 'bokoblin@zelda.com', '猪', 1,752472045,1),
                    ('moblin', PASSWORD('moblin'), 'moblin@zelda.com', '莫布林', 1,752472045,1),
                    ('pikachu', PASSWORD('pikachu'), 'pikachu@pokemon.com', '电气老鼠', 1,752472045,1),
                    ('akie', PASSWORD('akie'), 'akie@utami.com', 'Akie秋绘', 2,752472045,1),
                    ('momo', PASSWORD('momo'), 'momo@omyoji.com', '桃花花', 2,752472045,1),
                    ('yousa', PASSWORD('yousa'), 'yousa@utami.com', '冷鸟yousa', 2,752472045,1),
                    ('keluxier', PASSWORD('keluxier'), 'klxer@arknights.com', '可露希尔', 2,752472045,1),
                    ('kirlia', PASSWORD('kirlia'), 'kirlia@pokemon.com', 'Lovely', 1,752472045,1),
                    ('yuki', PASSWORD('yuki'), 'yuki@omyoji.com', '冻住不许走', 1,752472045,1);";
    execute_sql($conn, $sql);
    
    // 商品表：iid，类型，名称，描述，卖家uid，价格，状态（1未售，2已售，3违禁）
    $table = 'item(
            item_id int auto_increment,
            item_type varchar(32),
            item_name varchar(32),
            item_description varchar(2048),
            item_seller int,
            item_price float,
            item_createtime bigint,
            item_state int,
            primary key (item_id),
            foreign key (item_seller) references sakura.user_info(user_id) on delete cascade) ENGINE=InnoDB;';
    execute_sql($conn, 'CREATE table if not exists sakura.'.$table);
    $sql = 'ALTER table sakura.item CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
    $sql = "INSERT into sakura.item (item_type,item_name,item_description,item_seller,item_price,item_createtime,item_state) 
            values ('源石','源石x1','开采源石',8,6,852472045,1),
                    ('源石','源石x6','开采一组源石',8,30,852472045,1),
                    ('源石','源石x20','开采一堆源石',8,98,852472045,1),
                    ('源石','源石x40','开采一袋源石',8,198,852472045,1),
                    ('源石','源石x66','开采一盒源石',8,328,852472045,1),
                    ('源石','源石x130','开采一箱源石',8,648,852472045,1),
                    ('组合包','新人组合包','古米，寻访凭证x1',8,6,852472045,1),
                    ('组合包','新人寻访组合包','十连寻访凭证x2',8,128,852472045,1),
                    ('组合包','新人养成组合包','源石x13，龙门币x40000，中级作战记录x20',8,68,852472045,1),
                    ('组合包','新人家具组合包','源石x6，家具零件x2400',8,68,852472045,1),
                    ('组合包','月卡','源石x6，合成玉x6000，应急理智合剂x30',8,30,852472045,1),
                    ('时装','枯柏-守林人','生命之地经典系列',8,108,852472045,1),
                    ('时装','孤攀客-崖心','生命之地经典系列',8,90,852472045,1),
                    ('时装','四边形-蛇屠箱','生命之地经典系列',8,90,852472045,1),
                    ('时装','石墨-陨星','开拓者系列-雷神工业出品',8,108,852472045,1),
                    ('时装','寒冬信使-德克萨斯','寒武纪系列冬季新款',8,90,852472045,1),
                    ('时装','新航线-讯使','寒武纪系列冬季新款',8,90,852472045,1),
                    ('时装','静谧午夜-闪灵','珊瑚海岸系列',8,108,852472045,1);";
    execute_sql($conn, $sql); 
    
    // 订单表：did，商品iid，购买用户uid，时间，买家评价，
    // 状态(1购物车，2已下单，3已发货，4已完成，5要求退货，6已退货，7要求申诉，8申诉完成/作废/终止)
    $table = 'deal(
            deal_id int auto_increment,
            deal_iid int,
            deal_uid int,
            deal_time bigint,
            deal_evaluation varchar(16384),
            deal_state int,
            primary key (deal_id),
            foreign key (deal_iid) references sakura.item(item_id) on delete cascade,
            foreign key (deal_uid) references sakura.user_info(user_id) on delete cascade
            constraint unique_cond UNIQUE (iid,uid) ) ENGINE=InnoDB;';
    execute_sql($conn, 'CREATE table if not exists sakura.'.$table);
    $sql = 'ALTER table sakura.deal CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
//    $sql = "INSERT into sakura.deal () 
//            value ();";
//    execute_sql($conn, $sql); 

    // 私信
    // state 0=正常 1=sender删除 2=receiver删除 3=双方删除
    $table = 'message(
            msg_id int auto_increment,
            msg_sender int,
            msg_receiver int,
            msg_time bigint,
            msg_content varchar(1024),
            msg_state int,
            primary key (msg_id),
            foreign key (msg_sender) references sakura.user_info(user_id) on delete cascade,
            foreign key (msg_receiver) references sakura.user_info(user_id) on delete cascade)';
    execute_sql($conn, 'CREATE table sakura.'.$table);
    $sql = 'ALTER table sakura.message CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
    $sql = "INSERT into sakura.message 
            (msg_sender,msg_receiver,msg_time,msg_content,msg_state) 
            values (1, 2, 100022, 'hello', 0),
            (2, 1, 100031, 'hi. how are you?', 0),
            (1, 2, 100045, 'i\'m fine, and you?', 0),
            (2, 1, 100062, 'i\'m die.', 0),
            (3, 1, 123456, 'awsl', 0);";
    execute_sql($conn, $sql);
}

function build_database_user($conn)
{
    $sql = "CREATE USER 'web_user'@'localhost' ";
    execute_sql($conn, $sql);
    $sql = "GRANT select,insert,delete,update ON  sakura.* TO 'web_user'@'localhost'";
    execute_sql($conn, $sql);
}

function check_usrpsw($conn,$name,$psw)
{   //检查用户账号密码是否正确，返回uid
    $sql = 'SELECT user_id FROM sakura.user_info WHERE user_name="'.$name.'"'
            . 'AND user_pwd=PASSWORD("'.$psw.'")';
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />发生错误：".mysqli_error($conn));
    $row = mysqli_fetch_array($retval);
    if(!$row) return NULL;
    else return $row[0];
}

function init($conn)
{
    execute_sql($conn, 'DROP database if exists sakura');
    execute_sql($conn, 'CREATE database sakura');
    build_web_database($conn);
    $_SESSION['uid'] = 0;
}

function check_board_manager($conn,$bid)
{
    if(!find($conn,'uid','sakura.manage','bid','1',$_SESSION['uid']) && 
        !find($conn,'uid','sakura.manage','bid',$bid,$_SESSION['uid']))
        return FALSE;
    else return TRUE;
}

function query_num($conn,$from,$where)
{   //调用此函数须确保查询的结果唯一
    $sql = 'select count(*) from '.$from.' where '.$where;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />查询失败：".mysqli_error($conn));
    $row = mysqli_fetch_array($retval);
    if(!$row) return NULL;
    else return $row[0];
}

function query_a($conn,$select,$from,$where)
{   //调用此函数须确保查询的结果唯一
    $sql = 'select '.$select.' from '.$from.' where '.$where;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />查询失败：".mysqli_error($conn));
    $row = mysqli_fetch_array($retval);
    if(!$row) return NULL;
    else return $row[0];
}

?>