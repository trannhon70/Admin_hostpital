<?php
/*
// - ����˵�� : �ͷ����� ������
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2011-04-11 16:38
*/
require "../../core/core.php";
set_time_limit(0);
$table = "patient_".$hid;

// �����б�:
$disease_list = $db->query("select id,name from disease where hospital_id='$hid' order by sort desc,sort2 desc, id asc", "id", "name");

$web_kefu_list = $db->query("select id, realname from sys_admin where concat(',',hospitals,',') like '%{$hid}%' and part_id=2 order by binary name asc", "id", "realname");
$tel_kefu_list = $db->query("select id, realname from sys_admin where concat(',',hospitals,',') like '%{$hid}%' and part_id=3 order by binary name asc", "id", "realname");
$kefu_list = array_merge($web_kefu_list, $tel_kefu_list);

$res_type_array = array(1 => "ԤԼ", 2 => "Ԥ�Ƶ�Ժ", 3 => "�ѵ�Ժ");


// Ĭ��ʱ��:
if (!isset($_GET["btime"])) {
	$_GET["btime"] = date("Y-m-01");
	$_GET["etime"] = date("Y-m-d", strtotime("+1 month", strtotime($_GET["btime"])) - 1);
}

// Ĭ������:
if (!isset($_GET["res_type"])) {
	$_GET["res_type"] = 1;
}


$op = $_GET["op"];

// ����ʱ��:
if ($op == "show") {
	$where = array();

	$tb = strtotime($_GET["btime"]);
	$te = strtotime($_GET["etime"]);

	if ($_GET["res_type"] == 1) {
		$where[] = "addtime>=".$tb." and addtime<=".$te;
	} else if ($_GET["res_type"] == 2) {
		$where[] = "order_date>=".$tb." and order_date<=".$te;
	} else {
		$where[] = "order_date>=".$tb." and order_date<=".$te." and status=1";
	}

	if (isset($_GET["disease"])) {
		$disease = implode(",", $_GET["disease"]);
		$where[] = "disease_id in (".$disease.")";
	}

	if (isset($_GET["kefu"])) {
		$run_kefu = $_GET["kefu"];
	} else {
		$run_kefu = $kefu_list;
	}
	foreach ($run_kefu as $k => $v) {
		if (trim($v) == '') unset($run_kefu[$k]);
	}

	$sqlwhere = '';
	if (count($where) > 0) {
		$sqlwhere = "and ".implode(" and ", $where);
	}

	$num1 = $num2 = $num3 = array();
	foreach ($run_kefu as $k => $v) {
		$num1[$v] = $db->query("select disease_id, count(id) as c from $table where author='$v' $sqlwhere group by disease_id order by c desc", "disease_id", "c");
	}

}

$title = '��������';

// ʱ�䶨��
// ����
$yesterday_begin = strtotime("-1 day");
// ����
$this_month_begin = mktime(0,0,0,date("m"), 1);
$this_month_end = strtotime("+1 month", $this_month_begin) - 1;
// �ϸ���
$last_month_end = $this_month_begin - 1;
$last_month_begin = strtotime("-1 month", $this_month_begin);
//����
$this_year_begin = mktime(0,0,0,1,1);
$this_year_end = strtotime("+1 year", $this_year_begin) - 1;
// ���һ����
$near_1_month_begin = strtotime("-1 month");
// ���������
$near_3_month_begin = strtotime("-3 month");
// ���һ��
$near_1_year_begin = strtotime("-12 month");

?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="res/datejs/picker.js" language="javascript"></script>
<style>
#tiaojian {margin:10px 0 0 30px; }
form {display:inline; }

#result {margin-left:50px; }
.h_name {font-weight:bold; margin-top:20px; }
.h_kf {margin-left:20px; }
.kf_li {border-bottom:0px dotted silver; }

s {width: 20px; text-align:center; text-decoration:none; }
.dh td, .dt td, .ds td {border:1px solid #E4E4E4; padding:4px 3px 2px 3px; text-align:center; }
.dh td {font-weight:bold; background:#EFF8F8; }
.ds td {background:#FFF2EC; }
</style>
<script type="text/javascript">
function write_dt(da, db) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
}
function check_data(form) {
	byid("submit_button_1").value = '�ύ��';
	byid("submit_button_1").disabled = true;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><span class="tips"><?php echo $title; ?></span></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<table width="100%" style="background:#FAFCFC;">
	<tr>
		<td style="padding:5px 5px 5px 10px; line-height:180%; border:2px solid #D8EBEB;">
			<b>ʱ��������</b>
			<form method="GET" onsubmit="return check_data(this)">
				<span id="t_day">
					&nbsp; ��ʼʱ�䣺<input name="btime" id="begin_time" class="input" style="width:100px" value="<?php echo $_GET["btime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
					&nbsp; ��ֹʱ�䣺<input name="etime" id="end_time" class="input" style="width:100px" value="<?php echo $_GET["etime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
					&nbsp; ���
					<a href="javascript:write_dt('<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d"); ?>')">����</a>
					<a href="javascript:write_dt('<?php echo date("Y-m-d", $yesterday_begin); ?>','<?php echo date("Y-m-d", $yesterday_begin); ?>')">����</a>
					<a href="javascript:write_dt('<?php echo date("Y-m-d", $this_month_begin); ?>','<?php echo date("Y-m-d", $this_month_end); ?>')">����</a>
					<a href="javascript:write_dt('<?php echo date("Y-m-d", $last_month_begin); ?>','<?php echo date("Y-m-d", $last_month_end); ?>')">����</a>&nbsp; &nbsp;
				</span>

				<!--  -->
				<b>������ͣ�</b>
				<select name="res_type" class="combo">
					<option value="" style="color:gray">-��ѡ��-</option>
					<?php echo list_option($res_type_array, "_key_", "_value_", $_GET["res_type"]); ?>
				</select>&nbsp;


		</td>
		<td width="150" align="center" style="border:2px solid #D8EBEB;">
			<input id="submit_button_1" type="submit" class="button" value="�ύ">
			<input type="hidden" name="op" value="show">
		</td>
	</tr>
</table>
</form>


<?php if ($op == "show") { ?>
<br>
<table width="100%"  style="border:2px solid #DFDFDF; background:#FAFCFC;">
	<tr class="dh">
		<td width="10%">����</td>
<?php foreach ($run_kefu as $v) { ?>
		<td><?php echo $v; ?></td>
<?php } ?>
		<td>�ܼ�</td>
	</tr>

<?php foreach ($disease_list as $k => $v) { ?>
	<tr class="dt">
		<td><?php echo $v; ?></td>
<?php foreach ($run_kefu as $kf) { ?>
		<td><?php echo intval($num1[$kf][$k]); ?></td>
<?php } ?>
		<td>-</td>
	</tr>
<?php } ?>

	<tr class="ds">
		<td>�ܼƣ�</td>
<?php foreach ($run_kefu as $kf) { ?>
		<td>-</td>
<?php } ?>
		<td>-</td>
	</tr>

</table>

<?php } ?>


</body>
</html>