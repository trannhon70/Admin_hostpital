<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title>�鿴Ȩ����ϸ</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><span class="tips">Ȩ����ϸ</span></div>
	<div class="headers_oprate"><input type="button" value="����" onclick="history.back()" class="button"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<table width="100%" class="edit">
	<tr>
		<td class="head" colspan="2">��ϸ����</td>
	</tr>
	<tr>
		<td class="left">���ƣ�</td>
		<td class="right"><b><?php echo $line["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="left">Ȩ�ޣ�</td>
		<td class="right"><?php echo $power->show($line["menu"]); ?></td>
	</tr>
	<tr>
		<td class="left">��ʵ������</td>
		<td class="right"><?php echo $line["author"]; ?></td>
	</tr>
	<tr>
		<td class="left">����վ�㣺</td>
		<td class="right"><?php echo date("Y-m-d H:i:s", $line["addtime"]); ?></td>
	</tr>
</table>

<div class="button_line"><button onclick="history.back()" class="buttonb">����</button></div>
<div class="space"></div>
</body>
</html>