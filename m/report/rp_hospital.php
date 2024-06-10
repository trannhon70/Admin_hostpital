<?php
// --------------------------------------------------------
// - ����˵�� : ͬ�����ݱ���
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2011-05-12
// --------------------------------------------------------
require "../../core/core.php";
header("Content-Type:text/html;charset=GB2312");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$used_hid = implode(",", $hospital_ids);
if ($used_hid == '') $used_hid = '0';

// ����ҽԺ����:
$h_id_name = $db->query("select id,name from hospital where id in ($used_hid) order by id asc", "id", "name");

// ʱ�䶨��:
$day = date("d");
$his = date("H:i:s");
$month_arr = $month_b_e = array();
for ($i = 0; $i < 6; $i++) {
	$month_arr[$i] = $t = date("Y-m", strtotime("-".$i." month", time()));
	$m_begin =  strtotime($t."-1 0:0:0");
	$m_end = strtotime($t."-".$day." ".$his);
	// �ж�����û�г���(��һ����û��31�յ�ʱ�� strtotime() ���������Ϊ�¸��µ�1�գ����³�����)
	if (date("Y-m-d", $m_end) != ($t."-".$day)) {
		$m_end = strtotime("+1 month") - 1; //����ʱ�����¶�Ϊһ���µ����һ���23:59:59
	}
	$month_b_e[$i] = array($m_begin, $m_end);
}

asort($month_arr);

$type_arr = array(0 => "����ҽԺ", 2 => "����", 3 => "�绰");

$sqladd = '';
if (array_key_exists($_GET["type"], $type_arr)) {
	if ($_GET["type"] > 0) {
		$sqladd = "and part_id=".$_GET["type"];
	}
}


?>
<html>
<head>
<title>ͬ�����ݱ���</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<script src="../../res/datejs/picker.js" language="javascript"></script>
<style>
.clear {clear:both; font-size:0; line-height:0; }

.red {color:red !important; }
.report_tips {padding:20px 0 20px 0; text-align:center; font-size:14px; font-weight:bold;  }
.list {border:2px solid #6AB5FF !important; }
.head {}
.item {text-align:center; padding:6px 3px 4px 3px !important; }
.hl {border-left:2px solid #A4D1FF !important; }
.hr {border-right:2px solid #A4D1FF !important; }
.ht {border-top:2px solid #A4D1FF !important; }
.hb {border-bottom:2px solid #A4D1FF !important; }

/* ������ */
#progress_bar {margin:20px auto 20px auto; }
#p_title {float:left; width:90px; text-align:left; padding-top:0px; }
#p_border {float:left; width:202px; height:12px; border:1px solid #405050; background:white; }
#p_cur {margin:1px; width:0px; height:10px; font-size:0; line-height:0; background-color:#FF8000; }
</style>

<script language="javascript">
// ���½�����
// s: 0 - 100   ��100ʱ��ʾ100%
function update_p(s) {
	s = s + 10;
	if (s < 0) s = 0;
	if (s > 100) s = 100;
	byid("p_cur").style.width = s*2+"px";
}
</script>

</head>

<body>

<!-- ����������ɺ����� -->
<div id="progress_bar" style="display:none;">
	<div id="p_title">&nbsp;���ݲ�ѯ�У�</div>
	<div id="p_border"><div id="p_cur"></div></div>
	<div class="clear"></div>
</div>

<?php
echo '<script>byid("progress_bar").style.display = "block"; </script>'."\r\n";
flush();
ob_flush();
ob_end_flush();

// ��ѯ����:
$data_arr = array();
$index = 0;
foreach ($h_id_name as $k => $v) {
	$t = "patient_".$k;
	foreach ($month_b_e as $i => $be) {
		$b = $be[0];
		$e = $be[1];
		ob_start();
		$data_arr[$k][$i] = @$db->query("select count(*) as c from $t where order_date>=$b and order_date<$e and status=1 $sqladd", 1, "c");
		ob_end_clean();
	}

	$index++; //�Ѵ����Ľ���
	$progress = round(($index / count($h_id_name)) * 100, 1);
	echo '<script type="text/javascript">update_p('.$progress.');</script>'."\r\n";
	flush();
	ob_flush();
	ob_end_flush();
}

?>

<script type="text/javascript">
byid("progress_bar").style.display = "none";
</script>

<div style="text-align:center; padding:20px 0 5px 0;">
	���ͣ�<input type="button" class="buttonb" value="����ҽԺ" onclick="location='?'">&nbsp;
	<input type="button" class="buttonb" value="����" onclick="location='?type=2'">&nbsp;
	<input type="button" class="buttonb" value="�绰" onclick="location='?type=3'">&nbsp; (���ѣ����ڱ�ҳ���ѯ��������̫������ռ�÷�������Դ���뾡������ˢ�¡�)
</div>

<div class="report_tips">����ͬ�����ݶԱ� (ÿ��<?php echo $day; ?>���ڵ�Ժ����) (<?php echo $type_arr[intval($_GET["type"])]; ?>)</div>

<table class="list" width="100%">
	<tr>
		<th class="head">ҽԺ</th>
<?php foreach ($month_arr as $i => $m) { ?>
		<th class="head"><?php echo $m; ?></th>
<?php } ?>
	</tr>

<?php
foreach ($h_id_name as $_hid => $_hname) {
?>

	<tr>
		<td class="item"><?php echo $_hname; ?></td>

<?php foreach ($month_arr as $i => $m) { ?>
		<td class="item"><?php echo @intval($data_arr[$_hid][$i]); ?></td>
<?php } ?>
	</tr>

<?php } ?>

</table>

<br>
<br>

</body>
</html>