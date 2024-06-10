<?php
/*
// - 功能说明 : main.php
// - 创建作者 : 爱医战队 
// - 创建时间 : 2013-05-13 12:28
*/
require "../core/core.php";
include "../core/function.lunar.php";

// -------------------- 2013-05-01 23:39
if ($_GET["do"] == 'change') {
	$_SESSION[$cfgSessionName]["hospital_id"] = $_GET["hospital_id"];
	$user_hospital_id = $_SESSION[$cfgSessionName]["hospital_id"];
}
$hospital_list = $db->query("select id,name from hospital where id in (".implode(',', $hospital_ids).") order by sort desc,id asc", 'id');
$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');
// --------------------

// 时间界限定义:
$today_tb = mktime(0,0,0);
$today_te = $today_tb + 24*3600;
$yesterday_tb = $today_tb - 24*3600;
$month_tb = mktime(0,0,0,date("m"),1);
$month_te = strtotime("+1 month", $month_tb);
$lastmonth_tb = strtotime("-1 month", $month_tb);

// 同比日期定义(2010-11-27):
$tb_tb = strtotime("-1 month", $month_tb);
$tb_te = strtotime("-1 month", time());

// 月比:
$yuebi_tb = strtotime("-1 month", $today_tb);
if (date("d", $yuebi_tb) != date("d", $today_tb)) {
	$yuebi_tb = $yuebi_te = -1;
} else {
	$yuebi_te = $yuebi_tb + 24*3600;
}

// 周比:
$zhoubi_tb = strtotime("-7 day", $today_tb);
$zhoubi_te = $zhoubi_tb + 24*3600;

// 同比:
$tb_tb = strtotime("-1 month", $month_tb); //同比时间开始
$tb_te = strtotime("-1 month", time()); //同比时间结束




// 带有缓存的查询结果:
function wee($tb, $te, $time_type='order_date', $condition='', $condition2='' ) {
	global $table, $db;
	$time_type = $time_type == "addtime" ? "addtime" : "order_date";
	$where = array();
	if ($tb > 0) $where[] = $time_type.">=".intval($tb);
	if ($te > 0) $where[] = $time_type."<".intval($te);
	if ($condition) $where[] = $condition;
	if ($condition2) $where[] = $condition2;
	$sqlwhere = implode(" and ", $where);
	$sql = "select count(*) as c from $table where $sqlwhere limit 1";
	$sql_md5 = md5($sql);

	// 缓存结果:
	$timeout = 60; //缓存超时时间
	$sql_result = -1;
	$cache_file = "cache/".$table;
	if (file_exists($cache_file)) {
		$tm = @explode("\n", str_replace("\r", "", file_get_contents($cache_file)));
		foreach ($tm as $tml) {
			list($a, $b, $c) = explode("|", trim($tml));
			if ($a == $sql_md5) {
				if (time() - $b < $timeout) {
					$sql_result = $c;
					break;
				}
			}
		}
	}

	if ($sql_result != -1) {
		return $sql_result;
	} else {
		$sql_result = $db->query($sql, 1, "c");

		// 更新缓存文件:
		$tm = array();
		$find = 0;
		$time = time();
		if (file_exists($cache_file)) {
			$tm = @explode("\n", str_replace("\r", "", file_get_contents($cache_file)));
			foreach ($tm as $k => $tml) {
				list($a, $b, $c) = explode("|", trim($tml));
				if ($a == $sql_md5) {
					$tm[$k] = $sql_md5."|".$time."|".intval($sql_result);
					$find = 1;
				} else {
					if ($time - $b > $timeout) {
						unset($tm[$k]); //删去过时的
					}
				}
			}
		}
		if ($find == 0) {
			$tm[] = $sql_md5."|".$time."|".intval($sql_result);
		}
		@file_put_contents($cache_file, implode("\r\n", $tm));
		// 更新结束:

		return $sql_result;
	}
}
?>
<html>
<head>
<title>后台首页</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../res/base.css" rel="stylesheet" type="text/css">
<script src="../res/base.js" language="javascript"></script>
<script language="javascript">
function hgo(dir) {
	var obj = byid("hospital_id");
	if (dir == "up") {
		if (obj.selectedIndex > 1) {
			obj.selectedIndex = obj.selectedIndex - 1;
			obj.onchange();
		} else {
			parent.msg_box("已经是最上一家医院了", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
		} else {
			parent.msg_box("已经是最下一家医院了", 3);
		}
	}
}
</script>
<style type="text/css">
<!--
.STYLE1 {
	color: #FF0000;
	font-weight: bold;
}
.STYLE2 {
	color:#009900;
	font-weight: bold;
}

-->
</style>
</head>

<body>

<div style='padding:20px 12px 12px 40px;'>
	<div style="line-height:24px">
<?php
$str = '您好，<font color="#FF0000"><b>'.$realname.'</b></font>';
if ($uinfo["hospitals"] || $uinfo["part_id"] > 0) {
	if ($uinfo["part_id"] > 0) {
		$str .= '　(身份：'.$part_id_name[$uinfo["part_id"]].")";
	}
}

$onlines = $db->query("select count(*) as count from sys_admin where online=1", 1, "count");
$str .= '　在线人数 <font color="red"><b>'.$onlines.'</b></font> 人';

if ($uinfo["part_id"] == 12) {
	//$str .= '<br><a href="#" onclick="parent.load_box(1,\'src\',\'patient_huifang_list_all.php\')">[查看列表]</a>';
}

echo $str;
?>
	</div>

<?php if (count($hospital_ids) > 1) { ?>
	<div style="margin-top:20px;">
		<b>切换医院：</b>
		<select name="hospital_id" id="hospital_id" class="combo" onChange="location='?do=change&hospital_id='+this.value" style="width:200px;">
			<option value="" style="color:gray">--请选择--</option>
			<?php echo list_option($hospital_list, 'id', 'name', $_SESSION[$cfgSessionName]["hospital_id"]); ?>
		</select>&nbsp;
		<button class="button" onClick="hgo('up');">上</button>&nbsp;
		<button class="button" onClick="hgo('down');">下</button>&nbsp;
<?php if ($user_hospital_id > 0) { ?>
		<button class="buttonb" onClick="self.location='/m/patient/patient.php?time_type=order_date&sort=预约时间&show=today&come=0'" title="查看今日未到需回访病人">回访病人</button>&nbsp;
	<?php if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 3) { ?>
		<button class="buttonb" onClick="self.location='/m/patient/patient.php?list_huifang=1'" title="查看我最近回访过的病人">我的回访</button>&nbsp;
	<?php } ?>
<?php }?>

<?php
if($_SESSION[$cfgSessionName]["username"]=="admin")
{
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="tel_go" type="button" value="群发今日该到院患者短信" onClick="if(confirm(\'您确认此操作，此操作将群发所有今日预约到院患者短信！\')){window.open(\'/gettel/index.php\');}">';
}
?>
	</div>
<?php } else if ($user_hospital_id > 0) { ?>
	<div style="margin-top:20px;">当前医院：<b><?php echo $hospital_list[$user_hospital_id]["name"];?></b></div>
<?php } else { ?>
	<div style="margin-top:20px;">没有为您分配医院，请联系上级管理人员处理。</div>
<?php }?>
</div>


