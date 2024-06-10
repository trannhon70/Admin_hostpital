<?php
/*
// - ����˵�� : �޸��ҵ�����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2007-07-19 09:59
*/
require "../../core/core.php";
$table = "sys_admin";

if (!$uid) {
	exit_html("���ܱ༭����...");
}

$uline = $db->query("select * from $table where id='$uid'", 1);

if ($_POST) {

	$detail = array();
	if ($uline["detail"]) {
		$detail = @unserialize($uline["detail"]);
	}

	$detail["�绰"] = $_POST["�绰"];
	$detail["�ֻ�"] = $_POST["�ֻ�"];
	$detail["QQ"] = $_POST["QQ"];
	$detail["��������"] = $_POST["��������"];
	$detail["���˼��"] = $_POST["���˼��"];

	$s = serialize($detail);

	$sql = "update $table set detail='$s' where id='$uid' limit 1";

	if ($db->query($sql)) {
		msg_box("���������޸ĳɹ�", "back", 1, 2);
	} else {
		msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ�����", "back", 1, 5);
	}
}


if ($uline && $uline["detail"]) {
	$tm = @unserialize($uline["detail"]);
	$uline = array_merge($uline, $tm);
} else {
	//exit_html("�޴�����...");
}

$title = "�޸��ҵ�����";
?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><span class="tips"><?php echo $title; ?></span></div>
	<div class="headers_oprate"><input type="button" value="����" onClick="history.back()" class="button"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">�޸���ʾ��</div>
	<li class="d_item">������Ҫ���ֺ�̨��ȫ�Ժ�һ���ԣ��ʻ�����һ��ȷ���Ͳ������޸�</li>
	<li class="d_item">Ϊ�˷�����˺���ȡ����ϵ������������ʵ��д���ĸ������Ϻ���ϵ��ʽ</li>
	<li class="d_item">�������ϳ���ʵ�������⣬δ����Ȩ����̨������->������Ա������->���鿴��Ȩ�޵��˽����ܲ鿴</li>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">�޸��ҵ����ϣ�</td>
	</tr>
	<tr>
		<td class="left">��¼����</td>
		<td class="right"><b><?php echo $uline["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="left"><font color='red'>��ʵ������</font></td>
		<td class="right"><input name="realname" value="<?php echo $uline["realname"]; ?>" class="input" style="width:120px" disabled="true"> <span class="intro">��ʵ���������޸�</span></td>
	</tr>
	<tr>
		<td class="left">�绰��</td>
		<td class="right"><input name="�绰" value="<?php echo $uline["�绰"]; ?>" class="input" style="width:180px"></td>
	</tr>
	<tr>
		<td class="left">�ֻ���</td>
		<td class="right"><input name="�ֻ�" value="<?php echo $uline["�ֻ�"]; ?>" class="input" style="width:120px"></td>
	</tr>
	<tr>
		<td class="left">QQ��</td>
		<td class="right"><input name="QQ" value="<?php echo $uline["QQ"]; ?>" class="input" style="width:120px"></td>
	</tr>
	<tr>
		<td class="left">�������䣺</td>
		<td class="right"><input name="��������" value="<?php echo $uline["��������"]; ?>" class="input" style="width:250px"></td>
	</tr>
	<tr>
		<td class="left">���˼�飺</td>
		<td class="right"><textarea class="input" name="���˼��" style="width:400px;height:80px"><?php echo $uline["���˼��"]; ?></textarea></td>
	</tr>
</table>

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
</form>

<div class="space"></div>
</body>
</html>