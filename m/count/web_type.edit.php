<?php
// --------------------------------------------------------
// - ����˵�� : ��Ŀ�������޸�
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2010-10-13 11:40
// --------------------------------------------------------

if ($_POST) {
	if ($id > 0) {
		$old = $db->query("select * from $table where id=$id limit 1", 1);
		$old_uids = explode(",", $old["uids"]);
	}

	$r = array();
	$r["hid"] = $tm_hid = intval($_POST["hid"]);
	if ($tm_hid > 0) {
		$r["h_name"] = $db->query("select name from hospital where id=$tm_hid limit 1", 1, "name");
	}
	$r["type"] = $_POST["type"];
	$r["name"] = $_POST["name"];
	$r["kefu"] = trim(str_replace("��", ",", str_replace("��", ",", $_POST["kefu"])));
	$r["uids"] = implode(",", $_POST["rec_user"]);
	$r["sort"] = intval($_POST["sort"]);

	if ($op == "add") {
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_realname"] = $realname;
	}

	$sqldata = $db->sqljoin($r);
	if ($op == "add") {
		$sql = "insert into $table set $sqldata";
	} else {
		$sql = "update $table set $sqldata where id='$id' limit 1";
	}

	if ($db->query($sql)) {
		msg_box("�����ύ�ɹ�", "?", 1);
	} else {
		msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);
	}
}

if ($op == "edit") {
	$line = $db->query_first("select * from $table where id='$id' limit 1");
}
$title = $op == "edit" ? "ҽԺ��Ŀ - �޸�" : "ҽԺ��Ŀ - ����";
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>

<style>
#rec_part, #rec_user {margin-top:6px; }
.rec_user_b {width:140px; float:left; }
.rec_group_part {clear:both; margin:10px 0 5px 0; font-weight:bold; }
</style>

<script language="javascript">
function Check() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("�����롰���ơ���"); oForm.name.focus(); return false;
	}
	return true;
}
function update_check_color(o) {
	o.parentNode.getElementsByTagName("label")[0].style.color = o.checked ? "blue" : "";
}
</script>

</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><span class="tips"><?php echo $title; ?></span></div>
	<div class="header_center"></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">��ʾ��</div>
	<div class="d_item">1.�������ƣ�����ύ����</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">ҽԺ��Ŀ����</td>
	</tr>
	<tr>
		<td class="left">��Ŀ���ƣ�</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">�硰�Ϻ���������ҽԺ ���ʺš�</span></td>
	</tr>
	<tr>
		<td class="left">����ҽԺ��</td>
		<td class="right">
			<select name="hid" class="combo">
				<option value="0" style="color:gray">-��ѡ������ҽԺ-</option>
<?php
	$h_id_name = $db->query("select id,name from hospital where id in (".implode(",", $hospital_ids).")", "id", "name");
	echo list_option($h_id_name, "_key_", "_value_", $line["hid"]);
?>
			</select>
			<span class="intro">����ҽԺ����ѡ��</span>
		</td>
	</tr>
	<tr>
		<td class="left">�ͷ�������</td>
		<td class="right"><input name="kefu" value="<?php echo $line["kefu"]; ?>" class="input" style="width:500px"> <span class="intro">��������ö��Ÿ���</span></td>
	</tr>
	<tr>
		<td class="left">����Ա��</td>
		<td class="right">
			<!-- �û�ѡ�� -->
			<div id="rec_user">
<?php
$cur_select = $line["uids"] ? explode(",", $line["uids"]) : array();
$part_id_name = $db->query("select id,name from sys_part", "id", "name");
$all_admin = $db->query("select id,realname,part_id from sys_admin where isshow=1 order by part_id,realname", "id");
$last_part = 0;
foreach ($all_admin as $a => $b) {
	if ($b["part_id"] != $last_part) {
		echo '<div class="rec_group_part">'.$part_id_name[$b["part_id"]].'</div>';
		$last_part = $b["part_id"];
	}
	echo '<div class="rec_user_b"><input type="checkbox" name="rec_user[]" value="'.$a.'" id="rec_user_'.$a.'"'.(in_array($a, $cur_select) ? ' checked' : "").' onclick="update_check_color(this)"><label for="rec_user_'.$a.'"'.(in_array($a, $cur_select) ? ' style="color:red"' : "").'>'.$b["realname"].'</label></div>';
}
?>
				<div class="clear"></div>
			</div>
		</td>
	</tr>
	<tr>
		<td class="left">����</td>
		<td class="right"><input name="sort" value="<?php echo $line["sort"]; ?>" class="input" size="10" style="width:80px"> <span class="intro">Խ��Խ��ǰ��Ĭ��ֵΪ0�������Ǹ���</span></td>
	</tr>

</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="type" value="web">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>