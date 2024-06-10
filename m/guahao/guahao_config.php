<?php
/*
// - ����˵�� : �޸ĹҺ�����
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2010-03-31 11:23
*/
require "../../core/core.php";
$table = "guahao_config";

if ($_POST) {
	$record = array();
	$record["config"] = $_POST["config"];

	$sqldata = $db->sqljoin($record);
	$sql = "update $table set $sqldata where name='filter' limit 1";

	if ($db->query($sql)) {
		msg_box("�����ύ�ɹ�", "?self", 1);
	} else {
		msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);
	}
}

$line = $db->query("select * from $table where name='filter' limit 1", 1);
$title = "�޸�����";
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<script language="javascript">
function check_data() {
	var oForm = document.mainform;
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
	<div class="d_item">1. ��ע�⣬�����öԱ�ϵͳ�ڵ�����ҽԺ����Ч����ȫ�ֵġ�</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">����</td>
	</tr>
	<tr>
		<td class="left">���˴ʻ㣺</td>
		<td class="right"><textarea name="config" style="width:60%; height:80px;"><?php echo $line["config"]; ?></textarea> <span class="intro">�����Զ���(,)����</span></td>
	</tr>
</table>

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>