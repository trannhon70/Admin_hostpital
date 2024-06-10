<?php
/*
// - ����˵�� : ���������Һ�
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2014-03-16 15:00
*/
error_reporting(0);
ob_start();
define("ROOT", str_replace("\\", "/", dirname(dirname(__FILE__)))."/");

$time = $timestamp = time();
$page_begin_time = $time.substr(microtime(), 1, 7);
$islocal = @file_exists("D:/Server/") ? true : false;

//�ű����ִ��ʱ��
set_time_limit(30);

require ROOT."core/config.php";
require ROOT."core/class.mysql.php";
$db = new mysql($mysql_server);
//if (!$islocal) {
	$db->show_error = false;
//}

// session ����:
//require ROOT."core/session.php";

// ���غ����ļ�
include ROOT."core/config.more.php";
require ROOT."core/function.php";
$mode = "add";
$op="add";
$line["author"]="��������";
if($_GET["hospital_id"]!="")
{
	$hid=$_GET["hospital_id"];
	$user_hospital_id=$hid;
	$table="patient_".$hid;
}
if ($_POST) {
	$po = &$_POST; //���� $_POST

	if ($mode == "edit") {
		$oldline = $db->query("select * from $table where id=$id limit 1", 1);
	} else {
		// ���һ�����ڵĲ����������ظ���:
		$name = trim($po["name"]);
		$tel = trim($po["tel"]);
		if (strlen($tel) >= 7) {
			$thetime = strtotime("-1 month");
			$list = $db->query("select * from $table where tel='$tel' and addtime>$thetime limit 1", 1);
			if ($list && count($list) > 0) {
				msg_box("�绰�����ظ����ύʧ��", "back", 1, 5);
			}
		}
	}

	/*
	// ������������ֶ�:
	if (!$oldline) {
		$test_line = $db->query("select * from $table limit 1", 1);
	} else {
		$test_line = $oldline;
	}

	// �Զ�����ֶ�:  ���ڿ���ȥ��
	if (!isset($test_line["engine"])) {
		$db->query("alter table `{$table}` add `engine` varchar(32) not null after `media_from`;");
	}
	if (!isset($test_line["engine_key"])) {
		$db->query("alter table `{$table}` add `engine_key` varchar(32) not null after `engine`;");
	}
	if (!isset($test_line["from_site"])) {
		$db->query("alter table `{$table}` add `from_site` varchar(40) not null after `engine_key`;");
	}
	*/


	// �ͷ����Ӽ�������  2010-10-27
	/*if ($po["disease_id"] == -1) {
		$d_name = $po["disease_add"];
		$d_id = 0;
		if ($d_name != '') {
			$d_id = $db->query("insert into disease set hospital_id='$hid', name='$d_name', addtime='$time', author='$username'");
		}
		$po["disease_id"] = $d_id ? $d_id : 0;
	}*/

//http://user.qzone.qq.com/4781921/infocenter
	$r = array();
	if (isset($po["name"])) $r["name"] = trim($po["name"]);
	if (isset($po["sex"])) $r["sex"] = $po["sex"];
	if (isset($po["qq"])) $r["qq"] = $po["qq"]; //2010-10-28
	if (isset($po["age"])) $r["age"] = $po["age"];
	if (isset($po["content"])) $r["content"] = $po["content"];
	if (isset($po["disease_id"])) $r["disease_id"] = $po["disease_id"];
	if (isset($po["depart"])) $r["depart"] = $po["depart"];
	if (isset($po["media_from"])) $r["media_from"] = $po["media_from"];
	if (isset($po["engine"])) $r["engine"] = $po["engine"];
	if (isset($po["engine_key"])) $r["engine_key"] = $po["engine_key"];
	if (isset($po["from_site"])) $r["from_site"] = $po["from_site"];
	if (isset($po["from_account"])) $r["from_account"] = $po["from_account"]; //2010-11-04
	if (isset($po["zhuanjia_num"])) $r["zhuanjia_num"] = $po["zhuanjia_num"];
	if (isset($po["is_local"])) $r["is_local"] = $po["is_local"];
	if (isset($po["area"])) $r["area"] = $po["area"];
	//if (isset($po["mtly"])) $r["mtly"] = $po["mtly"];
	$uinfo["part_id"]=1;
	if (($username == "admin") || !in_array($uinfo["part_id"], array(4))) { 
		if (isset($po["addtime"])) $r["addtime"] = strtotime($po["addtime"]);
	}
	// �޸�ʱ��:
	if (isset($po["order_date"])) {
		$order_date_post = @strtotime($po["order_date"]);
		if ($mode == "add") {

			// ����޸ģ���ʱ�䲻�ܱ��޸�Ϊ��ǰʱ���һ����֮ǰ(2011-01-15)
			if ($order_date_post < strtotime("-1 month")) {
				msg_box("ԤԼʱ�䲻����һ����֮ǰ�������ȼ�����ĵ���ʱ���Ƿ����󣡣�  �뷵��������д��", "back", 1, 5);
			}

			$r["order_date"] = $order_date_post; //����
		} else {
			//�ж�ʱ���Ƿ����޸�
			if ($order_date_post != $oldline["order_date"]) {

				// ����޸ģ���ʱ�䲻�ܱ��޸�Ϊ��ǰʱ���һ����֮ǰ(2011-01-15)
				if ($order_date_post < strtotime("-1 month")) {
					msg_box("ԤԼʱ�䲻�ܱ��޸ĵ�һ����֮ǰ�������ȼ�����ĵ���ʱ���Ƿ����󣡣�  �뷵��������д��", "back", 1, 5);
				}

				$r["order_date"] = $order_date_post;
				$r["order_date_changes"] = intval($oldline["order_date_changes"])+1;
				$r["order_date_log"] = $oldline["order_date_log"].(date("Y-m-d H:i:s")." ".$realname." �޸� (".date("Y-m-d H:i", $oldline["order_date"])." => ".date("Y-m-d H:i", $order_date_post).")<br>");

				// ����޸�ԤԼʱ�䣬�Զ��޸�״̬Ϊ�ȴ�
				if ($oldline["status"] == 2) {
					$r["status"] = 0;
				}
			}
		}
	}

	if (isset($po["memo"])) $r["memo"] = $po["memo"];
	if (isset($po["status"])) $r["status"] = $po["status"];
	if (isset($po["fee"])) $r["fee"] = $po["fee"]; //2010-11-18

	// ���Ӵ����޸�Ϊ��ǰ�ĵ�ҽ:
	if ($mode == "edit" && $oldline["jiedai"] == '' && $uinfo["part_id"] == 4) {
		$r["jiedai"] = $realname;
	}

	// ��ҽ����ֱ������Ϊ�ѵ�:
	if ($mode == "add" && $uinfo["part_id"] == 4) {
		$r["status"] = 1; //�ѵ�
		$r["jiedai"] = $realname;
	}

	if (isset($po["doctor"])) {
		$r["doctor"] = $po["doctor"];
	}

	// ������������Ŀ:
	if ($po["update_xiangmu"]) {
		$r["xiangmu"] = @implode(" ", $po["xiangmu"]);
	}

	if (isset($po["huifang"]) && trim($po["huifang"]) != '') {
		$r["huifang"] = $oldline["huifang"]."<b>".date("Y-m-d H:i")." [".$realname."]</b>:  ".$po["huifang"]."\n";
	}


	if ($mode == "edit") { //�޸�ģʽ
		if (isset($po["jiedai_content"])) {
			$r["jiedai_content"] = $po["jiedai_content"];
		}

		// �޸ļ�¼��
		if ($oldline["author"] != $realname) {
			$r["edit_log"] = $oldline["edit_log"].$realname.' �� '.date("Y-m-d H:i:s")." �޸Ĺ�������<br>";
		}
	} else {         //����ģʽ
		$r["part_id"] = $uinfo["part_id"];
		$r["addtime"] = time();
		$r["author"] = $realname;
	}

	if (isset($po["tel"])) {
		$tel = trim($po["tel"]);
		//if (strlen($tel) > 20) $tel = substr($tel, 0, 20);
		//$r["tel"] = ec($tel, "ENCODE", md5($encode_password));
		$r["tel"] = $tel;
	}

	if (isset($r["status"])) {
		if (($op == "add" && $r["status"] == 1) || ($op == "edit" && $oldline["status"] != 1 && $r["status"] == 1)) {
			$r["order_date"] = time();
		}
	}

	if ($mode == "edit" && isset($po["rechecktime"]) && $po["rechecktime"] != '') {
		if (strlen($po["rechecktime"]) <= 2 && is_numeric($po["rechecktime"])) {
			$rechecktime = ($r["order_date"] ? $r["order_date"] : $oldline["order_date"]) + intval($po["rechecktime"])*24*3600;
		} else {
			$rechecktime = strtotime($po["rechecktime"]." 0:0:0");
		}
		$r["rechecktime"] = $rechecktime;
	}

	$sqldata = $db->sqljoin($r);
	if ($mode == "edit") {
		$sql = "update $table set $sqldata where id='$id' limit 1";
	} else {
		$sqldata=str_replace("`author`=''","`author`='��������'",$sqldata);
		$sql = "insert into $table set $sqldata";
	}
	include("check_sql.php");
	$sql=strip_tags(check_sql($sql));
	$return = $db->query($sql);

	if ($return) {
		if ($op == "add") $id = $return;
		if ($mode == "edit") {
			//$log->add("edit", ("�޸��˲������ϻ�״̬: ".$oldline["name"]), $oldline, $table);
		} else {
			//$log->add("add", ("�����˲���: ".$r["name"]), $r, $table);
		}
		msg_box("�����ύ�ɹ�", history(2, $id), 1);
	} else {
		msg_box("�����ύʧ�ܣ�������д���������û��ѡ��ҽԺ���߿��ң�\\nҽԺ����ұ�����ѡ��", "back", 1, 5);
	}
}

