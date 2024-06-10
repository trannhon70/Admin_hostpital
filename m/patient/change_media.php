<?php

/*

// - ����˵�� : ����

// - �������� : ��ҽս�� 

// - ����ʱ�� : 2013-05-02 15:47

*/

require "../../core/core.php";

$table = "patient_".$user_hospital_id;



if ($user_hospital_id == 0) {

	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");

}



if ($_POST) {

	$author = $_POST["author"];

	$media = $_POST["media"];

	if ($author != '' && $media != '') {

		if ($db->query("update $table set media_from='$media' where binary author='$author'")) {

			msg_box("�����ɹ���", "?", 1);

		}

	}

}





$title = 'ý����Դ�������ù���';



$part_id_name = array(2 => "����ͷ�", 3 => "�绰�ͷ�");



$kefu_23_list = $db->query("select id,realname,part_id from sys_admin where hospitals='$user_hospital_id' and part_id in (2,3)");

foreach ($kefu_23_list as $k => $li) {

	$kefu_23_list[$k]["showname"] = $li["realname"]." (".$part_id_name[$li["part_id"]].")";

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

	if (oForm.media.value == "") {

		alert("��ѡ���µ�ý����Դ����"); oForm.media.focus(); return false;

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

	<div class="d_title">��ʾ��</div>

	<div class="d_item">��ע�⣬�˹����ܹ���ȷ�������������������������������𻵽��޷��ָ�������ؽ�����</div>

</div>



<div class="space"></div>



<form name="mainform" action="?action=move" method="POST" onsubmit="return Check()">

<table width="100%" class="edit">

	<tr>

		<td colspan="2" class="head"></td>

	</tr>

	<tr>

		<td class="left red">���֣�</td>

		<td class="right">

			<select name="author" class="combo" onchange="window.location='?author='+this.value">

				<option value='' style="color:gray">--��ѡ��--</option>

				<?php echo list_option($kefu_23_list, 'realname', 'showname', $_GET["author"]); ?>

			</select>

			<span class="intro">���ֱ���ѡ��</span>

		</td>

	</tr>

<?php if ($_GET["author"] != '') {

	$author = $_GET["author"];

	$medias = $db->query("select media_from, count(media_from) as count from $table where author='$author' group by media_from", "media_from", "count");

	$s = array();

	foreach ($medias as $k => $v) {

		$s[] = $k." (".$v.")";

	}

	$s = implode("<br>", $s);

?>

	<tr>

		<td class="left red">��ǰý����Դ��</td>

		<td class="right">

			<?php echo $s; ?>

		</td>

	</tr>

<?php } ?>

	<tr>

		<td class="left red">�µ�ý����Դ��</td>

		<td class="right">

			<select name="media" class="combo">

				<option value='' style="color:gray">--��ѡ��--</option>

				<?php echo list_option($part_id_name, '', '', ''); ?>

			</select>

			<span class="intro">����ѡ��</span>

		</td>

	</tr>

</table>



<div class="button_line"><input type="submit" class="submit" value="�ύ"></div>



</form>

</body>

</html>