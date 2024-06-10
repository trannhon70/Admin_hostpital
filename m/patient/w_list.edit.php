<?php
/*
// - ����˵�� : �������޸Ĳ�������
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2013-05-01 05:57
*/
require "../../core/core.php";
if ($_POST) {
	$id=trim($_GET["id"]);
	$name = trim($_POST["name"]);
	$sex = trim($_POST["sex"]);
	$age = trim($_POST["age"]);
	$tel = trim($_POST["tel"]);
	$qq = trim($_POST["qq"]);
	$content = trim($_POST["content"]);
	$zhuanjia_num = trim($_POST["zhuanjia_num"]);
	$order_date = strtotime(trim($_POST["order_date"]));
	$memo = trim($_POST["memo"]);
	$status = trim($_POST["status"]);
	
	$d_id = $db->query("update yy_list set name='$name',sex='$sex',age='$age',tel='$tel',qq='$qq',content='$content',zhuanjia_num='$zhuanjia_num',order_date='$order_date',memo='$memo',status='$status' where id='{$id}'");
	
	echo '<script language="javascript">alert("�޸ĳɹ���");</script>';
}

//ѡ��˵�����
function list_option_a($list, $key_field='_key_', $value_field='_value_', $default_value='') {
	$option = array();
	foreach ($list as $k => $li) {
		// option value=��ֵ
		if ($key_field != '') {
			if ($key_field == "_key_" || $key_field == "_value_") {
				$value = $key_field == "_key_" ? $k : $li;
			} else {
				$value = $li[$key_field];
			}
		} else {
			$value = $li;
		}

		// �Ƿ�ѡ��:
		$select = ($value == $default_value ? 'selected' : '');

		// ��ʾ����:
		if ($value_field != '') {
			if ($value_field == "_key_" || $value_field == "_value_") {
				$title = $value_field == "_key_" ? $k : $li;
			} else {
				$title = $li[$value_field];
			}
		} else {
			$title = $li;
		}
		// ���Ϊ��ǰ����ʾһ�� * ���:
		if ($select) {
			$title .= " *";
		}
		$option[] = '<option value="'.$value.'" '.$select.'>'.$title.'</option>';
	}

	return implode('', $option);
}