// ��ȡ�ֵ�:
$hospital_list = $db->query("select id,name from hospital");
$disease_list = $db->query("select id,name from disease where hospital_id='$user_hospital_id' and isshow=1 order by sort desc,sort2 desc", "id", "name");
$doctor_list = $db->query("select id,name from doctor where hospital_id='$user_hospital_id'");
$part_id_name = $db->query("select id,name from sys_part", "id", "name");
$depart_list = $db->query("select id,name from depart where hospital_id='$user_hospital_id'");
$engine_list = $db->query("select id,name from engine", "id", "name");
$sites_list = $db->query("select id,url from sites where hid=$hid", "id", "url");
$account_list = $db->query("select id,concat(name,if(type='web',' (����)',' (�绰)')) as fname from count_type where hid=$hid order by id asc", "id", "fname");
$time1 = strtotime("-3 month");
$area_list = $db->query("select area, count(area) as c from $table where area!='' and addtime>$time1 group by area order by c desc limit 20", "", "area");


$account_first = 0;
if (count($account_list) > 0) {
	$tmp = @array_keys($account_list);
	$account_first = $tmp[0];
}

$status_array = array(
	array("id"=>0, "name"=>'�ȴ�'),
	array("id"=>1, "name"=>'�ѵ�'),
	array("id"=>2, "name"=>'δ��'),
);

