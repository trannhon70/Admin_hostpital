<?php

/*

// - ����˵�� : ����ͳһת������

// - �������� : ��ҽս�� 

// - ����ʱ�� : 2013-06-06 14:50

*/

require "../../core/core.php";

$table = "patient_".$user_hospital_id;



if ($user_hospital_id == 0) {

	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");

}



if ($_POST) {

	$author = $_POST["author"];

	$part_id = $_POST["part_id"];

	if ($author != '' && $part_id != '') {

		if ($db->query("update $table set part_id='$part_id' where binary author='$author'")) {

			msg_box("�����ɹ���", "?", 1);

		}

	}

}



if ($_GET["do"] == "all") {

	$kefu_list = $db->query("select id,realname,part_id from sys_admin where hospitals='$user_hospital_id' and part_id in (2,3,4)");

	foreach ($kefu_list as $li) {

		$author = $li["realname"];

		$part_id = $li["part_id"];

		if ($author != '' && $part_id > 0) {

			$db->query("update $table set part_id=$part_id where binary author='$author'");

		}

	}

	msg_box("ȫ�������ɹ���", "?", 1);

}





$title = '���ŵ�������';



$part_id_name = $db->query("select id,name from sys_part", "id", "name");



$kefu_list = $db->query("select id,realname,part_id from sys_admin where hospitals='$user_hospital_id' and part_id in (1,2,3,4)");

foreach ($kefu_list as $k => $li) {

	$kefu_list[$k]["showname"] = $li["realname"]." (".$part_id_name[$li["part_id"]].")";

}

?>

<html>

<head>

<title><?php echo $title; ?></title>

<meta http-equiv="Content-Type" content="text/html;charset=gb2312">

<link href="/res/base.css" rel="stylesheet" type="text/css">

<script src="/res/base.js" language="javascript"></script>

<script src="res/datejs/picker.js" language="javascript"></script>

<script language="javascript">

function Check() {

	var oForm = document.mainform;

	if (oForm.author.value == "") {

		alert("��ѡ�����֡���"); oForm.author.focus(); return false;

	}

	if (oForm.part_id.value == "") {

		alert("��ѡ��Ҫ���õ��µĲ��ţ�"); oForm.part_id.focus(); return false;

	}

	if (confirm("�Ƿ�ȷ��������ϸ�����ȷ���£���Ū����Ŷ��")) {

		return true;

	} else {

		return false;

	}

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



<div class="description">

	<div class="d_title">��������ǽ��ʲô����ģ�</div>

	<div class="d_item">&nbsp;&nbsp;��������Ϊ�˽����������������ģ�ĳ�ˣ����硰���������ڴ����ʻ���ʱ�򣬲����ǡ�����ͷ�������ʵ���ϸ����ǵ绰�ͷ����������ӵĲ������Ͻ�ֻ������ͷ������������ʾ����������ʾ�ڵ绰�������������ʹ�����˵Ĳ������µ������绰���ţ���Щ�ɵĲ�������Ҳ�������Զ����������<br>��Ϊ���޸Ĳ��ŵ�ʱ���Զ������²������ϵĲ����أ���Ϊ��������£�����Ҫ�����Ĳ������������֮ǰ��ȷ��������ͷ�������������������ͷ������ӵ����ϣ�����������ͷ����ġ�������Ϊ���Ժ�ת���绰�ͷ���������Ҳһ������绰�ͷ�����ʵ���ϣ�����ǰ������ͷ����ӵĲ�������Ӧ���ֲ��䡣</div>

	<div class="d_item">&nbsp;&nbsp;��ֻ��������Ҫ�����Ĳ������ݣ�����ɵĲ�Ҫ������</div>

</div>



<div class="space"></div>

<form name="mainform" action="?action=move" method="POST" onsubmit="return Check()">

<table width="100%" class="edit">

	<tr>

		<td colspan="2" class="head"></td>

	</tr>

	<tr>

		<td class="left red">ѡ��Ҫ�������ˣ�</td>

		<td class="right">

			<select name="author" class="combo">

				<option value='' style="color:gray">--��ѡ��--</option>

				<?php echo list_option($kefu_list, 'realname', 'showname', $_GET["author"]); ?>

			</select>

			<span class="intro">��Ҫ����˭�����ϣ���ѡ����������</span>

		</td>

	</tr>

	<tr>

		<td class="left red">����ͳһ�޸�Ϊ��</td>

		<td class="right">

			<select name="part_id" class="combo">

				<option value='' style="color:gray">--��ѡ��--</option>

				<?php echo list_option($part_id_name, "_key_", "_value_", ""); ?>

			</select>

			<span class="intro">����ѡ��</span>

		</td>

	</tr>

</table>

<div class="button_line">

<input type="submit" class="submit" value="�ύ">

 &nbsp;&nbsp;&nbsp;&nbsp; ����û�����̫����

<button onclick="if (confirm('�Ƿ�ȷ��Ҫȫ��������')) {location='?do=all'; this.disabled=true;}" class="buttonb">ȫ������</button> (ע��ֻ���� ���硢�绰����ҽ)

</div>

</form>



<table width="100%" class="list">

	<tr>

		<td class="head" align="center">����</td>

		<td class="head">��������״̬</td>

	</tr>



<?php foreach ($kefu_list as $li) {

	$author = $li["realname"];

	$data = $db->query("select part_id, count(part_id) as count, min(addtime) as begintime, max(addtime) as endtime from $table where binary author='$author' group by part_id");

	$tmp = array();

	foreach ($data as $tm) {

		$tmp[] = $part_id_name[$tm["part_id"]]." (".$tm["count"].") &nbsp;&nbsp; �� ".date("Y-m-d", $tm["begintime"])." �� ".date("Y-m-d", $tm["endtime"])."";

	}

	$tmp = implode("<br>", $tmp);

?>

	<tr>

		<td class="item" align="center"><?php echo $li["showname"]; ?></td>

		<td class="item" align="left"><?php echo $tmp; ?></td>

	</tr>

<?php } ?>



</table>



<br>

<br>



</body>

</html>