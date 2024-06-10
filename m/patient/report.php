<?php
/*
// - ����˵�� : ����
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2013-05-25 15:45
*/
require "../../core/core.php";

if ($hid == 0) {
	msg_box("�Բ���û��ѡ��ҽԺ������ִ�иò�����", "back", 1, 5);
}

$table = "patient_".$hid;

// ҽԺ����:
$h_name = $db->query("select name from hospital where id=$hid limit 1", "1", "name");

// ʱ�䶨��:
$today_begin = mktime(0,0,0); //���쿪ʼ
$today_end = $today_begin + 24*3600 - 1; //�������
$yesterday_begin = $today_begin - 24*3600; //���쿪ʼ
$yesterday_end = $today_begin - 1; //�������
$thismonth_begin = mktime(0,0,0,date("m"),1); //���¿�ʼ
$thismonth_end = strtotime("+1 month", $thismonth_begin) - 1; //���¿�ʼ
$lastmonth_begin = strtotime("-1 month", $thismonth_begin); //���¿�ʼ
$lastmonth_end = $thismonth_begin - 1; //���¿�ʼ


$date_array = array(
	"����" => array($today_begin, $today_end),
	"����" => array($yesterday_begin, $yesterday_end),
	"����" => array($thismonth_begin, $thismonth_end),
	"����" => array($lastmonth_begin, $lastmonth_end),
);

$tf = "order_date";

$kefu = array();
// ��������ͷ�:
$kefu[2] = $db->query("select distinct author from $table where part_id=2 and $tf>=$lastmonth_begin and $tf<=$thismonth_end order by author", "", "author");

// ���е绰�ͷ�:
$kefu[3] = $db->query("select distinct author from $table where part_id=3 and $tf>=$lastmonth_begin and $tf<=$thismonth_end order by author", "", "author");

$data = array();
foreach ($kefu as $ptid => $kfs) {
	foreach ($kfs as $kf) {
		foreach ($date_array as $tname => $t) {
			$b = $t[0];
			$e = $t[1];

			// Ԥ���ܵ�Ժ:
			$data[$ptid][$kf][$tname]["all"] = $d1 = $db->query("select count(*) as c from $table where part_id=$ptid and author='$kf' and $tf>=$b and $tf<=$e", 1, "c");
			// �ѵ�:
			$data[$ptid][$kf][$tname]["come"] = $d2 = $db->query("select count(*) as c from $table where part_id=$ptid and author='$kf' and $tf>=$b and $tf<=$e and status=1", 1, "c");
			// δ��:
			$data[$ptid][$kf][$tname]["leave"] = $d1 - $d2;
		}
	}
}

?>
<html>
<head>
<title>���ݱ���</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="res/datejs/picker.js" language="javascript"></script>
<style>
.red {color:red !important; }

.report_tips {padding:20px 0 10px 0; text-align:center; font-size:14px; font-weight:bold;  }