$xiaofei_array = array(
	array("id"=>0, "name"=>'δ����'),
	array("id"=>1, "name"=>'������'),
);


// ȡǰ30������:
$show_disease = array();
foreach ($disease_list as $k => $v) {
	$show_disease[$k] = $v;
	if (count($show_disease) >= 50) {
		break;
	}
}

// ��ȡ�༭ ����
$cur_disease_list = array();
if ($mode == "edit") {
	$line = $db->query_first("select * from $table where id='$id' limit 1");

	$cur_disease_list = explode(",", $line["disease_id"]);
	foreach ($cur_disease_list as $v) {
		if ($v && !array_key_exists($v, $show_disease)) {
			$show_disease[$v] = $disease_list[$v];
		}
	}
}


// 2014-3-`6
$media_from_array = explode(" ", "���� �绰"); // ���� ��־ �г� ���� ���ѽ��� ·�� ���� ��̨ ���� ·�� ���� ��� ��ֽ ����
$media_from_array2 = $db->query("select name from media where hospital_id='$user_hospital_id'", "", "name");
foreach ($media_from_array2 as $v) {
	if (!in_array($v, $media_from_array)) {
		$media_from_array[] = $v;
	}
}

$mtly_from_array2 = $db->query("select name from mtly", "", "name");
foreach ($mtly_from_array2 as $v) {
		$mtly_from_array[] = $v;
}

