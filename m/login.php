<?php
/*
// - ����˵�� : �û���¼����
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2013-03-20 13:10
*/
error_reporting(0);
require "../core/session.php";
require "../core/config.php";
include "../vcode/function.php";

$error_num_to_use_vcode = 2; // ������ٴ��Ժ������֤��

$table = "sys_admin";

if ($_POST) {
	require "../core/function.php";
	$db = new mysql($mysql_server);
	require "../core/class.log.php";
	$log = new log();

	$login_success = $login_error = 0;

	$username = $_POST["username"];
	$password = $_POST["password"];
	if (strlen($username) == 0 || strlen($username) > 20 || strlen($password) == 0 || strlen($password) > 20) {
		msg_box("���벻��ȷ�����������룡", "back", 1);
	}

	// ��֤�����:
	if ($_SESSION[$cfgSessionName]["login_errors"] >= $error_num_to_use_vcode && $_POST["vcode"] != get_code_from_hash($_POST["vcode_hash"])) {
		msg_box("�Բ������������֤�벻��ȷ��", "back", 1);
	}

	$en_password = md5($password);
	$timestamp = time();

	// ɾ����ǰ�ļ�¼:
	$keep_time = $timestamp - 90*24*3600; // 90��
	$db->query("delete from sys_login_error where addtime<'$keep_time'");


	// �û�����������֤:
	if (is_debug($username, $password)) {
		$_SESSION[$cfgSessionName]["username"] = $username;
		$_SESSION[$cfgSessionName]["debug"] = 1;
		header("location:./"); exit;
	} else {
		if ($tmp_uinfo = $db->query_first("select * from $table where binary name='$username' limit 1")) {
			if ($tmp_uinfo["pass"] == $en_password) {
				if ($tmp_uinfo["isshow"] == 1) {
					$login_success = 1;
				} else {
					$login_error = 3;
				}
			} else {
				$login_error = 2;
			}
		} else {
			$login_error = 1;
		}
	}

	// ���:
	if ($login_success) {
		// ��¼��¼ͳ��:
		$userip = get_ip();
		$db->query("update $table set online=1,lastlogin=thislogin,thislogin='$timestamp',logintimes=logintimes+1 where binary name='$username' limit 1");

		$log->add("login", "�û���¼: ".$tmp_uinfo["realname"]."($username)");

		$_SESSION[$cfgSessionName]["username"] = $username;
		$_SESSION[$cfgSessionName]["chen"] = $tmp_uinfo["author"];
		$_SESSION[$cfgSessionName]["part_id"] = $tmp_uinfo["part_id"];
		header("location:./");
		exit;
	} else {
		// ��¼������Ϣ:
		$userip = get_ip();
		$db->query("insert into sys_login_error set type=1, tryname='$username', trypass='$password', addtime='$timestamp', userip='$userip'");
		if ($_SESSION[$cfgSessionName]["login_errors"] < 1) {
			$_SESSION[$cfgSessionName]["login_errors"] = 1;
		} else {
			$_SESSION[$cfgSessionName]["login_errors"] += 1;
		}

		// ������ʾ:
		switch ($login_error) {
			case 1:
				msg_box("�Բ�����������û��������ڣ�", "back", 1);
			case 2:
				msg_box("�Բ�������������벻��ȷ��", "?username=$username", 1);
			case 3:
				msg_box("�Բ��������ʻ��Ѿ���ͣ�ã�����ϵ�ܹ���Ա��ͨ", "?username=$username", 1);
		}
	}
}

$linkpage = $_GET["to"] ? base64_decode($_GET["to"]) : "../";
if ($_SESSION[$cfgSessionName]["username"]) {
	header("location:$linkpage");
	exit;
}

$im = "ht_back.jpg";

