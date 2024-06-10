<?php
// --------------------------------------------------------
// - ����˵�� : ���� ���ݶԱ� ����
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2010-11-04 15:09
// --------------------------------------------------------
require "../../core/core.php";
$table = "count_web";

// ���пɹ�����Ŀ:
if ($debug_mode || in_array($uinfo["part_id"], array(9))) {
	$types = $db->query("select id,name from count_type where type='web' order by sort desc, id asc", "id", "name");
} else {
	$hids = implode(",", $hospital_ids);
	$types = $db->query("select id,name from count_type where type='web' and hid in ($hids) order by sort desc, id asc", "id", "name");
}
if (count($types) == 0) {
	exit("û�п��Թ�������Ŀ");
}

$cur_type = $_SESSION["count_type_id_web"];
if (!$cur_type) {
	$type_ids = array_keys($types);
	$cur_type = $_SESSION["count_type_id_web"] = $type_ids[0];
}

// �����Ĵ���:
if ($op = $_REQUEST["op"]) {
	if ($op == "change_type") {
		$cur_type = $_SESSION["count_type_id_web"] = intval($_GET["type_id"]);
	}
}

$type_detail = $db->query("select * from count_type where id=$cur_type limit 1", 1);
$kefu_list = $type_detail["kefu"] ? explode(",", $type_detail["kefu"]) : array();


// ��ʼֵΪ����:
if ($_GET["month"] == '') {
	$_GET["month"] = date("Y-m", mktime(0,0,0,date("m"), 1));
}

$m = strtotime($_GET["month"]."-1 0:0:0");
$m_end = date("d", strtotime("+1 month", $m) - 1);
$week_array = array(
	1 => array(date("Y-m-1", $m), date("Y-m-7", $m)),
	2 => array(date("Y-m-8", $m), date("Y-m-14", $m)),
	3 => array(date("Y-m-15", $m), date("Y-m-21", $m)),
	4 => array(date("Y-m-22", $m), date("Y-m-".$m_end, $m)),
);




// ��������:
if ($cur_type && $_GET["month"]) {

	$all = array();

	// ��ѯÿ������:
	foreach ($week_array as $wi => $w) {

		// ʱ���:
		$btime = strtotime($w[0]." 0:0:0");
		$etime = strtotime($w[1]." 23:59:59");

		$b = date("Ymd", $btime);
		$e = date("Ymd", $etime);

		//��ѯ��ҽԺ��������:
		$tmp_list = $db->query("select * from $table where type_id=$cur_type and date>=$b and date<=$e order by kefu asc,date asc");

		// �������:
		$list = $dt_count = array();
		foreach ($tmp_list as $v) {
			$dt = $v["kefu"];
			$dt_count[$dt] += 1;
			foreach ($v as $a => $b) {
				if ($b && is_numeric($b)) {
					$list[$dt][$a] = floatval($list[$dt][$a]) + $b;
				}
			}
		}

		// ��������:
		foreach ($list as $k => $v) {
			// ��ѯԤԼ��:
			$list[$k]["per_1"] = @round($v["talk"] / $v["click"] * 100, 2);
			// ԤԼ������:
			$list[$k]["per_2"] = @round($v["come"] / $v["orders"] * 100, 2);
			// ��ѯ������:
			$list[$k]["per_3"] = @round($v["come"] / $v["click"] * 100, 2);
			// ��Ч��ѯ��:
			$list[$k]["per_4"] = @round($v["ok_click"] / $v["click"] * 100, 2);
			// ��ЧԤԼ��:
			$list[$k]["per_5"] = @round($v["talk"] / $v["ok_click"] * 100, 2);
		}

		// ����ͳ������:
		$cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other per_1 per_2 per_3 per_4 per_5");
		// ����:
		$sum_list = array();
		foreach ($list as $v) {
			foreach ($cal_field as $f) {
				$sum_list[$f] = floatval($sum_list[$f]) + $v[$f];
			}
		}

		// ����������:
		foreach ($list as $k => $v) {
			$all[$k][$wi]["per_1"] = $v["per_1"];
			$all[$k][$wi]["per_2"] = $v["per_2"];
			$all[$k][$wi]["per_3"] = $v["per_3"];
			$all[$k][$wi]["per_4"] = $v["per_4"];
			$all[$k][$wi]["per_5"] = $v["per_5"];
		}

		$all["sum"][$wi]["per_1"] = @round($sum_list["per_1"] / count($list), 2);
		$all["sum"][$wi]["per_2"] = @round($sum_list["per_2"] / count($list), 2);
		$all["sum"][$wi]["per_3"] = @round($sum_list["per_3"] / count($list), 2);
		$all["sum"][$wi]["per_4"] = @round($sum_list["per_4"] / count($list), 2);
		$all["sum"][$wi]["per_5"] = @round($sum_list["per_5"] / count($list), 2);
	}
}


// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>��������ͳ�� - ����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="res/datejs/picker.js" language="javascript"></script>
<style>
body {padding:5px 8px; }
form {display:inline; }
#date_tips {float:left; font-weight:bold; padding-top:1px; }
#ch_date {float:left; margin-left:20px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.main_title {margin:0 auto; padding-top:30px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:12px; font-family:"����"; }

.list {border:2px solid #43A75C !important; }
.item {padding:8px 3px 6px 3px !important; }

.hl {border-left:2px solid #ADE0BA !important; }
.hr {border-right:2px solid #ADE0BA !important; }
.ht {border-top:2px solid #ADE0BA !important; }
.hb {border-bottom:2px solid #ADE0BA !important; }

.rate_tips {padding:30px 0 0 30px; line-height:24px; }

</style>

<script language="javascript">
function update_date(type, o) {
	byid("date_"+type).value = parseInt(o.innerHTML, 10);

	var a = parseInt(byid("date_1").value, 10);
	var b = parseInt(byid("date_2").value, 10);

	var s = a + '' + (b<10 ? "0" : "") + b;

	byid("date").value = s;
	byid("ch_date").submit();
}

function hgo(dir, o) {
	var obj = byid("type_id");
	if (dir == "up") {
		if (obj.selectedIndex > 1) {
			obj.selectedIndex = obj.selectedIndex - 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("�Ѿ�����ǰ��", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("�Ѿ������һ����", 3);
		}
	}
}
</script>
</head>

<body>
<div style="margin:10px 0 0 0px;">
	<div id="date_tips">ҽԺ��Ŀ��</div>
	<form method="GET" style="margin-left:30px;">
		<select name="type_id" id="type_id" class="combo" onchange="this.form.submit()">
			<option value="" style="color:gray">-��ѡ����Ŀ-</option>
			<?php echo list_option($types, "_key_", "_value_", $cur_type); ?>
		</select>&nbsp;
		<button class="button" onclick="hgo('up',this);">��</button>&nbsp;
		<button class="button" onclick="hgo('down',this);">��</button>
		<input type="hidden" name="month" value="<?php echo $_GET["month"]; ?>">
		<input type="hidden" name="op" value="change_type">
	</form>&nbsp;&nbsp;&nbsp;

	<b>�·ݣ�</b>
	<form method="GET">
		<input name="month" id="time_month" class="input" style="width:100px" value="<?php echo $_GET["month"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'time_month',dateFmt:'yyyy-MM'})" align="absmiddle" style="cursor:pointer" title="ѡ���·�">
		<input type="submit" class="button" value="ȷ��">
	</form>
</div>


<?php if ($cur_type && $_GET["month"]) { ?>

<div class="main_title"><?php echo $type_detail["name"]; ?> <?php echo $_GET["month"]; ?> �����ݶԱ�</div>

<table width="100%" align="center" class="list">
	<tr>
		<td class="head hb" align="center"></td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">��ѯԤԼ��</td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">ԤԼ������</td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">��ѯ������</td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">��Ч��ѯ��</td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">��ЧԤԼ��</td>
	</tr>
	<tr>
		<td class="head hb" align="center">�ͷ�</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

	</tr>

<?php
foreach ($kefu_list as $i) {
	$li = $all[$i];
	if (!is_array($li)) {
		$li = array();
	}

?>
	<tr>
		<td class="item" align="center"><?php echo $i; ?></td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_1"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_1"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_1"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_1"]); ?>%</td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_2"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_2"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_2"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_2"]); ?>%</td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_3"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_3"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_3"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_3"]); ?>%</td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_4"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_4"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_4"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_4"]); ?>%</td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_5"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_5"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_5"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_5"]); ?>%</td>

	</tr>

<?php } ?>

		<td class="item ht" align="center">����</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_1"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_1"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_1"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_1"]); ?>%</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_2"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_2"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_2"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_2"]); ?>%</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_3"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_3"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_3"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_3"]); ?>%</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_4"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_4"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_4"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_4"]); ?>%</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_5"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_5"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_5"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_5"]); ?>%</td>

	</tr>
</table>

<div class="rate_tips">
��ѯԤԼ�� = ԤԼ���� / �ܵ��<br>
ԤԼ������ = ʵ�ʵ�Ժ���� / Ԥ�Ƶ�Ժ����<br>
��ѯ������ = ʵ�ʵ�Ժ���� / �ܵ��<br>
��Ч��ѯ�� = ��Ч��� / �ܵ��<br>
��ЧԤԼ�� = ԤԼ���� / ��Ч���<br>
</div>

<?php } ?>

<br>
<br>

</body>
</html>