// 2014-3-`6
$is_local_array = array(1 => "����", 2 => "���");


// ���Ƹ�ѡ���Ƿ���Ա༭:
$all_field = explode(" ", "name sex age tel qq content disease_id media_from zhuanjia_num order_date doctor status xiaofei memo xiangmu huifang depart is_local from_account fee");

$ce = array(); // can_edit �ļ�д, ĳ�ֶ��Ƿ��ܱ༭
if ($mode == "edit") { // �޸�ģʽ
	$edit_field = array();
	if ($uinfo["part_id"] == 2 || $uinfo["part_id"] == 3) {
		// δ���޸Ĺ������ϣ������޸�:
		if ($line["status"] == 0 || $line["status"] == 2) {
			if ($line["author"] == $realname) {
				$edit_field = explode(' ', 'qq content disease_id media_from zhuanjia_num memo order_date depart is_local from_account'); //�Լ��޸�
			} else {
				$edit_field[] = 'memo'; //�����Լ������ϣ����޸ı�ע
			}
		} else if ($line["status"] == 1) {
			$edit_field[] = 'memo'; //�ѵ������޸ı�ע
		}

		$edit_field[] = "order_date"; //�޸Ļطã����ܵ���ԤԼʱ��
		$edit_field[] = "huifang";

		if ($uinfo["part_id"] == 3) {
			$edit_field[] = 'xiangmu';
			$edit_field[] = "rechecktime";
		}
	} else if ($uinfo["part_id"] == 4) {
		//if ($line["author"] != $realname) {
		// ��ҽ���޸� �Ӵ�ҽ������Լ״̬�����ѣ���ע������
		if ($line["status"] == 1) {
			$edit_field[] = 'memo';
			$edit_field[] = 'xiangmu';
			$edit_field[] = 'rechecktime';
			$edit_field[] = 'fee';
		} else {
			$edit_field = explode(' ', 'name doctor status xiaofei memo');
		}
	} else if ($uinfo["part_id"] == 12) {
		// �绰�طò���
		$edit_field[] = 'order_date';
		$edit_field[] = 'memo';
		$edit_field[] = 'xiangmu';
		$edit_field[] = 'huifang';
		$edit_field[] = 'rechecktime';
	} else {
		// ����Ա �޸����е�����
		$edit_field = $all_field;
	}
} else { // ����ģʽ
	if ($uinfo["part_id"] == 2 || $uinfo["part_id"] == 3) { //�ͷ�����
		$edit_field = explode(' ', 'name sex age tel qq content disease_id media_from zhuanjia_num order_date memo depart is_local from_account');
	} else if ($uinfo["part_id"] == 4) { //��ҽ����
		$edit_field = explode(' ', 'name sex age tel qq content disease_id media_from zhuanjia_num order_date doctor status memo depart is_local from_account');
	} else {
		$edit_field = $all_field;
	}
}

// ������Ϊ���ѣ������Ǹ�������ݣ��������޸ģ�
if ($line["status"] == 1 && (strtotime(date("Y-m-d 0:0:0")) > strtotime(date("Y-m-d 0:0:0", $line["come_date"])))) {
	//$edit_field = array(); //ȫ�������޸�
}

