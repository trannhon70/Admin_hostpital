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

	$fromname = $_POST["fromname"];

	$toname = $_POST["toname"];

	if ($fromname != '' && $toname != '') {

		if ($db->query("update $table set author='$toname' where binary author='$fromname'")) {

			msg_box("�����ɹ���", "?", 1);

		}

	}

}





$title = '����ת�ƹ���';



$kefu_23_list = $db->query("select author,count(author) as acount from $table where author!='' group by author order by binary author");

foreach ($kefu_23_list as $k => $li) {

	$kefu_23_list[$k]["author_name"] = $li["author"]." (".$li["acount"].")";

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

	if (oForm.fromname.value == "") {

		alert("�����롰ԭ���֡���"); oForm.fromname.focus(); return false;

	}

	if (oForm.toname.value == "") {

		alert("�����롰�����֡���"); oForm.toname.focus(); return false;

	}

	return true;

}

</script>

</head>



<body>

<!-- ͷ�� begin -->

<div class="headers" >

	<div class="headers_title"><span class="tips"><?php echo $title; ?></span></div>

	<div class="headers_oprate"><button onclick="history.back()" class="button">����</button></div>

</div>

<!-- ͷ�� end -->



<div class="space"></div>



<div class="description">

	<div class="d_title">��ʾ��</div>

	<div class="d_item">����ת������������ύ��ť��ʼת��</div>

</div>



<div class="space"></div>



<form name="mainform" action="?action=move" method="POST" onsubmit="return Check()">

<table width="100%" class="edit">

	<tr>

		<td colspan="2" class="head">ת������</td>

	</tr>

	<tr>

		<td class="left red">ԭ���֣�</td>

		<td class="right">

			<select name="fromname" class="combo">

				<option value='' style="color:gray">--��ѡ��--</option>

				<?php echo list_option($kefu_23_list, 'author', 'author_name', ''); ?>

			</select>

			<span class="intro">ԭ���֣�����Ϊ��</span>

		</td>

	</tr>

	<tr>

		<td class="left red">�����֣�</td>

		<td class="right">

			<input name="toname" id="toname" class="input" style="width:150px">

			<span class="intro">�µ����֣�����Ϊ��</span>

		</td>

	</tr>

</table>



<div class="button_line"><input type="submit" class="submit" value="�ύ"></div>



</form>

</body>

</html>