<?php
/*
// ˵��: ����
// ����: ��ҽս�� 
// ʱ��: 2011-11-24
*/
require "../../core/core.php";

// �������Ķ���:
include "rp.core.php";

$tongji_tips = " - ��Ժ״̬ͳ�� - ".$type_tips;
?>
<html>
<head>
<title>״̬����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<script src="../../res/datejs/picker.js" language="javascript"></script>
<style>
body {margin-top:6px; }
#rp_condition_form {text-align:center; }
.head, .head a {font-family:"΢���ź�","Verdana"; }
.item {font-family:"Tahoma"; padding:8px 3px 6px 3px !important; }
.footer_op_left {font-family:"Tahoma"; }
.date_tips {padding:15px 0 15px 0px; font-weight:bold; text-align:center; font-size:15px; font-family:"΢���ź�","Verdana"; }
form {display:inline; }
.red {color:red !important;  }
</style>
</head>

<body>

<?php include_once "rp.condition_form.php"; ?>

<?php if ($_GET["op"] == "report") { ?>
<?php

// $status_array �� config.php �ж��壬Ϊϵͳ�ֵ�

if (in_array($type, array(1,2,3,4))) {
	// ����ͳ������:
	$data = array();
	foreach ($final_dt_arr as $k => $v) {
		$data[$k]["��"] = $db->query("select count(*) as c from $table where $where {$timetype}>=".$v[0]." and {$timetype}<=".$v[1]." ", 1, "c");

		foreach ($status_array as $sid => $sname) {
			$data[$k][$sid] = $db->query("select count(*) as c from $table where $where status='{$sid}' and {$timetype}>=".$v[0]." and {$timetype}<=".$v[1]." ", 1, "c");
		}
	}
} else if ($type == 5) {
	$arr = array();
	$arr["��"] = $db->query("select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where $where {$timetype}>=".$tb." and {$timetype}<=".$te." group by from_unixtime({$timetype},'%k')", "sd", "c");

	foreach ($status_array as $sid => $sname) {
		$arr[$sid] = $db->query("select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where status='{$sid}' and $where {$timetype}>=".$tb." and {$timetype}<=".$te." group by from_unixtime({$timetype},'%k')", "sd", "c");
	}

	$data = array();
	foreach ($final_dt_arr as $k => $v) {
		$data[$k]["��"] = intval($arr["��"][$v]);
		foreach ($status_array as $sid => $sname) {
			$data[$k][$sid] = intval($arr[$sid][$v]);
		}
	}
}


?>
<div class="date_tips"><?php echo $h_name.$tongji_tips.$tips; ?></div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="100">ʱ��</td>
		<td class="head red" align="center">�ܼ�</td>
<?php foreach ($status_array as $sid => $sname) { ?>
		<td class="head" align="center"><?php echo $sname; ?></td>
<?php } ?>
		<td class="head" align="center">��Ժ��</td>
	</tr>

<?php foreach ($final_dt_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["��"]; ?></td>
<?php   foreach ($status_array as $sid => $sname) { ?>
		<td class="item" align="center"><?php echo $data[$k][$sid]; ?></td>
<?php   } ?>
		<td class="item" align="center"><?php echo @round(100 * $data[$k][1] / $data[$k]["��"], 1)."%"; ?></td>
	</tr>
<?php } ?>
</table>

<br>
<?php } ?>


</body>
</html>