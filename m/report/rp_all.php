<?php
/*
// ˵��: ���屨��
// ����: ��ҽս�� 
// ʱ��: 2011-11-23
*/
require "../../core/core.php";

// �������Ķ���:
include "rp.core.php";

$part = $_GET["part"];
$where = '';
$tips = "������";
if ($part == "web") {
	$where = "part_id=2 and ";
	$tips = "����";
} else if ($part == "tel") {
	$where = "part_id=3 and ";
	$tips = "�绰";
}
?>
<html>
<head>
<title>���屨��</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<style>
body {margin-top:10px; }
.head, .head a {font-family:"΢���ź�","Verdana"; }
.item {font-family:"Tahoma"; padding:8px 3px 6px 3px !important; }
.footer_op_left {font-family:"Tahoma"; }
.date_tips {padding:10px 0 10px 5px; font-weight:bold; }
form {display:inline; }
</style>
</head>

<body>

<div style="text-align:center;">
	<b><?php echo $h_name; ?></b>&nbsp;&nbsp;
	<form method="GET">
		<input type="hidden" name="part" value="all">
		<input type="submit" value="������" class="buttonb" <?php if ($part == "" || $part == "all") echo 'style="color:red;font-weight:bold;"'; ?> />
	</form>

	<form method="GET">
		<input type="hidden" name="part" value="web">
		<input type="submit" value="����" class="button" <?php if ($part == "web") echo 'style="color:red;font-weight:bold;"'; ?> />
	</form>

	<form method="GET">
		<input type="hidden" name="part" value="tel">
		<input type="submit" value="�绰" class="button" <?php if ($part == "tel") echo 'style="color:red;font-weight:bold;"'; ?> />
	</form>
</div>

<!-- ����鿴 -->
<!-- ���꣬ȥ�꣬ǰ��ļ�¼ -->
<?php
$y = intval(date("Y"));
$time_arr = array(
	"����" => array(strtotime($y."-01-01 00:00:00"), strtotime($y."-12-31 00:00:00")),
	"ȥ��" => array(strtotime(($y-1)."-01-01 00:00:00"), strtotime(($y-1)."-12-31 00:00:00")),
	"ǰ��" => array(strtotime(($y-2)."-01-01 00:00:00"), strtotime(($y-2)."-12-31 00:00:00")),
);

// ����ͳ������:
$data = array();
foreach ($time_arr as $k => $v) {
	$data[$k]["ԤԼ"] = $db->query("select count(*) as c from $table where $where addtime>=".$v[0]." and addtime<=".$v[1]." ", 1, "c");
	$data[$k]["Ԥ��"] = $db->query("select count(*) as c from $table where $where order_date>=".$v[0]." and order_date<=".$v[1]." ", 1, "c");
	$data[$k]["�ѵ�"] = $db->query("select count(*) as c from $table where $where status=1 and order_date>=".$v[0]." and order_date<=".$v[1]." ", 1, "c");
	$data[$k]["δ��"] = $data[$k]["Ԥ��"] - $data[$k]["�ѵ�"];
}
?>
<div class="date_tips">��������(���3��<?php echo $tips; ?>)��</div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="10%">���</td>
		<td class="head" align="center" width="18%">ԤԼ</td>
		<td class="head" align="center" width="18%">Ԥ��</td>
		<td class="head" align="center" width="18%">�ѵ�</td>
		<td class="head" align="center" width="18%">δ��</td>
		<td class="head" align="center" width="18%">��Ժ����</td>
	</tr>

<?php foreach ($time_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["ԤԼ"]; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["Ԥ��"]; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["�ѵ�"]; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["δ��"]; ?></td>
		<td class="item" align="center"><?php echo @round(100 * $data[$k]["�ѵ�"] / $data[$k]["Ԥ��"], 1)."%"; ?></td>
	</tr>
<?php } ?>
</table>

<br>


<!-- ���·ݲ鿴 -->
<!-- ���x���µļ�¼ -->
<?php

$time_arr = array();
for ($i = 0; $i < 12; $i++) {
	$m = strtotime("-".$i." month");
	$time_arr[date("Y-m", $m)] = array(strtotime(date("Y-m-01 00:00:00", $m)), strtotime(date("Y-m-31 23:59:59", $m)));
}

// ����ͳ������:
$data = array();
foreach ($time_arr as $k => $v) {
	$data[$k]["ԤԼ"] = $db->query("select count(*) as c from $table where $where addtime>=".$v[0]." and addtime<=".$v[1]." ", 1, "c");
	$data[$k]["Ԥ��"] = $db->query("select count(*) as c from $table where $where order_date>=".$v[0]." and order_date<=".$v[1]." ", 1, "c");
	$data[$k]["�ѵ�"] = $db->query("select count(*) as c from $table where $where status=1 and order_date>=".$v[0]." and order_date<=".$v[1]." ", 1, "c");
	$data[$k]["δ��"] = $data[$k]["Ԥ��"] - $data[$k]["�ѵ�"];
}
?>
<div class="date_tips">���·����(���12����<?php echo $tips; ?>)��</div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="10%">�·�</td>
		<td class="head" align="center" width="18%">ԤԼ</td>
		<td class="head" align="center" width="18%">Ԥ��</td>
		<td class="head" align="center" width="18%">�ѵ�</td>
		<td class="head" align="center" width="18%">δ��</td>
		<td class="head" align="center" width="18%">��Ժ����</td>
	</tr>

<?php foreach ($time_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["ԤԼ"]; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["Ԥ��"]; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["�ѵ�"]; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["δ��"]; ?></td>
		<td class="item" align="center"><?php echo @round(100 * $data[$k]["�ѵ�"] / $data[$k]["Ԥ��"], 1)."%"; ?></td>
	</tr>
<?php } ?>
</table>



</body>
</html>