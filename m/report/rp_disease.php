<?php
/*
// ˵��: ����
// ����: ��ҽս�� 
// ʱ��: 2011-11-24
*/
require "../../core/core.php";

// �������Ķ���:
include "rp.core.php";

$tongji_tips = " - ��������ͳ�� - ".$type_tips;
?>
<html>
<head>
<title>��������</title>
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
.item {border-left:1px solid #eeeeee !important; border-right:1px solid #eeeeee !important; }
.red {color:red !important;  }
</style>
</head>

<body>

<?php include_once "rp.condition_form.php"; ?>

<?php if ($_GET["op"] == "report") { ?>
<?php

// ����:
$disease_arr = $db->query("select id,name from disease where hospital_id=$hid and isshow=1 order by id asc", "id", "name");
if (count($disease_arr) == 0) {
	exit_html("<center>��δ���弲�����ͣ������޷����б���������</center>");
}

// ��������̫�ࣺɾ��������С�ļ���:
$max_disease_num = 15;
if (count($disease_arr) > $max_disease_num) {
	$new_disease_arr = $db->query("select disease_id,count(disease_id) as c from $table where $where disease_id>0  and {$timetype}>=$max_tb and {$timetype}<=$max_te group by disease_id order by c desc", "disease_id", "c");

	$disease_arr2 = array();
	foreach ($new_disease_arr as $k => $v) {
		$disease_arr2[$k] = $disease_arr[$k];
		if (count($disease_arr2) >= $max_disease_num) {
			break;
		}
	}
	$disease_arr = $disease_arr2;
	$tips = " (Ϊ�򻯱���ֻͳ��Ƶ����ߵ�{$max_disease_num}������)";
}


if (in_array($type, array(1,2,3,4))) {
	// ����ͳ������:
	$data = array();
	foreach ($final_dt_arr as $k => $v) {
		$data[$k]["��"] = $db->query("select count(*) as c from $table where $where {$timetype}>=".$v[0]." and {$timetype}<=".$v[1]." ", 1, "c");

		foreach ($disease_arr as $did => $dname) {
			$data[$k][$did] = $db->query("select count(*) as c from $table where $where disease_id=$did and {$timetype}>=".$v[0]." and {$timetype}<=".$v[1]." ", 1, "c");
		}
	}
} else if ($type == 5) {
	$arr = array();
	$arr["��"] = $db->query("select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where $where {$timetype}>=".$tb." and {$timetype}<=".$te." group by from_unixtime({$timetype},'%k')", "sd", "c");

	foreach ($disease_arr as $did => $dname) {
		$arr[$did] = $db->query("select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where disease_id=$did and $where {$timetype}>=".$tb." and {$timetype}<=".$te." group by from_unixtime({$timetype},'%k')", "sd", "c");
	}

	$data = array();
	foreach ($final_dt_arr as $k => $v) {
		$data[$k]["��"] = intval($arr["��"][$v]);
		foreach ($disease_arr as $did => $dname) {
			$data[$k][$did] = intval($arr[$did][$v]);
		}
	}
}


?>
<div class="date_tips"><?php echo $h_name.$tongji_tips.$tips; ?></div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center">ʱ��</td>
		<td class="head red" align="center">�ܼ�</td>
<?php foreach ($disease_arr as $did => $dname) { ?>
		<td class="head" align="center"><?php echo $dname; ?></td>
<?php } ?>
	</tr>

<?php foreach ($final_dt_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["��"]; ?></td>
<?php   foreach ($disease_arr as $did => $dname) { ?>
		<td class="item" align="center"><?php echo $data[$k][$did]; ?></td>
<?php   } ?>
	</tr>
<?php } ?>
</table>

<br>
<?php } ?>


</body>
</html>