// ÿ���ֶ��Ƿ��ܱ༭:
foreach ($all_field as $v) {
	$ce[$v] = in_array($v, $edit_field) ? '' : ' disabled="true"';
}

// 2013-06-30 10:42 fix
if ($line["media_from"] == "����ͷ�") {
	$line["media_from"] = "����";
} else if ($line["media_from"] == "�绰�ͷ�") {
	$line["media_from"] = "�绰";
}


$title = $mode == "edit" ? "�޸�" : "�����Һ�ƽ̨";

// page begin ----------------------------------------------------
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="res/datejs/picker.js" language="javascript"></script>
<style>
.dischk {width:6em; height:16px; line-height:16px; vertical-align:middle; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; padding:0; margin:0; }
</style>
<script language="javascript">
function check_data() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("�����벡��������"); oForm.name.focus(); return false;
	}
	if (oForm.tel.value != "" && get_num(oForm.tel.value) == '') {
		alert("����ȷ���벡�˵���ϵ�绰��"); oForm.tel.focus(); return false;
	}
	if (oForm.sex.value == '') {
		alert("�����롰�Ա𡱣�"); oForm.sex.focus(); return false;
	}
	if (oForm.tel.value == '') {
		alert("����д���ġ���ϵ�绰����"); oForm.tel.focus(); return false;
	}
	if (oForm.media_from.value == '') {
		alert("��ѡ��ý����Դ����"); oForm.media_from.focus(); return false;
	}
	if (oForm.order_date.value.length < 12) {
		alert("����ȷ��д��ԤԼʱ�䡱��"); oForm.order_date.focus(); return false;
	}
	<?php if (($username == "admin") || !in_array($uinfo["part_id"], array(4))) { ?>
	if (oForm.addtime.value == '') {
		alert("����Ա�˺ű���ѡ��Ǽ�ʱ�䣡"); oForm.addtime.focus(); return false;
	}
	<?php }?>
	return true;
}
function input(id, value) {
	if (byid(id).disabled != true) {
		byid(id).value = value;
	}
}

function input_date(id, value) {
	var cv = byid(id).value;
	var time = cv.split(" ")[1];

	if (byid(id).disabled != true) {
		byid(id).value = value+" "+(time ? time : '00:00:00');
	}
}

function input_time(id, time) {
	var s = byid(id).value;
	if (s == '') {
		alert("������д���ڣ�����дʱ�䣡");
		return;
	}
	var date = s.split(" ")[0];
	var datetime = date+" "+time;

	if (byid(id).disabled != true) {
		byid(id).value = datetime;
	}
}

// ��״̬Ϊ�ѵ�ʱ, ��ʾѡ��Ӵ�ҽ��:
function change_yisheng(v) {
	byid("yisheng").style.display = (v == 1 ? "inline" : "none");
}

// ��������ظ�:
function check_repeat(type, obj) {
	if (!byid("id") || (byid("id").value == '0' || byid("id").value == '')) {
		var value = obj.value;
		if (value != '') {
			var xm = new ajax();
			xm.connect("/http/check_repeat.php?type="+type+"&value="+value+"&r="+Math.random(), "GET", "", check_repeat_do);
		}
	}
}

function check_repeat_do(o) {
	var out = ajax_out(o);
	if (out["status"] == "ok") {
		if (out["tips"] != '') {
			alert(out["tips"]);
		}
	}
}

function show_hide_engine(o) {
	byid("engine_show").style.display = (o.value == "����" ? "inline" : "none");
}

function show_hide_area(o) {
	byid("area_from_box").style.display = (o.value == "2" ? "inline" : "none");
}

function show_hide_disease_add(o) {
	byid("disease_add_box").style.display = (o.value == "-1" ? "inline" : "none");
}

function set_color(o) {
	if (o.checked) {
		o.nextSibling.style.color = "blue";
	} else {
		o.nextSibling.style.color = "";
	}
}

