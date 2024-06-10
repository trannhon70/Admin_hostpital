<?php
/*
// ˵��: ����ͼ���� (���ղ鿴)
// ����: ��ҽս�� 
// ʱ��: 2011-01-15
*/
require "../../core/core.php";
include "../../res/chart/FusionCharts_Gen.php";

if ($op == "run") {

	if (count($hospital_ids) == 0) {
		exit_html("û�п�����ʾ��ҽԺ��");
	}

	// ѭ����ʾҽԺ:
	if ($_GET["show"] == "next") {
		$last_hid = $_SESSION["rhid"];
		if (!$last_hid) {
			$cur_hid = $hospital_ids[0];
		} else {
			foreach ($hospital_ids as $k => $v) {
				if ($v == $last_hid) {
					if ($hospital_ids[$k+1]) {
						$cur_hid = $hospital_ids[$k+1];
					} else {
						$cur_hid = $hospital_ids[0];
					}
					break;
				}
			}
		}
	} else {
		$cur_hid = $hospital_ids[0];
	}

	$_SESSION["rhid"] = $cur_hid;

	//$cur_hid = 69; //����֮��

	// ҽԺ��Ϣ:
	$h_info = $db->query("select * from hospital where id=$cur_hid limit 1", "1");
	$h_name = $h_info["name"];
	$hc = @unserialize($h_info["config"]); //ҽԺ������Ϣ���ں��ָ������ݣ�

	$table = "patient_".$cur_hid;

	// ��ѯ���µ�ԤԼ/�������ݣ��Լ��ο���
	$ms = array();
	for ($i = 1; $i<=31; $i++) {
		if ($i <= 28 || checkdate(date("n", $date_time), $i, date("Y", $date_time))) {
			$dt = date("Y-m-d", mktime(0,0,0,date("m"),$i));
			$ms[str_replace("-", "", $dt)] = $dt;
		}
	}

	$d_jishu = intval($hc[date("Ym")]["jiangli_jishu"] / count($ms));
	$d_zhibiao = intval($hc[date("Ym")]["jiangli_zhibiao"] / count($ms));
	$d_mubiao = intval($hc[date("Ym")]["jiuzhen_mubiao"] / count($ms));

	$come = $yue = $jishu = $zhibiao = $mubiao = array(); // ��Ժ����
	foreach ($ms as $ndt => $dt) {
		// �ο�������:
		$jishu[$ndt] = $d_jishu;
		$zhibiao[$ndt] = $d_zhibiao;
		$mubiao[$ndt] = $d_mubiao;

		// ͳ��:
		$timebegin = strtotime($dt." 0:0:0"); //��ʼ
		$timeend = strtotime("+1 day", $timebegin); //����
		$come[$ndt] = @intval($db->query("select count(*) as c from $table where order_date>=$timebegin and order_date<$timeend and status=1", 1, "c"));
		$yue[$ndt] = @intval($db->query("select count(*) as c from $table where addtime>=$timebegin and addtime<$timeend", 1, "c"));
	}



	// �����������:
	$FC = new FusionCharts("MSColumn2D","1000","500", "", 1);
	$FC->setSWFPath("/res/chart/");
	$FC->setChartParams("decimalPrecision=0; formatNumberScale=0; baseFontSize=12; baseFont=Arial; chartBottomMargin=0; outCnvBaseFontSize=12;numdivlines=0;" );

	foreach ($ms as $dt) {
		$FC->addCategory(date("j", strtotime($dt)));
	}

	$FC->addDataset("��Ժ��","numberPrefix=;showValues=1;color=800000");
	foreach ($come as $dt => $v) {
		$FC->addChartData($v);
	}

	$FC->addDataset("ԤԼ��","numberPrefix=;showValues=1;color=405050");
	foreach ($yue as $dt => $v) {
		$FC->addChartData($v);
	}

	$FC->addTrendLine("startValue={$d_jishu};color=ff0000;displayvalue=�������� ({$d_jishu});showOnTop=1");
	$FC->addTrendLine("startValue={$d_zhibiao};color=005800;displayvalue=����ָ�� ({$d_zhibiao});showOnTop=1");
	$FC->addTrendLine("startValue={$d_mubiao};color=0000ff;displayvalue=Ŀ����� ({$d_mubiao});showOnTop=1");

	$max = @max(max($mubiao), max($come), max($zhibiao), max($jishu));
	$ymax = 10 * ceil(($max + 10) / 10);
	$FC->setChartParams("PYAxisMaxValue={$ymax}");
	$FC->setChartParams("SYAxisMaxValue={$ymax}");
}

?>
<html>
<head>
<title>����ԤԼ/��Ժ����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gbk">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<script src='../../res/chart/FusionCharts.js' language='javascript'></script>
<style>
.w400 {width:400px }
.w800 {width:1000px; }
.hr {border:0; margin:0; padding:0; height:3px; line-height:0; font-size:0; background-color:red; color:white; border-top:1px solid silver; }
.h_name {font-size:16px; color:black; font-family:"΢���ź�"; font-weight:bold; margin-top:20px; margin-bottom:20px; }

.a_button {font-size:14px; font-weight:bold; line-height:30px; text-align:center; width:100px; height:30px; background:url('/res/img/button_submit.gif');}
</style>

<script type="text/javascript">
function show_next() {
	location = "?op=run&show=next";
}
</script>
</head>

<body>

<?php if ($op == "run") { ?>

<div style="width:100%; margin:0 auto; text-align:center;">

	<div class="h_name"><?php echo $h_name." <font color=white>".$cur_hid."</font>"; ?></div>

	<?php $FC->renderChart(); ?>
	<!-- <div class="w800" style="text-align:center"><?php echo "<b>"."Ŀ��/��Ժ����</b>"; ?></div> -->

	<br>

	<?php //$FC2->renderChart(); ?>
	<!-- <div class="w800" style="text-align:center"><?php echo "<b>".date("Y��n��j�� H:i")." ��ʱԤԼ����</b>"; ?></div> -->

</div>

<?php if ($d_jishu == 0 || $d_zhibiao == 0 || $d_mubiao == 0) { ?>
<div style="padding:20px; text-align:center;">
<b>��ʾ</b>�������ý�������������ָ���Ŀ���������(��ҽԺ�б���)
</div>
<?php } ?>

<script type="text/javascript">
setTimeout("show_next()", 12000);
</script>

<?php } else { ?>

<div style="margin:50px;">
	<a href="report4.php?op=run" class="a_button" target="_blank">���·ݲ鿴</a> &nbsp; &nbsp;
	<a href="report4_d.php?op=run" class="a_button" target="_blank">���ղ鿴</a>
</div>

<?php } ?>

</body>
</html>