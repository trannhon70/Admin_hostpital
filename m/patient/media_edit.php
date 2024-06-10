<?php

/*

// - ����˵�� : ý�������������޸�

// - �������� : ��ҽս�� 

// - ����ʱ�� : 2013-05-03 14:48

*/



if ($_POST) {

	$record = array();
	$record["name"] = $_POST["name"];


	if ($op == "add") {

		$record["hospital_id"] = $user_hospital_id;

		$record["addtime"] = time();

		$record["author"] = $username;

	}



	$sqldata = $db->sqljoin($record);

	if ($op == "edit") {

		$sql = "update $table set $sqldata where id='$id' limit 1";

	} else {

		$sql = "insert into $table set $sqldata";

	}



	if ($db->query($sql)) {

		msg_box("���������ύ�ɹ�", "?", 1);

	} else {

		msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);

	}

}



$title = $editmode ? "�޸ľ�����Դ" : "�����µ���Դ";



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

		alert("�����롰���ơ���"); oForm.name.focus(); return false;

	}

	return true;

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

	<div class="d_item">1.�������ƣ�����ύ����</div>

</div>



<div class="space"></div>



<form name="mainform" action="" method="POST" onSubmit="return Check()">

<table width="100%" class="edit">

	<tr>

		<td colspan="2" class="head">������������</td>

	</tr>

	<tr>

		<td class="left">���ƣ�</td>

		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">���Ʊ�����д</span></td>

	</tr>

</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">

<input type="hidden" name="op" value="<?php echo $op; ?>">



<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>

</form>

</body>

</html>