</script>
</head>

<body>
<!-- ͷ�� begin -->
<div style="height:45px; line-height:45px; text-align:center; font-size:24px; color:red">
���������Һ�ϵͳ��*�������ѡ��ҽԺ����ң������޷��Һţ�
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
	  <td class="left" style="color:red">����ѡ��ҽԺ����ң�</td>
	  <td class="right"><select name="hospital_id" id="hospital_id" class="combo" onChange="location='?do=change&hospital_id='+this.value" style="width:200px;">
			<option value="" style="color:gray">--��ѡ��--</option>
			<?php echo list_option($hospital_list, 'id', 'name', $user_hospital_id); ?>
		</select></td>
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
		<td class="right"><input name="tel" id="tel" value="<?php echo $line["tel"]; ?>" class="input" style="width:200px" <?php echo $ce["tel"]; ?> onChange="check_repeat('tel', this)">  <span class="intro">�绰������ֻ�</span></td>
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
		<td class="left" valign="top">�������ͣ�</td>
		<td class="right">
			<select name="disease_id" class="combo" <?php echo $ce["disease_id"]; ?>>
				<option value="" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($show_disease, '_key_', '_value_', $line["disease_id"]); ?>
			</select>
		</td>
	</tr>

<?php if (count($depart_list) > 0) { ?>
	<tr>
		<td class="left">�������ң�</td>
		<td class="right">
			<select name="depart" class="combo" <?php echo $ce["depart"]; ?>>
				<option value="0" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($depart_list, 'id', 'name', $line["depart"]); ?>
			</select>
			<span class="intro">��ѡ��ҽԺ����</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td class="left">������Դ��</td>
		<td class="right">
			<select name="media_from" class="combo" <?php echo $ce["media_from"]; ?> onChange="show_hide_engine(this)">
				<option value="" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($media_from_array, '_value_', '_value_', $line["media_from"]); ?>
			</select>&nbsp;
			<span id="engine_show" style="display:<?php echo $line["media_from"] == "����" ? "" : "none"; ?>" <?php echo $ce["media_from"]; ?>>
				<select name="engine" class="combo">
					<option value="" style="color:gray">--����������Դ--</option>
					<?php echo list_option($engine_list, '_value_', '_value_', $line["engine"]); ?>
				</select>
				�ؼ��ʣ�<input name="engine_key" value="<?php echo $line["engine_key"]; ?>" class="input" size="15" <?php echo $ce["media_from"]; ?>>
				<select name="from_site" class="combo" <?php echo $ce["media_from"]; ?>>
					<option value="" style="color:gray">--��Դ��վ--</option>
					<?php echo list_option($sites_list, '_value_', '_value_', $line["from_site"]); ?>
				</select>
			</span>
			<span class="intro">��ѡ�������Դ</span>
		</td>
	</tr>
    
<!--
    <tr>
		<td class="left">ý����Դ��</td>
		<td class="right">
			<select name="mtly" class="combo" <?php echo $ce["media_from"]; ?>>
				<option value="" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($mtly_from_array, '_value_', '_value_', $line["media_from"]); ?>
			</select>&nbsp;
			<span class="intro">��ѡ��ý����Դ</span>
		</td>
	</tr>
-->    

	<tr>
		<td class="left">������Դ��</td>
		<td class="right">
			<select name="is_local" class="combo" <?php echo $ce["is_local"]; ?> onChange="show_hide_area(this)">
				<option value="0" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($is_local_array, '_key_', '_value_', ($op == "add" ? 1 : $line["is_local"])); ?>
			</select>&nbsp;
			<span id="area_from_box" style="display: <?php echo $op == "add" ? "none" : ($line["is_local"] == 2 ? "inline" : "none"); ?>">
				������<input name="area" id="area" value="<?php echo $line["area"]; ?>" class="input" size="14" <?php echo $ce["is_local"]; ?>>&nbsp;
				������õ�����<select id="quick_area" class="combo" <?php echo $ce["is_local"]; ?> onChange="byid('area').value=this.value;">
					<option value="" style="color:gray">-����-</option>
					<?php echo list_option($area_list, "_value_", "_value_"); ?>
				</select>
			</span>
			<span class="intro">������ԴĬ��Ϊ����(����������ʱ)</span>
		</td>
	</tr>

	<!-- <tr>
		<td class="left">����ͳ���ʻ���</td>
		<td class="right">
			<select name="from_account" class="combo" <?php echo $ce["from_account"]; ?>>
				<option value="0" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($account_list, '_key_', '_value_', ($op == "add" ? $account_first : $line["from_account"])); ?>
			</select>&nbsp;

			<span class="intro">��ѡ������ͳ���ʻ�</span>
		</td>
	</tr> -->

	<tr>
		<td class="left"><?php echo $uinfo["part_id"] == 4 ? "�����" : "ר�Һ�"; ?>��</td>
		<td class="right"><input name="zhuanjia_num" value="<?php echo $line["zhuanjia_num"]; ?>" class="input" size="30" style="width:200px" <?php echo $ce["zhuanjia_num"]; ?>>  <span class="intro"><?php echo $uinfo["part_id"] == 4 ? "�����" : "ԤԼר�Һ�"; ?></span></td>
	</tr>
	<tr>
		<td class="left" valign="top">ԤԼʱ�䣺</td>
		<td class="right"><input name="order_date" value="<?php echo $line["order_date"] ? @date('Y-m-d H:i:s', $line["order_date"]) : ''; ?>" class="input" style="width:150px" id="order_date" <?php echo $ce["order_date"]; ?>> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm:ss'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"> <span class="intro">���޸�<?php echo intval($line["order_date_changes"]); ?>��</span> <span class="intro">��ע�⣬�˴��ѵ�����ԤԼʱ�䲻�������ϸ���<?php echo date("j"); ?>�ţ����������޷��ύ��</span><?php if ($line["order_date_log"]) { ?><a href="javascript:void(0)" onClick="byid('order_date_log').style.display = (byid('order_date_log').style.display == 'none' ? 'block' : 'none'); ">�鿴�޸ļ�¼</a><?php } ?>
		<?php
		$show_days = array(
			"��" => $today = date("Y-m-d"), //����
			"��" => date("Y-m-d", strtotime("+1 day")), //����
			"��" => date("Y-m-d", strtotime("+2 days")), //����
			"�����" => date("Y-m-d", strtotime("+3 days")), //�����
			"����" => date("Y-m-d", strtotime("next Saturday")), //����
			"����" => date("Y-m-d", strtotime("next Sunday")), // ����
			"��һ" => date("Y-m-d", strtotime("next Monday")), // ��һ
			"һ�ܺ�" => date("Y-m-d", strtotime("+7 days")), // һ�ܺ�
			"���º�" => date("Y-m-d", strtotime("+15 days")), //����º�
		);
		if (!$ce["order_date"]) {
			echo '<div style="padding-top:6px;">����: ';
			foreach ($show_days as $name => $value) {
				echo '<a href="javascript:input_date(\'order_date\', \''.$value.'\')">['.$name.']</a>&nbsp;';
			}
			echo '<br>ʱ��: ';
			echo '<a href="javascript:input_time(\'order_date\',\'00:00:00\')">[ʱ�䲻��]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'09:00:00\')">[����9��]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'14:00:00\')">[����2��]</a>&nbsp;</div>';
		}
		?>
		<?php if ($line["order_date_log"]) { ?>
		<div id="order_date_log" style="display:none; padding-top:6px;"><b>ԤԼʱ���޸ļ�¼:</b> <br><?php echo strim($line["order_date_log"], '<br>'); ?></div>
		<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left" valign="top">��ע��</td>
		<td class="right"><textarea name="memo" style="width:60%; height:48px;vertical-align:middle;" class="input" <?php echo $ce["memo"]; ?>><?php echo $line["memo"]; ?></textarea> <span class="intro">������ע��Ϣ</span></td>
	</tr>
<?php if ($line["edit_log"] && $line["author"] == $realname) { ?>
	<tr>
		<td class="left" valign="top">�����޸ļ�¼��</td>
		<td class="right"><?php echo strim($line["edit_log"], '<br>'); ?></td>
	</tr>
<?php } ?>


<?php // ������Ŀ -------------  ?>
<?php
if (in_array($uinfo["part_id"], array(4,9,12)) && $line["status"] == 1) { ?>
	<tr>
		<td class="left">������Ŀ��</td>
		<td class="right">
<?php
$xiangmu_str = $db->query("select xiangmu from disease where id=".$line["disease_id"]." limit 1", 1, "xiangmu");
$xiangmu = explode(" ", trim($xiangmu_str));
$cur_xiangmu = explode(" ", trim($line["xiangmu"]));
$xiangmu = array_unique(array_merge($cur_xiangmu, $xiangmu));
foreach ($xiangmu as $k) {
	if ($k == '') continue;
	$checked = in_array($k, $cur_xiangmu) ? " checked" : "";
	$makered = $checked ? ' style="color:red"' : '';
	echo '<input type="checkbox" name="xiangmu[]" value="'.$k.'"'.$checked.' id="xiangmu_'.$k.'"'. $ce["xiangmu"].'><label for="xiangmu_'.$k.'"'.$makered.'>'.$k.'</label>&nbsp;&nbsp;';
}
?>
<?php if (!$ce["xiangmu"]) { ?>
		<input type="hidden" name="update_xiangmu" value="1">
		<span id="xiangmu_user"></span>
		<span id="xiangmu_add"><b>���ӣ�</b><input id="miangmu_my_add" class="input" size="10">&nbsp;<button onClick="xiangmu_user_add()" class="button">ȷ��</button></span>
<script language="JavaScript">
function xiangmu_user_add() {
	var name = byid("miangmu_my_add").value;
	if (name == '') {
		alert("�������¼ӵ����֣�"); return false;
	}
	var str = '<input type="checkbox" name="xiangmu[]" value="'+name+'" checked id="xiangmu_'+name+'"><label for="xiangmu_'+name+'">'+name+'</label>&nbsp;&nbsp;';
	byid("xiangmu_user").insertAdjacentHTML("beforeEnd", str);
	byid("miangmu_my_add").value = '';
}
</script>
<?php } ?>

		</td>
	</tr>

	<!-- ���Ʒ��� -->
	<tr>
		<td class="left">���Ʒ��ã�</td>
		<td class="right">
			<input name="fee" id="fee" value="<?php echo $line["fee"] > 0 ? $line["fee"] : ''; ?>" class="input" <?php echo $ce["fee"]; ?> size="20">
			<span class="intro">���Ʒ���</span>
		</td>
	</tr>
<?php } ?>


<?php
if (in_array($uinfo["part_id"], array(4,9,12)) && $line["status"] == 1) { ?>
	<tr>
		<td class="left">����ʱ�䣺</td>
		<td class="right">
			<input name="rechecktime" id="rechecktime" value="<?php if ($line["rechecktime"]>0) echo date("Y-m-d", $line["rechecktime"]); ?>" class="input" <?php echo $ce["rechecktime"]; ?> size="20">
			<?php if ($line["rechecktime"]) echo intval(($line["rechecktime"] - $line["order_date"]) / 24/3600)."�� "; ?>
			 <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'rechecktime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
			<span class="intro">����д����(�� 10 �����ԤԼʱ������)�����ʱ��(�� 2013-10-1)</span>
		</td>
	</tr>
<?php } ?>

</table>


<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $mode; ?>">
<input type="hidden" name="go" value="<?php echo $_GET["go"]; ?>">

<div class="button_line"><input type="submit" class="submit" value="�����Һ�"></div>
</form>
</body>
</html>