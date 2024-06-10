<?php

/*

// - ����˵�� : ������������

// - �������� : ��ҽս�� 

// - ����ʱ�� : 2013-06-12 21:50

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

			msg_box("�����ɹ���", "patient_change_media.php", 1);

		}

	}

}





$title = '����������������';



$date = $db->query("select min(addtime) as btime, max(addtime) as etime from $table", 1);

$btime = $date["btime"];

$etime = $date["etime"];



$begin_year = date("Y", $btime);

$end_year = date("Y", $etime);



$lists = array();

for ($i=$begin_year; $i<=$end_year; $i++) {

	$date_begin = mktime(0,0,0,0,0,$i);

	$date_end = mktime(0,0,0,0,0,$i+1);



	$count = $db->query("select count(id) as count from $table where addtime>=$date_begin and addtime<$date_end", 1, "count");

	if ($i == date("Y")) {

		$lists[$i] = $i."�� (".$count." ��) [��������]";

	} else {

		$lists[$i] = $i."�� (".$count." ��)";

	}

}



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

	if (byid("server_year").value == oForm.year.value) {

		alert("����������������ݣ�"); oForm.year.focus(); return false;

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

	<div class="d_item" style="color:red;">����ǰ����ϵ����Ա�������ݣ�������ܻ���Σ�գ�</div>

</div>



<div class="space"></div>



<form name="mainform" action="?action=do" method="POST" onsubmit="return Check()">

<table width="100%" class="edit">

	<tr>

		<td colspan="2" class="head"></td>

	</tr>

	<tr>

		<td class="left red">������ݣ�</td>

		<td class="right">

			<select name="year" class="combo">

				<option value='' style="color:gray">--��ѡ��--</option>

				<?php echo list_option($lists, "_key_", "_value_"); ?>

			</select>

			<span class="intro">����ѡ��</span>

		</td>

	</tr>

</table>



<div class="button_line"><input type="submit" class="submit" value="�ύ"></div>



<input id="server_year" type="hidden" value="<?php echo date("Y"); ?>">



</form>

</body>

</html>