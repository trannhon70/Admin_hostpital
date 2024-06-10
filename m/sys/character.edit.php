<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<script language="javascript">
function Check() {
	var oForm = document.mainform;
	if (oForm.ch_name.value == "") {
		alert("�����롰Ȩ�����ơ���");
		oForm.ch_name.focus();
		return false;
	}
	return true;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><span class="tips"><?php echo $title; ?></span></div>
	<div class="headers_oprate"><input type="button" value="����" onclick="history.back()" class="button"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">��ʾ��</div>
	<li class="d_item">����ؽ������䡰ϵͳ������һ���Ȩ�ޣ�����Ϥϵͳ����Ա��ϵͳ�������ý�ʹ��������������������</li>
	<li class="d_item">��ϵͳ��־����һ��Ŀ��ǣ��ϵͳ��Ҫ�������ݣ�Ҳ���������Ȩ��</li>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">Ȩ������</td>
	</tr>
	<tr>
		<td class="left">Ȩ�����ƣ�</td>
		<td class="right"><input name="ch_name" value="<?php echo $cline["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">Ȩ�����Ʊ�����д</span></td>
	</tr>
	<tr>
		<td class="left">Ȩ����ϸ��</td>
		<td class="right"><?php echo $power->show_power_table($usermenu, $cline["menu"]); ?></td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo intval($id); ?>">
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>

<div class="space"></div>
</body>
</html>