.list {border:2px solid #43A75C !important; }
.head {}
.item {text-align:center; padding:6px 3px 4px 3px !important; }

.hl {border-left:2px solid #ADE0BA !important; }
.hr {border-right:2px solid #ADE0BA !important; }
.ht {border-top:2px solid #ADE0BA !important; }
.hb {border-bottom:2px solid #ADE0BA !important; }
</style>
</head>

<body>
<div class="report_tips"><?php echo $h_name; ?> ���粿 �ͷ�ԤԼ���</div>

<table class="list" width="100%">
	<tr>
		<th class="head hb"></th>
		<th class="head hl hb red" colspan="3">����</th>
		<th class="head hl hb red" colspan="3">����</th>
		<th class="head hl hb red" colspan="3">����</th>
		<th class="head hl hb red" colspan="3">����</th>
	</tr>

	<tr>
		<th class="head hb">�ͷ�</th>

		<th class="head hl hb">�ܹ�</th>
		<th class="head hb">�ѵ�</th>
		<th class="head hb">δ��</th>

		<th class="head hl hb">�ܹ�</th>
		<th class="head hb">�ѵ�</th>
		<th class="head hb">δ��</th>

		<th class="head hl hb">�ܹ�</th>
		<th class="head hb">�ѵ�</th>
		<th class="head hb">δ��</th>

		<th class="head hl hb">�ܹ�</th>
		<th class="head hb">�ѵ�</th>
		<th class="head hb">δ��</th>
	</tr>

<?php foreach ((array) $data[2] as $kf => $arr) { ?>

	<tr onmouseover="mi(this)" onmouseout="mo(this)">
		<td class="item"><?php echo $kf; ?></td>

		<td class="item hl"><?php echo $arr["����"]["all"]; ?></td>
		<td class="item"><?php echo $arr["����"]["come"]; ?></td>
		<td class="item"><?php echo $arr["����"]["leave"]; ?></td>

		<td class="item hl"><?php echo $arr["����"]["all"]; ?></td>
		<td class="item"><?php echo $arr["����"]["come"]; ?></td>
		<td class="item"><?php echo $arr["����"]["leave"]; ?></td>

		<td class="item hl"><?php echo $arr["����"]["all"]; ?></td>
		<td class="item"><?php echo $arr["����"]["come"]; ?></td>
		<td class="item"><?php echo $arr["����"]["leave"]; ?></td>

		<td class="item hl"><?php echo $arr["����"]["all"]; ?></td>
		<td class="item"><?php echo $arr["����"]["come"]; ?></td>
		<td class="item"><?php echo $arr["����"]["leave"]; ?></td>
	</tr>

<?php } ?>

</table>


<!-- �绰�� -->

<div class="report_tips" style="margin-top:20px;"><?php echo $h_name; ?> �绰�� �ͷ�ԤԼ���</div>

<table class="list" width="100%">
	<tr>
		<th class="head hb"></th>
		<th class="head hl hb red" colspan="3">����</th>
		<th class="head hl hb red" colspan="3">����</th>
		<th class="head hl hb red" colspan="3">����</th>
		<th class="head hl hb red" colspan="3">����</th>
	</tr>

	<tr>
		<th class="head hb">�ͷ�</th>

		<th class="head hl hb">�ܹ�</th>
		<th class="head hb">�ѵ�</th>
		<th class="head hb">δ��</th>

		<th class="head hl hb">�ܹ�</th>
		<th class="head hb">�ѵ�</th>
		<th class="head hb">δ��</th>

		<th class="head hl hb">�ܹ�</th>
		<th class="head hb">�ѵ�</th>
		<th class="head hb">δ��</th>

		<th class="head hl hb">�ܹ�</th>
		<th class="head hb">�ѵ�</th>
		<th class="head hb">δ��</th>
	</tr>

<?php foreach ((array) $data[3] as $kf => $arr) { ?>

	<tr onmouseover="mi(this)" onmouseout="mo(this)">
		<td class="item"><?php echo $kf; ?></td>

		<td class="item hl"><?php echo $arr["����"]["all"]; ?></td>
		<td class="item"><?php echo $arr["����"]["come"]; ?></td>
		<td class="item"><?php echo $arr["����"]["leave"]; ?></td>

		<td class="item hl"><?php echo $arr["����"]["all"]; ?></td>
		<td class="item"><?php echo $arr["����"]["come"]; ?></td>
		<td class="item"><?php echo $arr["����"]["leave"]; ?></td>

		<td class="item hl"><?php echo $arr["����"]["all"]; ?></td>
		<td class="item"><?php echo $arr["����"]["come"]; ?></td>
		<td class="item"><?php echo $arr["����"]["leave"]; ?></td>

		<td class="item hl"><?php echo $arr["����"]["all"]; ?></td>
		<td class="item"><?php echo $arr["����"]["come"]; ?></td>
		<td class="item"><?php echo $arr["����"]["leave"]; ?></td>
	</tr>

<?php } ?>

</table>

<br>
<br>
<b>��ע��</b>�������ݣ��ɲ���ԤԼ��Ժʱ�����ͳ�ƣ������ǲ������ϵ�����ʱ�䡣<br>
<br>

</body>
</html>