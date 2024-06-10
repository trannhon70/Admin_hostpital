<?php
/*
// ˵��: ���Ա𱨱�
// ����: ��ҽս�� 
// ʱ��: 2011-11-23
*/
require "../../core/core.php";

// �������Ķ���:
include "rp.core.php";

$tongji_tips = " - �Ա�ͳ�� - ".$type_tips;
?>
<html>
<head>
<title>�Ա𱨱�</title>
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
</style>
</head>

<body>

<?php include_once "rp.condition_form.php"; ?>

<?php if ($_GET["op"] == "report") { ?>
<?php

if (in_array($type, array(1,2,3,4))) {
	// ����ͳ������:
	$data = array();
	foreach ($final_dt_arr as $k => $v) {
		$data[$k]["��"] = $db->query("select count(*) as c from $table where $where {$timetype}>=".$v[0]." and {$timetype}<=".$v[1]." ", 1, "c");
		$data[$k]["��"] = $db->query("select count(*) as c from $table where $where sex='��' and {$timetype}>=".$v[0]." and {$timetype}<=".$v[1]." ", 1, "c");
		$data[$k]["Ů"] = $db->query("select count(*) as c from $table where $where sex='Ů' and {$timetype}>=".$v[0]." and {$timetype}<=".$v[1]." ", 1, "c");
		$data[$k]["δ֪"] = $data[$k]["��"] - $data[$k]["��"] - $data[$k]["Ů"];
	}
} else if ($type == 5) {
	$arr_all = $db->query("select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where $where {$timetype}>=".$tb." and {$timetype}<=".$te." group by from_unixtime({$timetype},'%k')", "sd", "c");
	$arr_man = $db->query("select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where sex='��' and $where {$timetype}>=".$tb." and {$timetype}<=".$te." group by from_unixtime({$timetype},'%k')", "sd", "c");
	$arr_woman = $db->query("select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where sex='Ů' and $where {$timetype}>=".$tb." and {$timetype}<=".$te." group by from_unixtime({$timetype},'%k')", "sd", "c");

	$data = array();
	foreach ($final_dt_arr as $k => $v) {
		$data[$k]["��"] = intval($arr_all[$v]);
		$data[$k]["��"] = intval($arr_man[$v]);
		$data[$k]["Ů"] = intval($arr_woman[$v]);
		$data[$k]["δ֪"] = $data[$k]["��"] - $data[$k]["��"] - $data[$k]["Ů"];
	}
}

?>
<div class="date_tips"><?php echo $h_name.$tongji_tips; ?></div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="10%">ʱ��</td>
		<td class="head" align="center" width="18%">������</td>
		<td class="head" align="center" width="18%">��</td>
		<td class="head" align="center" width="18%">Ů</td>
		<td class="head" align="center" width="18%">δ֪</td>
		<td class="head" align="center" width="18%">��Ů����</td>
	</tr>

<?php foreach ($final_dt_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["��"]; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["��"]; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["Ů"]; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["δ֪"]; ?></td>
		<td class="item" align="center">1:<?php echo $data[$k]["��"] == 0 ? "��" : @round($data[$k]["Ů"] / $data[$k]["��"], 2); ?></td>
	</tr>
<?php } ?>
</table>

<br>
<?php } ?>


</body>
</html>