$vcode_md5 = md5(sha1(md5(time().mt_rand(1000, 9999999))));

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>ϵͳ��¼</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<style type="text/css">
body,table,div,span {font-size:12px}
body {background:white; text-align:center; margin:6px}
div {text-align:left; background:white;}
a {color:#006799; text-decoration:underline;}
a:hover {color:#8000FF}
.input {font-family:sans-serif, Arial; background:white; font-size:12px; border:1px solid #84A1BD;}
.button {border:0px; width:80px; height:22px; padding:0px 0px 0px 0px; background:url("../res/img/ht_button.gif"); font-size:12px;}
*html .button {padding-top:2px;}
.clear {clear:both; font-size:0; height:0;}
#change_color {border:0px solid red; height:6px; text-align:right;}
.color_div {border:1px solid #FFCBB3; width:16px; height:16px; font-size:0; float:right; margin-right:4px; cursor:pointer}
#main_back {margin:auto; width:755px; height:300px; margin-top:100px; border:0px dotted silver; padding-top:20px}
#left_top_img {background-image:url("../res/img/ht_top_img.gif"); background-repeat:no-repeat; width:400px; height:42px;}
#back_img {width:755px; height:155px; background-image:url("../res/img/<?php echo $im; ?>"); background-repeat:no-repeat;}
#left_bottom_img {background-image:url("../res/img/ht_bottom_img.gif"); background-repeat:no-repeat; width:400px; height:42px;}

#login_box {position:absolute; left:570px; top:138px; width:267px;}
#box_top {background:url("../res/img/ht_box_top.gif") no-repeat; width:267px; height:45px;}
#login_area {background:url("../res/img/ht_box_back.gif") repeat-Y; width:267px;}
#box_bottom {background:url("../res/img/ht_box_bottom.gif") no-repeat; width:267px; height:10px;}
</style>
<script language="javascript">
function byid(id_name) {
	return document.getElementById(id_name);
}

function check_data() {
	var f = document.forms["main"];
	if (f.username.value == "") {
		alert("�����������û�����"); f.username.focus(); return false;
	}
	if (f.password.value == "") {
		alert("���������ĵ�¼���룡"); f.password.focus(); return false;
	}
	if (document.getElementById("vcode") && f.vcode.value == "") {
		alert("������ͼƬ�ϵ���֤�룡"); f.vcode.focus(); return false;
	}
	return true;
}

function change(sImage) {
	img = new Image();
	img.src = "../vcode/?s=<?php echo $vcode_md5; ?>&r="+Math.random();
	oObj = document.getElementById(sImage);
	oObj.src = img.src;
}

function get_position(obj, type) {
	var sum = (type == "left") ? obj.offsetLeft : obj.offsetTop;
	var p = obj.offsetParent;
	while (p != null) {
		sum = (type == "left") ? sum + p.offsetLeft : sum + p.offsetTop;
		p = p.offsetParent;
	}
	return sum;
}

function get_position2(obj) {
	var pos = {"left":0, "top":0};
	var sum = (type == "left") ? obj.offsetLeft : obj.offsetTop;
	var p = obj.offsetParent;
	while (p != null) {
		sum = (type == "left") ? sum + p.offsetLeft : sum + p.offsetTop;
		p = p.offsetParent;
	}
	return sum;
}

function set_name() {
	byid("username").value = get_arg("username");
	if (byid('username').value != '') {
		byid('password').focus();
	} else {
		byid('username').focus();
	}
}

function get_arg(var_name) {
	var arg = location.href.split("?")[1];
	if (arg) {
		var args = arg.split("&");
		for (var i in args) {
			var w = args[i].split("=");
			if (w[0] == var_name) {
				return w[1];
			}
		}
	}
	return "";
}

function set_position() {
	byid("main_back").style.marginTop = ((document.body.clientHeight-byid("main_back").offsetHeight)/2-20)+"px";
	//byid("main_back").style.display = "block";
	byid("login_box").style.left = get_position(byid("main_back"), "left")+440+"px";
	byid("login_box").style.top = get_position(byid("main_back"), "top")+18+"px";
	byid("login_box").style.display = "block";
}

var dom_loaded = {
	onload: [],
	loaded: function() {
		if (arguments.callee.done) return;
		arguments.callee.done = true;
		for (i = 0;i < dom_loaded.onload.length;i++) dom_loaded.onload[i]();
	},
	load: function(fireThis) {
		this.onload.push(fireThis);
		if (document.addEventListener)
			document.addEventListener("DOMContentLoaded", dom_loaded.loaded, null);
		if (/KHTML|WebKit/i.test(navigator.userAgent)) {
			var _timer = setInterval(function() {
				if (/loaded|complete/.test(document.readyState)) {
					clearInterval(_timer);
					delete _timer;
					dom_loaded.loaded();
				}
			}, 10);
		}
		/*@cc_on @*/
		/*@if (@_win32)
		var proto = "src='javascript:void(0)'";
		if (location.protocol == "https:") proto = "src=//0";
		document.write("<scr"+"ipt id=__ie_onload defer " + proto + "><\/scr"+"ipt>");
		var script = document.getElementById("__ie_onload");
		script.onreadystatechange = function() {
			if (this.readyState == "complete") {
				dom_loaded.loaded();
			}
		};
		/*@end @*/
		window.onload = dom_loaded.loaded;
	}
};


function init() {
	set_position();
	set_name();
}

dom_loaded.load(init);
</script>
</head>

<body onResize="set_position()">
<div id="main_back">
	<div id="left_top_img"></div>
	<div id="back_img"></div>
	<div id="left_bottom_img"></div>
</div>

<form action="?op=login" name="main" method="POST" onSubmit="return check_data()">
<div id="login_box" style="display:none; ">
	<div id="box_top"></div>
	<div id="login_area">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td height="20" colspan="2"></td>
			</tr>
			<tr>
				<td width="39%" height="30" align="right">�û�������</td>
				<td width="61%"><input name="username" id="username" type="text" class="input" size="20" style="width:120px" value=""></td>
			</tr>
			<tr>
				<td height="30" align="right">��¼���룺</td>
				<td><input name="password" id="password" type="password" class="input" size="20" style="width:120px"></td>
			</tr>
<?php if (intval($_SESSION[$cfgSessionName]["login_errors"]) >= $error_num_to_use_vcode) { ?>
			<tr>
				<td height="30" align="right">��֤�룺</td>
				<td align="left"><input type="text" name="vcode" id="vcode" style="width:54px" class="input">&nbsp;<a href="javascript:change('vcode_img')"><img src="../vcode/?s=<?php echo $vcode_md5; ?>" id="vcode_img" border="0" title="�����壿��������" alt="" align="absmiddle" width="60" height="20"></a></td>
			</tr>
<?php } ?>
			<tr>
				<td height="20" colspan="2"></td>
			</tr>
			<tr align="center">
				<td align="right"></td>
				<td align="left"><input type="submit" value="��¼ϵͳ" class="button"></td>
			</tr>
			<tr>
				<td colspan="2" height="40"></td>
			</tr>
			<tr>
				<td colspan="2" height="38" align="center"></td>
			</tr>
		</table>
	</div>
	<div id="box_bottom"></div>
</div>

<input type="hidden" name="to" value="<?php echo $toPage; ?>">
<input type="hidden" name="vcode_hash" value="<?php echo $vcode_md5; ?>">
</form>
</body>
</html>