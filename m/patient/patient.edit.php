<?php
/*
// - ����˵�� : �������޸Ĳ�������
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2014-04-03 11:57
*/
$mode = $op;

function request_by_other($remote_server, $post_string)
{
	global $tel_Account;
	$context = array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded' .
						'\r\n'.'User-Agent : Jimmy\'s POST Example beta' .
						'\r\n'.'Content-length:' . strlen($post_string) + 8,
			'content' => 'mypost=' . $post_string)
		);
	$stream_context = stream_context_create($context);
	$data = file_get_contents($remote_server, false, $stream_context);
	return $data;
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
	if ($po["disease_id"] == -1) {
		$d_name = $po["disease_add"];
		$d_id = 0;
		if ($d_name != '') {
			$d_id = $db->query("insert into disease set hospital_id='$hid', name='$d_name', addtime='$time', author='$username'");
		}
		$po["disease_id"] = $d_id ? $d_id : 0;
	}


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
	
	if (($username == "admin") || !in_array($uinfo["part_id"], array(4))) { 
		if (isset($po["addtime"])) $r["addtime"] = strtotime($po["addtime"]);
	}
	// �޸�ʱ��:
	if (isset($po["order_date"])) {
		$order_date_post = @strtotime($po["order_date"]);
		if ($mode == "add") {

			// ����޸ģ���ʱ�䲻�ܱ��޸�Ϊ��ǰʱ���һ����֮ǰ(2011-01-15)
			if ($order_date_post < strtotime("-1 month")) {
				exit_html("ԤԼʱ�䲻����һ����֮ǰ�������ȼ�����ĵ���ʱ���Ƿ����󣡣�  �뷵��������д��");
			}

			$r["order_date"] = $order_date_post; //����
		} else {
			//�ж�ʱ���Ƿ����޸�
			if ($order_date_post != $oldline["order_date"]) {

				// ����޸ģ���ʱ�䲻�ܱ��޸�Ϊ��ǰʱ���һ����֮ǰ(2011-01-15)
				if ($order_date_post < strtotime("-1 month")) {
					exit_html("ԤԼʱ�䲻�ܱ��޸ĵ�һ����֮ǰ�������ȼ�����ĵ���ʱ���Ƿ����󣡣�  �뷵��������д��");
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
		if ($op == "add" && isset($tel_Account)) {
			//$user_hospital_id;
			$get_hospital = $db->query("select * from mtly where hospital='$user_hospital_id' limit 0,1", "id", "name");
			foreach($get_hospital as $tel_msg)
			{
				$get_msg=$tel_msg;
			}
			if(preg_match("/1[3458]{1}\d{9}$/",$tel)){
				$get_msg=$po["name"]."���ã�\r\n".$get_msg;
				//{ר�Һ�} zhuanjia_num
				$get_msg=str_replace("{ר�Һ�}",$po["zhuanjia_num"],$get_msg);
				$msg=urlencode ($get_msg);
				$post_string = "aaa=go&func=sendsms&username={$tel_Account[0]}&password={$tel_Account[1]}&mobiles={$tel}&message={$msg}";
				$yz=request_by_other('http://sms.c8686.com/Api/BayouSmsApiEx.aspx',$post_string);
				
				/*$f=fopen("tel_Verification.txt","w");
				fwrite($f,$yz."\r\n".$get_msg);
				fclose($f);*/
			}
		}
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
		$sql = "insert into $table set $sqldata";
	}

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
		msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);
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
	array("id"=>0, "name"=>'CHO DOI'),
	array("id"=>1, "name"=>'DA DEN'),
	array("id"=>2, "name"=>'CHUA DEN'),
);

$xiaofei_array = array(
	array("id"=>0, "name"=>'δ����'),
	array("id"=>1, "name"=>'������'),
);


// ȡǰ30������:
$show_disease = array();
foreach ($disease_list as $k => $v) {
	$show_disease[$k] = $v;
	if (count($show_disease) >= 100) {
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


// 2010-05-18
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

// 2010-10-23
$is_local_array = array(1 => "TPHCM", 2 => "TINH");


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


$title = $mode == "edit" ? "�޸Ĳ�������" : "�����µĲ�������";

// page begin ----------------------------------------------------
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<script src="../../res/datejs/picker.js" language="javascript"></script>
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
<div class="headers">
	<div class="headers_title"><span class="tips"><?php echo $title; ?></span></div>
	<div class="header_center"><!-- <button onclick="if (check_data()) document.forms['mainform'].submit();" class="buttonb">�ύ����</button> --></div>
	<div class="headers_oprate"><button onClick="history.back()" class="button">TRO LAI</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div class="description">
	<div class="d_title">GOI Y��</div>
	<div class="d_item">1.HO TEN CHAC CHAN PHAI DIEN����2.DIEN THOAI DIEN DAY DU����3.CHI TIET CUA BENH NHAN PHAI GHI CHU RO RANG��</div>
</div>

<div class="space"></div>
<form name="mainform" method="POST" onSubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">THONG TIN CO BAN CUA BN</td>
	</tr>
	<tr>
		<td class="left">HO TEN��</td>
		<td class="right"><input name="name" id="name" value="<?php echo $line["name"]; ?>" class="input" style="width:200px" <?php echo $ce["name"]; ?> onChange="check_repeat('name', this)"> <span class="intro">* CHAC CHAN PHAI DIEN</span></td>
	</tr>
	<tr>
		<td class="left">GIOI TINH��</td>
		<td class="right"><input name="sex" id="sex" value="<?php echo $line["sex"]; ?>" class="input" style="width:80px" <?php echo $ce["sex"]; ?>> <a href="javascript:input('sex', '��')">[NAM]</a> <a href="javascript:input('sex', 'Ů')">[NU]</a> <span class="intro">CHAC CHAN PHAI DIEN</span></td>
	</tr>
	<tr>
		<td class="left">TUOI��</td>
		<td class="right"><input name="age" id="age" value="<?php echo $line["age"]; ?>" class="input" style="width:80px" <?php echo $ce["age"]; ?>> <span class="intro">DIEN TUOI KHONG DIEN NAM SINH</span></td>
	</tr>
<?php if($op == "add" || ($op == "edit" && $line["author"] == $realname)||$username == "admin"||$username == "wenjianshan"||$username == "zuoqiuying") { ?>
	<tr>
		<td class="left">DIEN THOAI��</td>
		<td class="right"><input name="tel" id="tel" value="<?php echo $line["tel"]; ?>" class="input" style="width:200px" <?php echo $ce["tel"]; ?> onChange="check_repeat('tel', this)">  <span class="intro">CHAC CHAN PHAI DIEN</span></td>
	</tr>
<?php } ?>
<!--	<tr>
		<td class="left">QQ��</td>
		<td class="right"><input name="qq" value="<?php echo $line["qq"]; ?>" class="input" style="width:140px" <?php echo $ce["qq"]; ?>>  <span class="intro">����QQ����</span></td>
	</tr>
-->	
<?php if ($op == "add" || ($op == "edit" && $line["author"] == $realname)||$username == "admin"||$username == "wenjianshan"||$username == "zuoqiuying") { ?>
<tr>
		<td class="left" valign="top">NOI DUNG TU VAN��</td>
		<td class="right"><textarea name="content" style="width:60%; height:72px;vertical-align:middle;" <?php echo $ce["content"]; ?> class="input"><?php echo $line["content"]; ?></textarea> <span class="intro">NOI DUNG CHAT</span></td>
	</tr>
<?php } ?>
	<tr>
		<td class="left" valign="top">LOAI BENH��</td>
		<td class="right">
			<select name="disease_id" class="combo" <?php echo $ce["disease_id"]; ?>>
				<option value="" style="color:gray">--LUA CHON--</option>
				<?php echo list_option($show_disease, '_key_', '_value_', $line["disease_id"]); ?>
			</select>
		</td>
	</tr>

<?php if (count($depart_list) > 0) { ?>
	<tr>
		<td class="left">KHOA��</td>
		<td class="right">
			<select name="depart" class="combo" <?php echo $ce["depart"]; ?>>
				<option value="0" style="color:gray">--LUA CHON--</option>
				<?php echo list_option($depart_list, 'id', 'name', $line["depart"]); ?>
			</select>
			<span class="intro">KHOA BENH</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td class="left">NGUON DEN��</td>
		<td class="right">
			<select name="media_from" class="combo" <?php echo $ce["media_from"]; ?> onChange="show_hide_engine(this)">
				<option value="" style="color:gray">--LUA CHON--</option>
				<?php echo list_option($media_from_array, '_value_', '_value_', $line["media_from"]); ?>
			</select>&nbsp;
			<span id="engine_show" style="display:<?php echo $line["media_from"] == "����" ? "" : "none"; ?>" <?php echo $ce["media_from"]; ?>>
				<select name="engine" class="combo">
					<option value="" style="color:gray">--NGUON HEN--</option>
					<?php echo list_option($engine_list, '_value_', '_value_', $line["engine"]); ?>
				</select>
				TU KHOA��<input name="engine_key" value="<?php echo $line["engine_key"]; ?>" class="input" size="15" <?php echo $ce["media_from"]; ?>>
				<select name="from_site" class="combo" <?php echo $ce["media_from"]; ?>>
					<option value="" style="color:gray">--MANG--</option>
					<?php echo list_option($sites_list, '_value_', '_value_', $line["from_site"]); ?>
				</select>
			</span>
			<span class="intro">LUA CHON</span>
		</td>
	</tr>   

	<tr>
		<td class="left">KHU VUC��</td>
		<td class="right">
			<select name="is_local" class="combo" <?php echo $ce["is_local"]; ?> onChange="show_hide_area(this)">
				<option value="0" style="color:gray">--LUA CHON--</option>
				<?php echo list_option($is_local_array, '_key_', '_value_', ($op == "add" ? 1 : $line["is_local"])); ?>
			</select>&nbsp;
			<span id="area_from_box" style="display: <?php echo $op == "add" ? "none" : ($line["is_local"] == 2 ? "inline" : "none"); ?>">
				KHU VUC��<input name="area" id="area" value="<?php echo $line["area"]; ?>" class="input" size="14" <?php echo $ce["is_local"]; ?>>&nbsp;
				��DIEN NHANH KHU VUC��<select id="quick_area" class="combo" <?php echo $ce["is_local"]; ?> onChange="byid('area').value=this.value;">
					<option value="" style="color:gray">-KHU VUC-</option>
					<?php echo list_option($area_list, "_value_", "_value_"); ?>
				</select>
			</span>
			<span class="intro">NGUON BN MAC DINH LA BAN DIA</span>
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
		<td class="left" valign="top">THOI GIAN HEN��</td>
		<td class="right"><input name="order_date" value="<?php echo $line["order_date"] ? @date('Y-m-d H:i:s', $line["order_date"]) : ''; ?>" class="input" style="width:150px" id="order_date" <?php echo $ce["order_date"]; ?>> <img src="../../res/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm:ss'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"> <span class="intro">���޸�<?php echo intval($line["order_date_changes"]); ?>��</span> <span class="intro">��ע�⣬�˴��ѵ�����ԤԼʱ�䲻�������ϸ���<?php echo date("j"); ?>�ţ����������޷��ύ��</span><?php if ($line["order_date_log"]) { ?><a href="javascript:void(0)" onClick="byid('order_date_log').style.display = (byid('order_date_log').style.display == 'none' ? 'block' : 'none'); ">�鿴�޸ļ�¼</a><?php } ?>
		<?php
		$show_days = array(
			"HOM NAY" => $today = date("Y-m-d"), //����
			"NGAY MAI" => date("Y-m-d", strtotime("+1 day")), //����
			"NGAY MOT" => date("Y-m-d", strtotime("+2 days")), //����
			"NGAY KIA" => date("Y-m-d", strtotime("+3 days")), //�����
			"THU 7" => date("Y-m-d", strtotime("next Saturday")), //����
			"CN" => date("Y-m-d", strtotime("next Sunday")), // ����
			"THU 2" => date("Y-m-d", strtotime("next Monday")), // ��һ
			"1 TUAN SAU" => date("Y-m-d", strtotime("+7 days")), // һ�ܺ�
			"NUA THANG SAU" => date("Y-m-d", strtotime("+15 days")), //����º�
		);
		if (!$ce["order_date"]) {
			echo '<div style="padding-top:6px;">NGAY: ';
			foreach ($show_days as $name => $value) {
				echo '<a href="javascript:input_date(\'order_date\', \''.$value.'\')">['.$name.']</a>&nbsp;';
			}
			echo '<br>THOI GIAN';
			echo '<a href="javascript:input_time(\'order_date\',\'00:00:00\')">[KHONG XAC DINH]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'09:00:00\')">[SANG 9H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'10:00:00\')">[SANG 10H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'11:00:00\')">[TRUA 11H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'12:00:00\')">[TRUA 12H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'13:00:00\')">[TRUA 13H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'14:00:00\')">[TRUA 14H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'15:00:00\')">[CHIEU 15H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'16:00:00\')">[CHIEU 16H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'17:00:00\')">[CHIEU 17H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'18:00:00\')">[CHIEU 18H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'19:00:00\')">[TOI 19H]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'20:00:00\')">[TOI 20H]</a>&nbsp;</div>';
		}
		?>
		<?php if ($line["order_date_log"]) { ?>
		<div id="order_date_log" style="display:none; padding-top:6px;"><b>LICH SU THAY DOI:</b> <br><?php echo strim($line["order_date_log"], '<br>'); ?></div>
		<?php } ?>
		</td>
	</tr>
	
	
	
	
<?php if ($op == "add" || ($op == "edit" && $line["author"] == $realname)||$username == "admin"||$username == "wenjianshan"||$username == "zuoqiuying") { ?>
	<tr>
		<td class="left" valign="top">GHI CHU��</td>
		<td class="right"><textarea name="memo" style="width:60%; height:48px;vertical-align:middle;" class="input" <?php echo $ce["memo"]; ?>><?php echo $line["memo"]; ?></textarea> <span class="intro">GHI CHU KHAC</span></td>
	</tr>
<?php } ?>
	
	
<?php if ($line["edit_log"] && $line["author"] == $realname) { ?>
	<tr>
		<td class="left" valign="top">LICH SU THAY DOI THONG TIN��</td>
		<td class="right"><?php echo strim($line["edit_log"], '<br>'); ?></td>
	</tr>
<?php } ?>


<?php // ������Ŀ -------------  ?>
<?php
if (in_array($uinfo["part_id"], array(4,9,12)) && $line["status"] == 1) { ?>
	<tr>
		<td class="left">MUC DIEU TRI��</td>
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
		<span id="xiangmu_add"><b>THEM��</b><input id="miangmu_my_add" class="input" size="10">&nbsp;<button onClick="xiangmu_user_add()" class="button">NHAP THONG TIN</button></span>
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
<!--	<tr>
		<td class="left">���Ʒ��ã�</td>
		<td class="right">
			<input name="fee" id="fee" value="<?php echo $line["fee"] > 0 ? $line["fee"] : ''; ?>" class="input" <?php echo $ce["fee"]; ?> size="20">
			<span class="intro">���Ʒ���</span>
		</td>
	</tr>-->
<?php } ?>


<?php // ���� -------------  ?>
<?php
if (in_array($uinfo["part_id"], array(4,9,12)) && $line["status"] == 1) { ?>
<!--	<tr>
		<td class="left">����ʱ�䣺</td>
		<td class="right">
			<input name="rechecktime" id="rechecktime" value="<?php if ($line["rechecktime"]>0) echo date("Y-m-d", $line["rechecktime"]); ?>" class="input" <?php echo $ce["rechecktime"]; ?> size="20">
			<?php if ($line["rechecktime"]) echo intval(($line["rechecktime"] - $line["order_date"]) / 24/3600)."�� "; ?>
			 <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'rechecktime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
			<span class="intro">����д����(�� 10 �����ԤԼʱ������)�����ʱ��(�� 2013-10-1)</span>
		</td>
	</tr>-->
<?php } ?>

</table>


<?php if (in_array($uinfo["part_id"], array(1,4,9)) || ($username == "admin") || $debug_mode) { ?>
<div class="space"></div>
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">DEN KHAM CHUA </td>
	</tr>
    <?php if (($username == "admin") || !in_array($uinfo["part_id"], array(4))) { ?>
    <tr>
	  <td class="left">�޸ĵǼ�ʱ�䣺</td>
	  <td class="right"><input name="addtime" id="rechecktime" value="<?php if ($line["addtime"]>0) echo date("Y-m-d H:i:s", $line["addtime"]); ?>" class="input" <?php echo $ce["rechecktime"]; ?> size="20" onClick="picker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"></td>
    </tr>
    <?php }?>
	<tr>
		<td class="left">TRANG THAI��</td>
		<td class="right">
        <?php $me_status_array = array ( 0 => array ( 'id' => '0','name' => 'CHO DOI'),1 => array ( 'id' => '1','name' => 'DA DEN'),2 => array ( 'id' => '2','name' => 'CHUA DEN'),3 => array ( 'id' => '3','name' => 'KHONG XAC DINH'));?>
			<select name="status" class="combo" <?php echo $ce["status"]; ?>> <!-- onchange="change_yisheng(this.value)" -->
				<option value="0" style="color:gray">--LUA CHON--</option>
				<?php echo list_option($me_status_array, 'id', 'name', ($mode == "add" && $uinfo["part_id"] == 4) ? 1 : $line["status"]); ?>
			</select> 
		</td>
	</tr>
	<tr id="yisheng"> <!--  style="display:<?php echo ($line["status"] == 1 ? "inline" : "none"); ?>;" -->
		<td class="left">BAC SI TIEP BENH��</td>
		<td class="right">
			<select name="doctor" class="combo" <?php echo $ce["doctor"]; ?>>
				<option value="" style="color:gray">--LUA CHON--</option>
				<?php echo list_option($doctor_list, 'name', 'name', $line["doctor"]); ?>
			</select>&nbsp;<span class="intro">KHI BENH NHAN TOI KHAM MOI CHON</span>
		</td>
	</tr>
</table>
<?php } ?>


<?php if (!in_array($uinfo["part_id"], array(1,4,9)) and ($username != "admin") || $debug_mode) { ?>

<div class="space"></div>
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">DEN KHAM CHUA</td>
	</tr>
	<tr>
		<td class="left">TINH TRANG��</td>
		<td class="right">
        <?php $me_status_array = array (0 => array ( 'id' => '3','name' => 'HEN KHONG XAC DINH'),1 => array ( 'id' => '0','name' => 'CHO DOI'));?>
			<select name="status" class="combo"> <!-- onchange="change_yisheng(this.value)" -->
				<option value="0" style="color:gray">--LUA CHON--</option>
				<?php echo list_option($me_status_array, 'id', 'name', ($mode == "add" && $uinfo["part_id"] == 4) ? 1 : $line["status"]); ?>
			</select>
		</td>
	</tr>
  </table>
<?php } ?>


<?php if ($mode == "edit" && $line["status"] == 1 && ($debug_mode || in_array($uinfo["part_id"], array(1,4,9))) ) { ?>
<!-- �Ӵ���¼ -->
<div class="space"></div>
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">�Ӵ���¼</td>
	</tr>
	<tr>
		<td class="left" valign="top">�Ӵ����ݣ�</td>
		<td class="right"><textarea name="jiedai_content" style="width:60%; height:48px;vertical-align:middle;" class="input"><?php echo $line["jiedai_content"]; ?></textarea> <span class="intro">����д�Ӵ�����</span></td>
	</tr>
</table>
<?php } ?>



<?php if ($mode == "edit" && (in_array("huifang", $edit_field) || $line["author"] == $username)) { ?>
<?php
	$huifang = trim($line["huifang"]);
?>
<div class="space"></div>
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">�绰�طü�¼</td>
	</tr>
	<tr>
		<td class="left" valign="top">���λطã�</td>
		<td class="right"><?php echo $line["huifang"] ? text_show($line["huifang"]) : "<font color=gray>(���޼�¼)</font>"; ?></td>
	</tr>
	<tr>
		<td class="left" valign="top">���λطã�</td>
		<td class="right"><textarea name="huifang" style="width:60%; height:48px;vertical-align:middle;" class="input"<?php echo $ce["huifang"]; ?>></textarea> <span class="intro">�طü�¼</span></td>
	</tr>
</table>
<?php } ?>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $mode; ?>">
<input type="hidden" name="go" value="<?php echo $_GET["go"]; ?>">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>