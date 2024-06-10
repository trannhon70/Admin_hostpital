<html>
<head>
<title>������������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
</head>

<body>
<?php
require "../../core/core.php";
if($_SESSION[$cfgSessionName]["chen"]!="debug")
{
	echo "�Բ��������Ǳ�վ����Ա�޷�������ǰҳ��";
	exit();
}
$menu_data = array();
?>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title" style="width:50%"><span class="tips">����֪ͨ�ӿ�(ȫ�֣�Ӧ��������ҽԺ)</span></div>
	<div class="headers_oprate"></div>
</div>
<!-- ͷ�� end -->
<div class="space"></div>
<!-- �����б� begin -->

<table width="100%" align="center" class="list">
	<!-- ��ͷ���� begin -->
	<tr>
		<td class="head" align="center" width="14%">ҽԺ</td>
		<td class="head" align="left" width="54%"><a href='#' title=''>����</a></td>
		<td class="head" align="center" width="12%">����</td>
	</tr>
	<!-- ��ͷ���� end -->
	<!-- ��Ҫ�б����� begin -->
    <?php
	function get_title($id)
	{
		global $db;
		$str=$db->query("select * from mtly where hospital='{$id}'");
		$str=$str[0];
		return $str["name"];
	}
	if ($tmp_data = $db->query("select * from hospital order by id desc")) {
		foreach ($tmp_data as $tmp_line) {
			
		?>
		<form id="mainform<?php echo $tmp_line["id"];?>" action="mtly_update.php?op=edit&id=<?php echo $tmp_line["id"];?>" method="post">
		<tr>
			<td height="90" align="center" class="item"><?=$tmp_line["name"]?></td>
<td align="left" class="item"><textarea name="t" cols="50" rows="5"><?=get_title($tmp_line["id"]);?></textarea></td>
			<td align="center" class="item"><a href='#' class='op' onClick="document.getElementById('mainform<?php echo $tmp_line["id"];?>').submit()">�޸�</a></td>
		</tr>
		</form>
		<?php
		}
	}
	?>
    <tr>
	  <td height="30" align="center" class="item">&nbsp;</td>
	  <td align="center" valign="bottom" class="item">
      </td>
	  <td align="center" class="item">&nbsp;</td>
  </tr>
	<!-- ��Ҫ�б����� end -->
</table>

<!-- �����б� end -->
<div class="space"></div>
<!-- ��ҳ���� begin -->

<div class="footer_op">
  
  <div class="footer_op_right"><div class="pagelink">
  <!-- 
  <div class="pagelink_tips">��<span class="pagelink_cur_page">1</span>/<span class="pagelink_all_page">1</span>ҳ&nbsp;��<span class="pagelink_all_rec">2</span>��</div>
  -->
</div>
<!-- ��ҳ���� end -->
</body>
</html>