<!-- 选择医院后 -->
<?php if ($user_hospital_id > 0) { ?>

<!-- 预约管理权限 -->
<?php
$table = "patient_".$user_hospital_id;

$where = array();
$where[] = '1';
if (!$debug_mode) {
	$read_parts = get_manage_part(); //所有子部门（连同其自身部门)
	$manage_parts = explode(",", $read_parts);
	if ($uinfo["part_admin"] || $uinfo["part_manage"]) { //部门管理员或数据管理员
		$where[] = "(part_id in (".$read_parts.") or binary author='".$realname."')";
	} else { //普通用户只显示自己的数据
		$where[] = "binary author='".$realname."'";
	}
}

// 电话回访只显示已到病人:
if ($uinfo["part_id"] == 12) {
	//$where[] = "status=1";
}

$sqlwhere = implode(" and ", $where);

$today_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$today_tb and order_date<$today_te and status<>3", 1, "count");
if ($_GET["show"] == "sql") {
	echo $db->sql."<br>";
}
$today_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$today_tb and order_date<$today_te and status=1", 1, "count");
$today_not = $today_all - $today_come;

$yesterday_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$yesterday_tb and order_date<$today_tb and status<>3", 1, "count");
$yesterday_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$yesterday_tb and order_date<$today_tb and status=1", 1, "count");
$yesterday_not = $yesterday_all - $yesterday_come;


$this_month_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$month_tb and order_date<$month_te and status<>3", 1, "count");
$this_month_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$month_tb and order_date<$month_te and status=1", 1, "count");
$this_month_not = $this_month_all - $this_month_come;

$last_month_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$lastmonth_tb and order_date<$month_tb and status<>3", 1, "count");
$last_month_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$lastmonth_tb and order_date<$month_tb and status=1", 1, "count");
$last_month_not = $last_month_all - $last_month_come;

// 同比:
$tb_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$tb_tb and order_date<$tb_te and status<>3", 1, "count");
$tb_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$tb_tb and order_date<$tb_te and status=1", 1, "count");
$tb_not = $zhoubi_all - $zhoubi_come;

?>
<div style="float:left">
<table width="510" class="edit" style="margin-top:10px;margin-left:40px;">
	<tr>
		<td colspan="2" class="head">挂号统计数据</td>
	</tr>
	<tr>
		<td class="left">今日：</td>
		<td class="right"><a href="/m/patient/patient.php?show=today">总共: <b><?=$today_all?></b></a> &nbsp;&nbsp; <a href="/m/patient/patient.php?show=today&come=1">已到: <b><?=$today_come?></b></a> &nbsp;&nbsp; <a href="/m/patient/patient.php?show=today&come=0">未到: <b><?=$today_not?></b></a></td>
	</tr>
	<tr>
		<td class="left">昨日：</td>
		<td class="right"><a href="/m/patient/patient.php?show=yesterday">总共: <b><?=$yesterday_all?></b></a> &nbsp;&nbsp; <a href="/m/patient/patient.php?show=yesterday&come=1">已到: <b><?=$yesterday_come?></b></a> &nbsp;&nbsp; <a href="/m/patient/patient.php?show=yesterday&come=0">未到: <b><?=$yesterday_not?></b></a></td>
	</tr>
	
<?php if ($username == "linhanxing"){ ?>

<?php }else{?>
	<tr>
		<td class="left">本月：</td>
		<td class="right"><a href="/m/patient/patient.php?show=thismonth">总共: <b><?=$this_month_all?></b></a> &nbsp;&nbsp; <a href="/m/patient/patient.php?show=thismonth&come=1">已到: <b><?=$this_month_come?></b></a> &nbsp;&nbsp; <a href="/m/patient/patient.php?show=thismonth&come=0">未到: <b><?=$this_month_not?></b></a></td>
	</tr>
	<tr>
		<td class="left" style="color:silver">同比：</td>
		<td class="right" style="color:silver">总共: <b><?=$tb_all?></b> &nbsp;&nbsp; 已到: <b><?=$tb_come?></b> &nbsp;&nbsp; 未到: <b><?=$tb_not?></b></td>
	</tr>
	<tr>
		<td class="left">上月：</td>
		<td class="right"><a href="/m/patient/patient.php?show=lastmonth">总共: <b><?=$last_month_all?></b></a> &nbsp;&nbsp; <a href="/m/patient/patient.php?show=lastmonth&come=1">已到: <b><?=$last_month_come?></b></a> &nbsp;&nbsp; <a href="/m/patient/patient.php?show=lastmonth&come=0">未到: <b><?=$last_month_not?></b></a></td>
	</tr>
<?php }?>	
	
</table>

	<?php if ($username == "linhanxing"){ ?>

<?php }else{?>

<?php
$today_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$today_tb and order_date<$today_te", 1, "count");
if ($_GET["show"] == "sql") {
	echo $db->sql."<br>";
}
$today_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$today_tb and order_date<$today_te and status=3", 1, "count");
$today_not = $today_all - $today_come;

$yesterday_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$yesterday_tb and order_date<$today_tb", 1, "count");
$yesterday_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$yesterday_tb and order_date<$today_tb and status=3", 1, "count");
$yesterday_not = $yesterday_all - $yesterday_come;

$this_month_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$month_tb and order_date<$month_te", 1, "count");
$this_month_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$month_tb and order_date<$month_te and status=3", 1, "count");
$this_month_not = $this_month_all - $this_month_come;

$last_month_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$lastmonth_tb and order_date<$month_tb", 1, "count");
$last_month_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$lastmonth_tb and order_date<$month_tb and status=3", 1, "count");
$last_month_not = $last_month_all - $last_month_come;

// 同比:
$tb_all = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$tb_tb and order_date<$tb_te", 1, "count");
$tb_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$tb_tb and order_date<$tb_te and status=3", 1, "count");
$tb_not = $zhoubi_all - $zhoubi_come;
?>
<table width="510" class="edit" style="margin-top:10px;margin-left:40px;">
	<tr>
		<td colspan="2" class="head">预约未定数据统计</td>
	</tr>
	<tr>
		<td class="left">今日：</td>
		<td class="right"><a href="/m/patient/patient.php?show=today&come=3">共: <b><?=$today_come?></b></a></td>
	</tr>
	<tr>
		<td class="left">昨日：</td>
		<td class="right"><a href="/m/patient/patient.php?show=yesterday&come=3">共: <b><?=$yesterday_come?></b></a></td>
	</tr>
	


	<tr>
		<td class="left">本月：</td>
		<td class="right"><a href="/m/patient/patient.php?show=thismonth&come=3">共: <b><?=$this_month_come?></b></a></td>
	</tr>
	<tr>
		<td class="left" style="color:silver">同比：</td>
		<td class="right" style="color:silver">共: <b><?=$tb_come?></b></td>
	</tr>
	<tr>
		<td class="left">上月：</td>
		<td class="right"><a href="/m/patient/patient.php?show=lastmonth&come=3">共: <b><?=$last_month_come?></b></a></td>
	</tr>
	
</table>
	<?php }?>

</div>

<?php
   $by_daoyuan_order = $db->query("select count(*) as count,author from $table where status = 1 and order_date>=$month_tb and order_date<$month_te and part_id!=4 group by author order by count desc limit 0,5");
   $by_yuyue_order = $db->query("select count(*) as count,author from $table where status != 3 and addtime>=$month_tb and addtime<$month_te and part_id!=4 group by author order by count desc limit 0,5");
   $sy_daoyuan_order = $db->query("select count(*) as count,author from $table where status = 1 and order_date>=$lastmonth_tb and order_date<$month_tb and part_id!=4 group by author order by count desc limit 0,5");
   $sy_yuyue_order = $db->query("select count(*) as count,author from $table where status != 3 and addtime>=$lastmonth_tb and addtime<$month_tb and part_id!=4 group by author order by count desc limit 0,5");
?>  
<style>
.paihangbang{margin-top:10px;padding-left:10px;float:left;width:380px;}
.paihangbang td{ height:26px;}
.paihangbang span{width:20px;height:20px;display:block;margin-left:10px;}
.paihangbang .crown{background:url("/res/img/crown.png") no-repeat; }
.paihangbang .moon{background:url("/res/img/moon.png") no-repeat; }
.paihangbang .star{background:url("/res/img/star.png") no-repeat; }
.paihangbang .sun{background:url("/res/img/sun.png") no-repeat; }
.paihangbang .nums{font-size:14px;font-weight:bold;padding-right:10px;}
</style>

<?php if ($username == "linhanxing"){ ?>

<?php }else{?>
<?php if($_SESSION[$cfgSessionName]["part_id"]!=5){?>

<div class="paihangbang">
  <div style="float:left">
    <table width="180" class="edit" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="3" class="head">本月到院排行榜</td>
      </tr>
   <?php 
      if(!is_array($by_daoyuan_order)){$by_daoyuan_order = array();}
	  
      foreach($by_daoyuan_order as $key=>$val){  
   ?>
      <tr>
        <td><?php if($key == 0){echo '<span class="crown"></span>';}elseif($key == 1){echo '<span class="sun"></span>';}elseif($key==2){echo '<span class="moon"></span>';}else{echo '<span class="star"></span>';}?></td>
        <td><?php echo $val['author'];?></td>
        <td class="nums"><?php echo $val['count'];?></td>
      </tr>
   <?php
      }
   ?>
    </table>
  </div>
  <div style="float:left; padding-left:10px;" >
    <table width="180" class="edit" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="3" class="head">本月预约排行榜</td>
      </tr>
   <?php 
      if(!is_array($by_yuyue_order)){$by_yuyue_order = array();}

      foreach($by_yuyue_order as $key=>$val){  
   ?>
      <tr>
        <td><?php if($key == 0){echo '<span class="crown"></span>';}elseif($key == 1){echo '<span class="sun"></span>';}elseif($key==2){echo '<span class="moon"></span>';}else{echo '<span class="star"></span>';}?></td>
        <td><?php echo $val['author'];?></td>
        <td class="nums"><?php echo $val['count'];?></td>
      </tr>
   <?php
      }
   ?>
    </table>
  </div>
  <div class="clear"></div>
  <div style="float:left; margin-top:10px;">
    <table width="180" class="edit" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="3" class="head">上月到院排行榜</td>
      </tr>
   <?php 
      if(!is_array($sy_daoyuan_order)){$sy_daoyuan_order = array();}
      foreach($sy_daoyuan_order as $key=>$val){  
   ?>
      <tr>
        <td><?php if($key == 0){echo '<span class="crown"></span>';}elseif($key == 1){echo '<span class="sun"></span>';}elseif($key==2){echo '<span class="moon"></span>';}else{echo '<span class="star"></span>';}?></td>
        <td><?php echo $val['author'];?></td>
        <td class="nums"><?php echo $val['count'];?></td>
      </tr>
   <?php
      }
   ?>
    </table>
  </div>
  <div style="float:left; padding-left:10px; margin-top:10px;" >
    <table width="180" class="edit" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="3" class="head">上月预约排行榜</td>
      </tr>
   <?php 
      if(!is_array($sy_yuyue_order)){$sy_yuyue_order = array();}
      foreach($sy_yuyue_order as $key=>$val){  
   ?>
      <tr>
        <td><?php if($key == 0){echo '<span class="crown"></span>';}elseif($key == 1){echo '<span class="sun"></span>';}elseif($key==2){echo '<span class="moon"></span>';}else{echo '<span class="star"></span>';}?></td>
        <td><?php echo $val['author'];?></td>
        <td class="nums"><?php echo $val['count'];?></td>
      </tr>
   <?php
      }
   ?>
    </table>
  </div>
</div>
<?php }?>
<?php }?>

<div class="clear"></div>
<p style="margin-top:20px;"><strong>渠道来诊</strong></p>
<!-- 管理员汇总统计数据 -->
<!--<?php if ($username == "admin" || $debug_mode || in_array($uinfo["part_id"], array(1,9)) || ($uinfo["part_admin"] && in_array(2,$manage_parts)) ) { ?>
<?php
$table = "patient_".$user_hospital_id;
$web_1 = $db->query("select count(*) as count from $table where part_id=2 and addtime>=$today_tb and addtime<$today_te and status<>3", 1, "count");
$web_2 = $db->query("select count(*) as count from $table where part_id=2 and addtime>=$yesterday_tb and addtime<$today_tb and status<>3", 1, "count");
$web_3 = $db->query("select count(*) as count from $table where part_id=2 and addtime>=$month_tb and addtime<$month_te and status<>3", 1, "count");

$web_4 = $db->query("select count(*) as count from $table where part_id=2 and status=1 and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_5 = $db->query("select count(*) as count from $table where part_id=2 and status=1 and order_date>=$yesterday_tb and order_date<$today_tb  and status<>3", 1, "count");
$web_6 = $db->query("select count(*) as count from $table where part_id=2 and status=1 and order_date>=$month_tb and order_date<$month_te  and status<>3", 1, "count");

$web_7 = $db->query("select count(*) as count from $table where part_id=2 and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_8 = $db->query("select count(*) as count from $table where part_id=2 and order_date>=$yesterday_tb and order_date<$today_tb and status<>3", 1, "count");
$web_9 = $db->query("select count(*) as count from $table where part_id=2 and order_date>=$month_tb and order_date<$month_te and status<>3", 1, "count");

// 同比
$web_tb1 = $db->query("select count(*) as count from $table where part_id=2 and addtime>=$tb_tb and addtime<$tb_te and status<>3", 1, "count");
$web_tb2 = $db->query("select count(*) as count from $table where part_id=2 and order_date>=$tb_tb and order_date<$tb_te and status<>3", 1, "count");
$web_tb3 = $db->query("select count(*) as count from $table where part_id=2 and order_date>=$tb_tb and order_date<$tb_te and status=1", 1, "count");

?>
<div style="float:left; width:250px; padding-left:40px;">
<table width="275" class="edit" style="margin-top:10px;">
	<tr>
		<td colspan="2" class="head">网络部</td>
	</tr>
	<tr>
		<td class="left" style="width:20%">今日：</td>
		<td class="right">
			<span title="今日客服预约人数">约:<a href="/m/patient/patient.php?show=today&time_type=addtime&part_id=2">&nbsp;<b><?php echo $web_1; ?></b>&nbsp;</a></span>
			<span title="今日预计到院人数">预计:<a href="/m/patient/patient.php?show=today&part_id=2">&nbsp;<b><?php echo $web_7; ?></b>&nbsp;</a></span>
			<span title="今日已经到院人数">到:<a href="/m/patient/patient.php?show=today&part_id=2&come=1">&nbsp;<b><?php echo $web_4; ?></b>&nbsp;</a></span>
		</td>
	</tr>
	<tr>
		<td class="left">昨日：</td>
		<td class="right">
			<span title="昨日客服预约人数">约:<a href="/m/patient/patient.php?show=yesterday&time_type=addtime&part_id=2">&nbsp;<b><?php echo $web_2; ?></b>&nbsp;</a></span>
			<span title="昨日预计到院人数">预计:<a href="/m/patient/patient.php?show=yesterday&part_id=2">&nbsp;<b><?php echo $web_8; ?></b>&nbsp;</a></span>
			<span title="昨日已经到院人数">到:<a href="/m/patient/patient.php?show=yesterday&part_id=2&come=1">&nbsp;<b><?php echo $web_5; ?></b>&nbsp;</a></span>
		</td>
	</tr>
	<tr>
		<td class="left">本月：</td>
		<td class="right">
			<span title="本月客服预约人数">约:<a href="/m/patient/patient.php?show=thismonth&time_type=addtime&part_id=2">&nbsp;<b><?php echo $web_3; ?></b>&nbsp;</a></span>
			<span title="本月预计到院人数">预计:<a href="/m/patient/patient.php?show=thismonth&part_id=2">&nbsp;<b><?php echo $web_9; ?></b>&nbsp;</a></span>
			<span title="本月已经到院人数">到:<a href="/m/patient/patient.php?show=thismonth&part_id=2&come=1">&nbsp;<b><?php echo $web_6; ?></b>&nbsp;</a></span>
		</td>
	</tr>
	<tr>
		<td class="left" style="color:silver">同比：</td>
		<td class="right" style="color:silver">
			约:<b>&nbsp;<?=$web_tb1?>&nbsp;</b>
			预计:<b>&nbsp;<?=$web_tb2?>&nbsp;</b>
			到:<b>&nbsp;<?=$web_tb3?>&nbsp;</b>
		</td>
	</tr>
</table>
</div>
<?php } ?>
-->
<?php
$ar=$db->query("select * from media where hospital_id='$user_hospital_id' order by id asc","id","name");
foreach($ar as $key=>$value)
{
?>
<!-- 管理员汇总统计数据 -->
<?php if ($username == "admin" ||$username == "linhanxing" || $debug_mode || in_array($uinfo["part_id"], array(1,9)) || ($uinfo["part_admin"] && in_array(2,$manage_parts)) ) { ?>
<?php
$table = "patient_".$user_hospital_id;
$web_1 = $db->query("select count(*) as count from $table where media_from='{$value}' and addtime>=$today_tb and addtime<$today_te and status<>3", 1, "count");
$web_2 = $db->query("select count(*) as count from $table where media_from='{$value}' and addtime>=$yesterday_tb and addtime<$today_tb and status<>3", 1, "count");
$web_3 = $db->query("select count(*) as count from $table where media_from='{$value}' and addtime>=$month_tb and addtime<$month_te and status<>3", 1, "count");

$web_4 = $db->query("select count(*) as count from $table where media_from='{$value}' and status=1 and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_5 = $db->query("select count(*) as count from $table where media_from='{$value}' and status=1 and order_date>=$yesterday_tb and order_date<$today_tb  and status<>3", 1, "count");
$web_6 = $db->query("select count(*) as count from $table where media_from='{$value}' and status=1 and order_date>=$month_tb and order_date<$month_te  and status<>3", 1, "count");

$web_7 = $db->query("select count(*) as count from $table where media_from='{$value}' and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_8 = $db->query("select count(*) as count from $table where media_from='{$value}' and order_date>=$yesterday_tb and order_date<$today_tb and status<>3", 1, "count");
$web_9 = $db->query("select count(*) as count from $table where media_from='{$value}' and order_date>=$month_tb and order_date<$month_te and status<>3", 1, "count");

// 同比
$web_tb1 = $db->query("select count(*) as count from $table where media_from='{$value}' and addtime>=$tb_tb and addtime<$tb_te and status<>3", 1, "count");
$web_tb2 = $db->query("select count(*) as count from $table where media_from='{$value}' and order_date>=$tb_tb and order_date<$tb_te and status<>3", 1, "count");
$web_tb3 = $db->query("select count(*) as count from $table where media_from='{$value}' and order_date>=$tb_tb and order_date<$tb_te and status=1", 1, "count");

?>
<div style="float:left; width:250px; padding-left:30px;">
<table width="275" class="edit" style="margin-top:10px;">
	<tr>
		<td colspan="2" class="head"><?php echo $value?></td>
	</tr>
	<tr>
		<td class="left" style="width:20%">今日：</td>
		<td class="right">
			<span title="今日客服预约人数">约:<a href="/m/patient/patient.php?show=today&time_type=addtime&part_id=&media=<?php echo $value?>">&nbsp;<b><?php echo $web_1; ?></b>&nbsp;</a></span>
			<span title="今日预计到院人数">预计:<a href="/m/patient/patient.php?show=today&part_id=&media=<?php echo $value?>">&nbsp;<b><?php echo $web_7; ?></b>&nbsp;</a></span>
			<span title="今日已经到院人数">到:<a href="/m/patient/patient.php?show=today&part_id=&media=<?php echo $value?>&come=1">&nbsp;<b><?php echo $web_4; ?></b>&nbsp;</a></span>
		</td>
	</tr>
	<tr>
		<td class="left">昨日：</td>
		<td class="right">
			<span title="昨日客服预约人数">约:<a href="/m/patient/patient.php?show=yesterday&time_type=addtime&part_id=&media=<?php echo $value?>">&nbsp;<b><?php echo $web_2; ?></b>&nbsp;</a></span>
			<span title="昨日预计到院人数">预计:<a href="/m/patient/patient.php?show=yesterday&part_id=&media=<?php echo $value?>">&nbsp;<b><?php echo $web_8; ?></b>&nbsp;</a></span>
			<span title="昨日已经到院人数">到:<a href="/m/patient/patient.php?show=yesterday&part_id=&media=<?php echo $value?>&come=1">&nbsp;<b><?php echo $web_5; ?></b>&nbsp;</a></span>
		</td>
	</tr>
	
	<?php if ($username == "linhanxing"){ ?>

<?php }else{?>
	<tr>
		<td class="left">本月：</td>
		<td class="right">
			<span title="本月客服预约人数">约:<a href="/m/patient/patient.php?show=thismonth&time_type=addtime&part_id=&media=<?php echo $value?>">&nbsp;<b><?php echo $web_3; ?></b>&nbsp;</a></span>
			<span title="本月预计到院人数">预计:<a href="/m/patient/patient.php?show=thismonth&part_id=&media=<?php echo $value?>">&nbsp;<b><?php echo $web_9; ?></b>&nbsp;</a></span>
			<span title="本月已经到院人数">到:<a href="/m/patient/patient.php?show=thismonth&part_id=&media=<?php echo $value?>&come=1">&nbsp;<b><?php echo $web_6; ?></b>&nbsp;</a></span>
		</td>
	</tr>
	<tr>
		<td class="left" style="color:silver">同比：</td>
		<td class="right" style="color:silver">
			约:<b>&nbsp;<?=$web_tb1?>&nbsp;</b>
			预计:<b>&nbsp;<?=$web_tb2?>&nbsp;</b>
			到:<b>&nbsp;<?=$web_tb3?>&nbsp;</b>
		</td>
	</tr>
	
	<?php }?>
</table>
</div>
<?php 
}
} ?>

<div class="clear"></div>


<p><strong>科室到诊</strong></p>
<?php
$ar=$db->query("select * from depart where hospital_id='$user_hospital_id' order by id ASC","id","name");
foreach($ar as $key=>$value)
{
?>
<!-- 管理员汇总统计数据 -->
<?php if ($username == "admin"  ||$username == "linhanxing" || $debug_mode || in_array($uinfo["part_id"], array(1,9)) || ($uinfo["part_admin"] && in_array(2,$manage_parts)) ) { ?>
<?php
$table = "patient_".$user_hospital_id;
$web_1 = $db->query("select count(*) as count from $table where depart='{$key}' and addtime>=$today_tb and addtime<$today_te and status<>3", 1, "count");
$web_2 = $db->query("select count(*) as count from $table where depart='{$key}' and addtime>=$yesterday_tb and addtime<$today_tb and status<>3", 1, "count");
$web_3 = $db->query("select count(*) as count from $table where depart='{$key}' and addtime>=$month_tb and addtime<$month_te and status<>3", 1, "count");

$web_4 = $db->query("select count(*) as count from $table where depart='{$key}' and status=1 and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_5 = $db->query("select count(*) as count from $table where depart='{$key}' and status=1 and order_date>=$yesterday_tb and order_date<$today_tb  and status<>3", 1, "count");
$web_6 = $db->query("select count(*) as count from $table where depart='{$key}' and status=1 and order_date>=$month_tb and order_date<$month_te  and status<>3", 1, "count");

$web_7 = $db->query("select count(*) as count from $table where depart='{$key}' and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_8 = $db->query("select count(*) as count from $table where depart='{$key}' and order_date>=$yesterday_tb and order_date<$today_tb and status<>3", 1, "count");
$web_9 = $db->query("select count(*) as count from $table where depart='{$key}' and order_date>=$month_tb and order_date<$month_te and status<>3", 1, "count");

?>
<div style="float:left; width:250px; padding-left:30px;">
<table width="275" class="edit" style="margin-top:10px;">
	<tr>
		<td colspan="2" class="head"><?php echo $value?></td>
	</tr>
	<tr>
		<td class="left" style="width:20%">今日：</td>
		<td class="right">
			<span title="今日客服预约人数"  class="STYLE1">约:&nbsp;<?php echo $web_1; ?>&nbsp;</span>
			<span title="今日预计到院人数">预计:&nbsp;<b><?php echo $web_7; ?></b>&nbsp;</span>
			<span title="今日已经到院人数" class="STYLE2">到:&nbsp;<b><?php echo $web_4; ?></b>&nbsp;</span>	
		</td>
	</tr>
	<tr>
		<td class="left">昨日：</td>
		<td class="right">
			<span title="昨日客服预约人数">约:&nbsp;<b><?php echo $web_2; ?></b>&nbsp;</span>
			<span title="昨日预计到院人数">预计:&nbsp;<b><?php echo $web_8; ?></b>&nbsp;</span>
			<span title="昨日已经到院人数">到:&nbsp;<b><?php echo $web_5; ?></b>&nbsp;</span>
		</td>
	</tr>
	<tr>
		<td class="left">本月：</td>
		<td class="right">
			<span title="本月客服预约人数">约:&nbsp;<b><?php echo $web_3; ?></b>&nbsp;</span>
			<span title="本月预计到院人数">预计:&nbsp;<b><?php echo $web_9; ?></b>&nbsp;</span>
			<span title="本月已经到院人数">到:&nbsp;<b><?php echo $web_6; ?></b>&nbsp;</span>
		</td>
	</tr>
</table>
</div>
<?php 
}
} ?>
<div class="clear"></div>
<p><strong>病种到诊</strong></p>
<?php
$ar=$db->query("select * from  disease where hospital_id='$user_hospital_id' order by id asc","id","name");
foreach($ar as $key=>$value)
{
?>
<!-- 管理员汇总统计数据 -->
<?php if ($username == "admin" ||$username == "linhanxing" || $debug_mode || in_array($uinfo["part_id"], array(1,9)) || ($uinfo["part_admin"] && in_array(2,$manage_parts)) ) { ?>
<?php
$table = "patient_".$user_hospital_id;
$web_1 = $db->query("select count(*) as count from $table where disease_id='{$key}' and addtime>=$today_tb and addtime<$today_te and status<>3", 1, "count");
$web_2 = $db->query("select count(*) as count from $table where disease_id='{$key}' and addtime>=$yesterday_tb and addtime<$today_tb and status<>3", 1, "count");
$web_3 = $db->query("select count(*) as count from $table where disease_id='{$key}' and addtime>=$month_tb and addtime<$month_te and status<>3", 1, "count");

$web_4 = $db->query("select count(*) as count from $table where disease_id='{$key}' and status=1 and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_5 = $db->query("select count(*) as count from $table where disease_id='{$key}' and status=1 and order_date>=$yesterday_tb and order_date<$today_tb  and status<>3", 1, "count");
$web_6 = $db->query("select count(*) as count from $table where disease_id='{$key}' and status=1 and order_date>=$month_tb and order_date<$month_te  and status<>3", 1, "count");

$web_7 = $db->query("select count(*) as count from $table where disease_id='{$key}' and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_8 = $db->query("select count(*) as count from $table where disease_id='{$key}' and order_date>=$yesterday_tb and order_date<$today_tb and status<>3", 1, "count");
$web_9 = $db->query("select count(*) as count from $table where disease_id='{$key}' and order_date>=$month_tb and order_date<$month_te and status<>3", 1, "count");

?>
<div style="float:left; width:250px; padding-left:30px;">
<table width="275" class="edit" style="margin-top:10px;">
	<tr>
		<td colspan="2" class="head"><?php echo $value?></td>
	</tr>
	<tr>
		<td class="left" style="width:20%">今日：</td>
		<td class="right">
			<span title="今日客服预约人数"  class="STYLE1">约:&nbsp;<?php echo $web_1; ?>&nbsp;</span>
			<span title="今日预计到院人数">预计:&nbsp;<b><?php echo $web_7; ?></b>&nbsp;</span>
			<span title="今日已经到院人数" class="STYLE2">到:&nbsp;<b><?php echo $web_4; ?></b>&nbsp;</span>	
		</td>
	</tr>
	<tr>
		<td class="left">昨日：</td>
		<td class="right">
			<span title="昨日客服预约人数">约:&nbsp;<b><?php echo $web_2; ?></b>&nbsp;</span>
			<span title="昨日预计到院人数">预计:&nbsp;<b><?php echo $web_8; ?></b>&nbsp;</span>
			<span title="昨日已经到院人数">到:&nbsp;<b><?php echo $web_5; ?></b>&nbsp;</span>
		</td>
	</tr>
	<tr>
		<td class="left">本月：</td>
		<td class="right">
			<span title="本月客服预约人数">约:&nbsp;<b><?php echo $web_3; ?></b>&nbsp;</span>
			<span title="本月预计到院人数">预计:&nbsp;<b><?php echo $web_9; ?></b>&nbsp;</span>
			<span title="本月已经到院人数">到:&nbsp;<b><?php echo $web_6; ?></b>&nbsp;</span>
		</td>
	</tr>
</table>
</div>
<?php 
}
} ?>
<div class="clear"></div>
<p><strong>咨询员到诊</strong></p>
<?php
$ar=$db->query("select * from sys_admin where hospitals='$user_hospital_id' and character_id=17 order by id desc","id","realname");
foreach($ar as $key=>$value)
{
?>
<!-- 管理员汇总统计数据 -->
<?php if ($username == "admin" || $debug_mode || in_array($uinfo["part_id"], array(1,9)) || ($uinfo["part_admin"] && in_array(2,$manage_parts)) ) { ?>
<?php
$table = "patient_".$user_hospital_id;
$web_1 = $db->query("select count(*) as count from $table where author='{$value}' and addtime>=$today_tb and addtime<$today_te and status<>3", 1, "count");
$web_2 = $db->query("select count(*) as count from $table where author='{$value}' and addtime>=$yesterday_tb and addtime<$today_tb and status<>3", 1, "count");
$web_3 = $db->query("select count(*) as count from $table where author='{$value}' and addtime>=$month_tb and addtime<$month_te and status<>3", 1, "count");

$web_4 = $db->query("select count(*) as count from $table where author='{$value}' and status=1 and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_5 = $db->query("select count(*) as count from $table where author='{$value}' and status=1 and order_date>=$yesterday_tb and order_date<$today_tb  and status<>3", 1, "count");
$web_6 = $db->query("select count(*) as count from $table where author='{$value}' and status=1 and order_date>=$month_tb and order_date<$month_te  and status<>3", 1, "count");

$web_7 = $db->query("select count(*) as count from $table where author='{$value}' and order_date>=$today_tb and order_date<$today_te  and status<>3", 1, "count");
$web_8 = $db->query("select count(*) as count from $table where author='{$value}' and order_date>=$yesterday_tb and order_date<$today_tb and status<>3", 1, "count");
$web_9 = $db->query("select count(*) as count from $table where author='{$value}' and order_date>=$month_tb and order_date<$month_te and status<>3", 1, "count");

?>
<div style="float:left; width:250px; padding-left:30px;">
<table width="275" class="edit" style="margin-top:10px;">
	<tr>
		<td colspan="2" class="head"><?php echo $value?></td>
	</tr>
	<tr>
		<td class="left" style="width:20%">今日：</td>
		<td class="right">
			<span title="今日客服预约人数"  class="STYLE1">约:&nbsp;<?php echo $web_1; ?>&nbsp;</span>
			<span title="今日预计到院人数">预计:&nbsp;<b><?php echo $web_7; ?></b>&nbsp;</span>
			<span title="今日已经到院人数" class="STYLE2">到:&nbsp;<b><?php echo $web_4; ?></b>&nbsp;</span>	
		</td>
	</tr>
	<tr>
		<td class="left">昨日：</td>
		<td class="right">
			<span title="昨日客服预约人数">约:&nbsp;<b><?php echo $web_2; ?></b>&nbsp;</span>
			<span title="昨日预计到院人数">预计:&nbsp;<b><?php echo $web_8; ?></b>&nbsp;</span>
			<span title="昨日已经到院人数">到:&nbsp;<b><?php echo $web_5; ?></b>&nbsp;</span>
		</td>
	</tr>
	<tr>
		<td class="left">本月：</td>
		<td class="right">
			<span title="本月客服预约人数">约:&nbsp;<b><?php echo $web_3; ?></b>&nbsp;</span>
			<span title="本月预计到院人数">预计:&nbsp;<b><?php echo $web_9; ?></b>&nbsp;</span>
			<span title="本月已经到院人数">到:&nbsp;<b><?php echo $web_6; ?></b>&nbsp;</span>
		</td>
	</tr>
</table>
</div>
<?php 
}
}
}?>
<div class="clear"></div>
<!-- 注释 -->
<div style="padding-top:40px; padding-left:50px;">
	* <b>同比</b>：上个月的同期数据。比如，今天是3月28日，则同比就是2月1日至2月28日这段时间的数据<br>
</div>

</body>
</html>