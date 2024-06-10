<?php
/*
// - ����˵�� : ����
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2013-05-02 15:47
*/

$p_type = $uinfo["part_id"]; // 0,1,2,3,4

$title = '��������';

$admin_name = $db->query("select realname from sys_admin", "", "realname");
$author_name = $db->query("select distinct author from $table order by binary author", "", "author");
$kefu_23_list = array_intersect($admin_name, $author_name);

$kefu_4_list = $db->query("select name,realname from ".$tabpre."sys_admin where hospitals='$user_hospital_id' and part_id in (4)");
$doctor_list = $db->query("select name from ".$tabpre."doctor where hospital_id='$user_hospital_id'");

$disease_list = $db->query("select id,name from ".$tabpre."disease where hospital_id=$user_hospital_id");
$depart_list = $db->query("select id,name from ".$tabpre."depart where hospital_id=$user_hospital_id");

$media_list = $db->query("select name from media where hospital_id=$user_hospital_id order by id asc", "", "name");
$media_list = array_merge(array("����", "�绰"), $media_list);

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
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<script src="../../res/datejs/picker.js" language="javascript"></script>
<script language="javascript">
function Check() {
	var oForm = document.mainform;
	//if (oForm.name.value == "") {
	//	alert("�����롰�������ơ���"); oForm.name.focus(); return false;
	//}
	return true;
}
function write_dt(da, db) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><span class="tips"><?php echo $title; ?></span></div>
	<div class="headers_oprate"><button onClick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">��ʾ��</div>
	<div class="d_item">������������������ύ��ť��ʼ������ÿ���������ǿ�ѡ�</div>
</div>

<div class="space"></div>

<form name="mainform" action="patient.php" method="GET" onSubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">�ؼ���</td>
	</tr>
	<tr>
		<td class="left">�ؼ��ʣ�</td>
		<td class="right"><input name="searchword" class="input" style="width:150px" value=""> <span class="intro">(��������Դ�����)</span></td>
	</tr>
	<tr>
		<td colspan="2" class="head">ʱ������</td>
	</tr>
	<tr>
		<td class="left">ʱ�����ͣ�</td>
		<td class="right">
			<select name="time_type" class="combo">
				<option value="" style="color:gray">--��ѡ��--</option>
				<option value="order_date">ԤԼʱ��</option>
				<option value="addtime">��������ʱ��</option>
				<!-- <option value="come_date">���˵�Ժʱ��</option> -->
			</select>
			<span class="intro">ѡ��������ʱ�����ͣ�Ĭ��ΪԤԼʱ��</span>
		</td>
	</tr>
	<tr>
		<td class="left">��ʼʱ�䣺</td>
		<td class="right"><input name="btime" id="begin_time" class="input" style="width:150px" value="<?php //echo date("Y-m-d"); ?>"> <img src="../../res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"> <br>���
		<a href="javascript:write_dt('<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d"); ?>')">[����]</a>
		<a href="javascript:write_dt('<?php echo date("Y-m-d", $yesterday_begin); ?>','<?php echo date("Y-m-d", $yesterday_begin); ?>')">[����]</a>
		<a href="javascript:write_dt('<?php echo date("Y-m-d", $this_month_begin); ?>','<?php echo date("Y-m-d", $this_month_end); ?>')">[����]</a>
		<a href="javascript:write_dt('<?php echo date("Y-m-d", $last_month_begin); ?>','<?php echo date("Y-m-d", $last_month_end); ?>')">[����]</a>
		<a href="javascript:write_dt('<?php echo date("Y-m-d", $this_year_begin); ?>','<?php echo date("Y-m-d", $this_year_end); ?>')">[����]</a>
		<a href="javascript:write_dt('<?php echo date("Y-m-d", $near_1_month_begin); ?>','<?php echo date("Y-m-d"); ?>')">[��һ����]</a>
		<a href="javascript:write_dt('<?php echo date("Y-m-d", $near_3_month_begin); ?>','<?php echo date("Y-m-d"); ?>')">[��������]</a>
		<a href="javascript:write_dt('<?php echo date("Y-m-d", $near_1_year_begin); ?>','<?php echo date("Y-m-d"); ?>')">[��һ��]</a>
		<!-- <span class="intro">��ָ����ʼʱ�� (��������Դ�����)</span> --></td>
	</tr>
	<tr>
		<td class="left">��ֹʱ�䣺</td>
		<td class="right"><input name="etime" id="end_time" class="input" style="width:150px" value="<?php //echo date("Y-m-d"); ?>"> <img src="../../res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"> <!-- <span class="intro">��ָ����ֹʱ�� (��������Դ�����)</span> --></td>
	</tr>

	<tr>
		<td colspan="2" class="head">��Ա����</td>
	</tr>

<?php //if ($debug_mode || $uinfo["part_admin"] || in_array($uinfo["part_id"], array(2,4))) { ?>
	<tr>
		<td class="left">�ѿͷ���</td>
		<td class="right">
			<select name="kefu_23_name" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($kefu_23_list, '_value_', '_value_', ''); ?>
			</select>
			<span class="intro">ָ��Ҫ�����Ŀͷ� (��ѡ����Դ�����)</span>
		</td>
	</tr>
<?php //} ?>

<?php if ($debug_mode || $uinfo["part_admin"] || in_array($uinfo["part_id"], array(3,4))) { ?>
	<tr>
		<td class="left">�ѵ�ҽ��</td>
		<td class="right">
			<select name="kefu_4_name" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($kefu_4_list, 'realname', 'realname', ''); ?>
			</select>
			<span class="intro">ָ��Ҫ�����ĵ�ҽ (��ѡ����Դ�����)</span>
		</td>
	</tr>
<?php } ?>

<?php if ($debug_mode || $uinfo["part_admin"]) { ?>
	<tr>
		<td class="left">��ҽ����</td>
		<td class="right">
			<select name="doctor_name" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($doctor_list, 'name', 'name', ''); ?>
			</select>
			<span class="intro">ָ��Ҫ�����ĽӴ�ҽ�� (��ѡ����Դ�����)</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td colspan="2" class="head">����������</td>
	</tr>

	<tr>
		<td class="left">��Լ״̬��</td>
		<td class="right">
			<select name="come" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<option value='0'>δ��</option>
				<option value='1'>�ѵ�</option>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>
	<tr>
		<td class="left">�������ͣ�</td>
		<td class="right">
			<select name="disease" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($disease_list, "id", "name", ''); ?>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>
<?php if ($debug_mode || $username == 'admin' || !in_array($uinfo["part_id"], array(2,3,4))) { ?>
	<tr>
		<td class="left">���ţ�</td>
		<td class="right">
			<select name="part_id" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<option value='2'>����</option>
				<option value='3'>�绰</option>
				<option value='4'>��ҽ</option>
                <option value='10'>����</option>
                <option value='5'>�г�</option>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>
<?php } ?>

<?php if (count($depart_list) > 0) { ?>
	<tr>
		<td class="left">���ң�</td>
		<td class="right">
			<select name="depart" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($depart_list, "id", "name", ''); ?>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td class="left">ý����Դ��</td>
		<td class="right">
			<select name="media" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($media_list, "_value_", "_value_", ''); ?>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>

</table>

<input type="hidden" name="from" value="search">
<input type="hidden" name="sort" value="����ʱ��">
<input type="hidden" name="sorttype" value="desc">
<div class="button_line"><input type="submit" class="submit" value="����"></div>

</form>
</body>
</html>