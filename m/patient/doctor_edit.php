<?php

/*

// - ����˵�� : ҽ���������޸�

// - �������� : ��ҽս�� 

// - ����ʱ�� : 2013-05-02 16:44

*/



if ($_POST) {

	$r = array();

	$r["doctor_num"] = $_POST["doctor_num"];

	$r["name"] = $_POST["name"];

	$r["intro"] = $_POST["intro"];



	if ($op == "add") {

		$r["hospital_id"] = $user_hospital_id;

		$r["addtime"] = time();

		$r["author"] = $username;

	}



	$sqldata = $db->sqljoin($r);

	if ($op == "edit") {

		$sql = "update $table set $sqldata where id='$id' limit 1";

	} else {

		$sql = "insert into $table set $sqldata";

	}



	if ($nid = $db->query($sql)) {

		msg_box("�����ύ�ɹ�", "?", 1);

	} else {

		msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);

	}

}



$title = $editmode ? "�޸�ҽ������" : "����ҽ��";



$hospital_list = $db->query("select id,name from ".$tabpre."hospital");

?>

<html>

<head>

<title><?php echo $title; ?></title>

<meta http-equiv="Content-Type" content="text/html;charset=gb2312">

<link href="../../res/base.css" rel="stylesheet" type="text/css">

<script src="../../res/base.js" language="javascript"></script>

<script language="javascript">

function Check() {

	var oForm = document.mainform;

	if (oForm.name.value == "") {

		alert("�����롰ҽ�����֡���"); oForm.name.focus(); return false;

	}

	return true;

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

	<div class="d_item">1.����ҽ�����ּ���飨���ɲ����룩������ύ����</div>

</div>



<div class="space"></div>



<form name="mainform" action="" method="POST" onsubmit="return Check()">

<table width="100%" class="edit">

	<tr>

		<td colspan="2" class="head">ҽ������</td>

	</tr>

	<tr>

		<td class="left">ҽ�����֣�</td>

		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">* ҽ�����ֱ�����д</span></td>

	</tr>

	<tr>

		<td class="left">ҽ����ţ�</td>

		<td class="right"><input name="doctor_num" value="<?php echo $line["doctor_num"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">ѡ��</span></td>

	</tr>

	<tr>

		<td class="left">ҽ����飺</td>

		<td class="right"><textarea name="intro" class="input" style="width:60%; height:80px; overflow:visible;"><?php echo $line["intro"]; ?></textarea> <span class="intro">ѡ��</span></td>

	</tr>

</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">

<input type="hidden" name="op" value="<?php echo $op; ?>">

<input type="hidden" name="linkinfo" value="<?php echo $linkinfo; ?>">



<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>

</form>

</body>

</html>