$line = $db->query_first("select * from yy_list where id='$id' limit 1");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="res/datejs/picker.js" language="javascript"></script>
<style>
.dischk {width:6em; height:16px; line-height:16px; vertical-align:middle; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; padding:0; margin:0; }
</style>
<script language="javascript">
function input(id, value) {
	if (byid(id).disabled != true) {
		byid(id).value = value;
	}
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><span class="tips">�޸Ĳ�������</span></div>
	<div class="header_center"><!-- <button onclick="if (check_data()) document.forms['mainform'].submit();" class="buttonb">�ύ����</button> --></div>
	<div class="headers_oprate"><button onClick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div class="description">
	<div class="d_title">��ʾ��</div>
	<div class="d_item">1.����������д����2.�绰���������д������������֣�������7λ����3.δ��������д�ڱ�ע�С�</div>
</div>

<div class="space"></div>
<form name="mainform" method="POST" onSubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">���˻�������</td>
	</tr>
	<tr>
		<td class="left">������</td>
		<td class="right"><input name="name" id="name" value="<?php echo $line["name"]; ?>" class="input" style="width:200px" <?php echo $ce["name"]; ?>> <span class="intro">* ���Ʊ�����д</span></td>
	</tr>
	<tr>
		<td class="left">�Ա�</td>
		<td class="right"><input name="sex" id="sex" value="<?php echo $line["sex"]; ?>" class="input" style="width:80px" <?php echo $ce["sex"]; ?>> <a href="javascript:input('sex', '��')">[��]</a> <a href="javascript:input('sex', 'Ů')">[Ů]</a> <span class="intro">��д�����Ա�</span></td>
	</tr>
	<tr>
		<td class="left">���䣺</td>
		<td class="right"><input name="age" id="age" value="<?php echo $line["age"]; ?>" class="input" style="width:80px" <?php echo $ce["age"]; ?>> <span class="intro">��д����</span></td>
	</tr>
<?php if ($op == "add" || ($op == "edit" && $line["author"] == $realname)) { ?>
	<tr>
		<td class="left">�绰��</td>
		<td class="right"><input name="tel" id="tel" value="<?php echo $line["tel"]; ?>" class="input" style="width:200px" <?php echo $ce["tel"]; ?> onChange="check_repeat('tel', this)">  <span class="intro">�绰������ֻ�(�ɲ���)</span></td>
	</tr>
<?php } ?>
	<tr>
		<td class="left">QQ��</td>
		<td class="right"><input name="qq" value="<?php echo $line["qq"]; ?>" class="input" style="width:140px" <?php echo $ce["qq"]; ?>>  <span class="intro">����QQ����</span></td>
	</tr>
	<tr>
		<td class="left" valign="top">��ѯ���ݣ�</td>
		<td class="right"><textarea name="content" style="width:60%; height:72px;vertical-align:middle;" <?php echo $ce["content"]; ?> class="input"><?php echo $line["content"]; ?></textarea> <span class="intro">��ѯ�����ܽ�</span></td>
	</tr>

	<tr>
		<td class="left">�������ң�</td>
		<td class="right" style="color:red"><?php echo $line["ks"]; ?></td>
	</tr>

	<tr>
		<td class="left">������Դ��</td>
		<td class="right">
        <?php echo $str=$line["is_local"]==1 ? "����":"���"; ?>
        </td>
	</tr>

	<tr>
		<td class="left">ר�Һţ�</td>
		<td class="right"><input name="zhuanjia_num" value="<?php echo $line["zhuanjia_num"]; ?>" class="input" size="30" style="width:200px"></td>
	</tr>
	<tr>
		<td class="left" valign="top">ԤԼʱ�䣺</td>
		<td class="right"><input name="order_date" value="<?php echo $line["order_date"] ? @date('Y-m-d H:i:s', $line["order_date"]) : ''; ?>" class="input" style="width:150px" id="order_date" <?php echo $ce["order_date"]; ?>> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm:ss'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"> <span class="intro">��ע�⣬�˴��ѵ�����ԤԼʱ�䲻�������ϸ���<?php echo date("j"); ?>�ţ����������޷��ύ</span></td>
	</tr>

	<tr>
		<td class="left" valign="top">��ע��</td>
		<td class="right"><textarea name="memo" style="width:60%; height:48px;vertical-align:middle;" class="input" <?php echo $ce["memo"]; ?>><?php echo $line["memo"]; ?></textarea> <span class="intro">������ע��Ϣ</span></td>
	</tr>
<?php if ($line["edit_log"] && $line["author"] == $realname) { ?>
	<?php } ?>


<?php // ������Ŀ -------------  ?>
<?php
if (in_array($uinfo["part_id"], array(4,9,12)) && $line["status"] == 1) { ?>
	<!-- ���Ʒ��� -->
	<?php } ?>


<?php // ���� -------------  ?>
<?php
if (in_array($uinfo["part_id"], array(4,9,12)) && $line["status"] == 1) { ?>
	<?php } ?>

</table>


<div class="space"></div>
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">�Ƿ�Ժ</td>
	</tr>
	<tr>
		<td class="left">��Լ״̬��</td>
		<td class="right">
        <?php $me_status_array = array ( 0 => array ( 'id' => '0','name' => '�ȴ�'),1 => array ( 'id' => '1','name' => '�ѵ�'),2 => array ( 'id' => '2','name' => 'δ��'),3 => array ( 'id' => '3','name' => 'ԤԼδ��'));?>
			<select name="status" class="combo" <?php echo $ce["status"]; ?>> <!-- onchange="change_yisheng(this.value)" -->
				<option value="0" style="color:gray">--��ѡ��--</option>
				<?php echo list_option_a($me_status_array, 'id', 'name', ($mode == "add" && $uinfo["part_id"] == 4) ? 1 : $line["status"]); ?>
			</select>
		</td>
	</tr>
  </table>

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>