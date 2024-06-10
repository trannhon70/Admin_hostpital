<html>
<head>
<meta charset="gbk" />
<title><?php echo $pinfo["title"]; ?></title>
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<script src="../../res/datejs/picker.js" language="javascript"></script>
<style>
#color_tips {padding:0 0 8px 12px; }
</style>

<script language="javascript">
function set_come(id, come_value) {
	var xm = new ajax();
	xm.connect('http/patient_set_come.php', 'GET', 'id='+id+'&come='+come_value, set_come_do);
}

function set_come_do(o) {
	var out = ajax_out(o);
	if (out["status"] == 'ok') {
		byid("come_"+out["id"]).innerHTML = ['�ȴ�', '�ѵ�', 'δ��'][out["come"]];
		byid("come_"+out["id"]+"_"+out["come"]).style.display = 'none';
		byid("come_"+out["id"]+"_"+(out["come"]==1 ? 2 : 1)).style.display = 'inline';
		byid("list_line_"+out["id"]).style.color = ['', 'red', 'gray'][out["come"]];
	} else {
		alert("����ʧ�ܣ����Ժ����ԣ�");
	}
}

function set_xiaofei(id, value) {
	var xm = new ajax();
	xm.connect('/http/patient_set_xiaofei.php', 'GET', 'id='+id+'&xiaofei='+value, set_xiaofei_do);
}

function set_xiaofei_do(o) {
	var out = ajax_out(o);
	if (out["status"] == 'ok') {
		if (out["xiaofei"] == '0') {
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',1); return false;">��</a>';
		} else {
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',0); return false;">��</a>';
		}
		byid("xiaofei_"+out["id"]).innerHTML = button;
	} else {
		alert("����ʧ�ܣ����Ժ����ԣ�");
	}
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title" style="width:40%"><span class="tips"><?=$hospital_id_name[$user_hospital_id]?> - ԤԼ�б�</span></div>
	<div class="header_center">
		<?php echo $power->show_button("add"); ?>&nbsp;
		<button onClick="location='?op=search'" class="buttonb">�߼�����</button>
		<form action="?" method="GET" style="display:inline;">
			<input name="date" id="ch_date" onChange="this.form.submit();" value="<?php echo $_GET["date"] ? $_GET["date"] : date("Y-m-d"); ?>" style="width:0px; overflow:hidden; padding:0; margin:0; border:0;">
		</form>
        <button onClick="picker({el:'ch_date',dateFmt:'yyyy-MM-dd'})" class="buttonb">���ղ鿴</button>
	</div>
	<div class="headers_oprate"><form name="topform" method="GET">ģ��������<input name="key" value="<?php echo $_GET["key"].$l; ?>" class="input" size="8">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<button onClick="location='?'" class="search" title="�˳�������ѯ">����</button>&nbsp;&nbsp;<button onClick="history.back()" class="button" title="������һҳ">����</button></form></div>
	<div class="clear"></div>
</div>
<!-- ͷ�� end -->

<!-- ͳ������ begin -->
<div class="space"></div>
<table width="100%" style="border:2px solid #D9D9D9; border-left:0; border-right:0; background-color:#F2F2F2; color:black; ">
	<tr>
		<td width="33%">&nbsp;<b>ͳ������:</b> <?php echo $res_report; ?></td>
		<td align="center"><?php if (in_array($uinfo["part_id"], array(2,3))) { ?><b>���Ž���:</b> <?php echo $part_report; ?><?php } ?></td>
		<td width="33%" align="right"><b>��������: </b> <?php echo $today_report; ?></td>
	</tr>
</table>
<!-- ���������ͳ������ end -->

<div class="space"></div>

<div id="color_tips">
��ɫ��ǣ�
<?php foreach ($line_color_tip as $k => $v) { ?>
<font color="<?php echo $line_color[$k]; ?>"><?php echo $v; ?></font>&nbsp;
<?php } ?>
</div>

<!-- �����б� begin -->
<?php echo $t->show(); ?>
<!-- �����б� end -->

<!-- ��ҳ���� begin -->
<div class="space"></div>
<div class="footer_op">
	<div class="footer_op_left"><button onClick="select_all()" class="button" disabled="true">ȫѡ</button></div>
	<div class="footer_op_right"><?php echo $pagelink; ?></div>
</div>
<!-- ��ҳ���� end -->

<!-- <?php echo $s_sql; ?> -->

</body>
</html>