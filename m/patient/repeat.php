<?php
/*
// - ����˵�� : �ظ�ԤԼ�����б�
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2013-05-18 10:31
*/
require "../../core/core.php";
$table = "patient_" . $user_hospital_id;

check_power('', $pinfo) or msg_box("û�д�Ȩ��...", "back", 1);
if ($user_hospital_id == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

// �����Ĵ���:
if ($op = $_GET["op"]) {
	switch ($op) {
		case "insert":
			check_power("i", $pinfo, $pagepower) or msg_box("û������Ȩ��...", "back", 1);
			header("location:" . $pinfo["insertpage"]);
			break;

		case "delete":
			//!check_power("delete") && msg_box("û��ɾ��Ȩ��...", "back", 1);

			$ids = explode(",", $_GET["id"]);
			$del_ok = $del_bad = 0;
			$op_data = array();
			foreach ($ids as $opid) {
				if (($opid = intval($opid)) > 0) {
					$tmp_data = $db->query_first("select * from $table where id='$opid' limit 1");
					if ($db->query("delete from $table where id='$opid' limit 1")) {
						$del_ok++;
						$op_data[] = $tmp_data;
					} else {
						$del_bad++;
					}
				}
			}

			if ($del_ok > 0) {
				$log->add("delete", "ɾ��ԤԼ����: " . implode("��", $del_name), $op_data, $table);
			}

			if ($del_bad > 0) {
				msg_box("ɾ���ɹ� $del_ok �����ϣ�ɾ��ʧ�� $del_bad �����ϡ�", "back", 1);
			} else {
				msg_box("ɾ���ɹ�", "back", 1);
			}

		case "setshow":
			check_power("h", $pinfo, $pagepower) or msg_box("û�п�ͨ�͹ر�Ȩ��...", "back", 1);

			$isshow_value = intval($_GET["value"]) > 0 ? 1 : 0;
			$ids = explode(",", $_GET["id"]);
			$set_ok = $set_bad = 0;
			foreach ($ids as $opid) {
				if (($opid = intval($opid)) > 0) {
					if ($db->query("update $table set isshow='$isshow_value' where id='$opid' limit 1")) {
						$set_ok++;
					} else {
						$set_bad++;
					}
				}
			}

			if ($set_bad > 0) {
				msg_box("�����ɹ���� $set_ok ����ʧ�� $del_bad ����", "back", 1);
			} else {
				msg_box("���óɹ���", "back", 1);
			}

		default:
			msg_box("����δ����...", "back", 1);
	}
}

if ($_GET["btime"]) {
	$_GET["begin_time"] = strtotime($_GET["btime"] . " 0:0:0");
}
if ($_GET["etime"]) {
	$_GET["end_time"] = strtotime($_GET["etime"] . " 23:59:59");
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$aLinkInfo = array(
	"page" => "page",
	"sortid" => "sort",
	"sorttype" => "sorttype",
	"stype" => "type",
	"begin_time" => "begin_time",
	"end_time" => "end_time",
);

// ��ȡҳ����ò���:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

// ���嵥Ԫ���ʽ:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	2 => array("title" => "����", "width" => "50", "align" => "center", "sort" => "name", "defaultorder" => 1),
	16 => array("title" => "�Ա�", "width" => "", "align" => "center", "sort" => "sex", "defaultorder" => 1),
	15 => array("title" => "����", "width" => "", "align" => "center", "sort" => "age", "defaultorder" => 1),
	3 => array("title" => "�绰", "width" => "", "align" => "center", "sort" => "tel", "defaultorder" => 1),
	4 => array("title" => "ר�Һ�", "width" => "", "align" => "center", "sort" => "zhuanjia_num", "defaultorder" => 1),
	5 => array("title" => "��ѯ����", "width" => "", "align" => "center", "sort" => "content", "defaultorder" => 1),
	6 => array("title" => "�Ӵ�", "width" => "", "align" => "center", "sort" => "jiedai", "defaultorder" => 1),
	7 => array("title" => "ԤԼʱ��", "width" => "8%", "align" => "center", "sort" => "order_sort", "defaultorder" => 2),
	8 => array("title" => "ʣ������", "width" => "", "align" => "center", "sort" => "remain_time", "defaultorder" => 2),
	9 => array("title" => "��������", "width" => "", "align" => "center", "sort" => "disease_id", "defaultorder" => 1),
	10 => array("title" => "ý����Դ", "width" => "", "align" => "center", "sort" => "media_from", "defaultorder" => 1),
	17 => array("title" => "����", "width" => "", "align" => "center", "sort" => "part_id", "defaultorder" => 1),
	11 => array("title" => "��ע", "width" => "", "align" => "center", "sort" => "memo", "defaultorder" => 1),
	12 => array("title" => "�ͷ�", "width" => "40", "align" => "center", "sort" => "author", "defaultorder" => 1),
	13 => array("title" => "��Լ���", "width" => "", "align" => "center", "sort" => "status_1", "defaultorder" => 2, "sort2" => "addtime desc"),
	1 => array("title" => "����ʱ��", "width" => "8%", "align" => "center", "sort" => "addtime", "defaultorder" => 2),
	99 => array("title" => "����", "width" => "80", "align" => "center"),
);

// Ĭ������ʽ:
$defaultsort = 7;
$defaultorder = 2;

// ��ѯ����:
$where = array();
if ($_GET["begin_time"]) {
	$where[] = "addtime>=" . $_GET["begin_time"];
}
if ($_GET["end_time"]) {
	$where[] = "addtime<=" . $_GET["end_time"];
}
$where[] = "$stype!='' and $stype!='��'";
$sqlwhere = count($where) > 0 ? ("where " . implode(" and ", $where)) : "";

// ������Ĵ�����
if ($sortid > 0) {
	$sqlsort = "order by " . $aTdFormat[$sortid]["sort"] . " ";
	if ($sorttype > 0) {
		$sqlsort .= $aOrderType[$sorttype];
	} else {
		$sqlsort .= $aOrderType[$aTdFormat[$sortid]["defaultorder"]];
	}
	if ($aTdFormat[$sortid]["sort2"]) {
		$sqlsort .= ',' . $aTdFormat[$sortid]["sort2"];
	}
} else {
	if ($defaultsort > 0 && array_key_exists($defaultsort, $aTdFormat)) {
		$sqlsort = "order by " . $aTdFormat[$defaultsort]["sort"] . " " . $aOrderType[$defaultorder];
	} else {
		$sqlsort = "";
	}
}
$sqlsort = $sqlsort ? ($sqlsort . ",order_date asc") : "order_date desc";

$data = array();
if ($stype != '') {
	// ��ҳ����:
	$count = $db->query("select count(a.$stype) as count1 from (select $stype,count($stype) as count from $table $sqlwhere group by $stype) as a where a.count>1", 1, "count1");
	$pagecount = max(ceil($count / $pagesize), 1);
	$page = max(min($pagecount, intval($page)), 1);
	$offset = ($page - 1) * $pagesize;

	// ��ѯ:
	$time = time();
	$today_begin = mktime(0, 0, 0);
	$today_end = $today_begin + 24 * 3600;

	$data = $db->query("select a.$stype, a.count from (select $stype,count($stype) as count from $table $sqlwhere group by $stype) as a where a.count>1 order by a.count desc limit $offset,$pagesize");

	//$data = $db->query("select *,(order_date-$time) as remain_time, if(order_date<$today_begin, 1, if(order_date>$today_end, 2, 3)) as order_sort, if(status=1,2, if(status=2,1,0)) as status_1 from $table $sqlwhere $sqlsort limit $offset,$pagesize");

	$data2 = array();
	foreach ($data as $li) {
		$rs[] = "'" . $li[$stype] . "'";
	}
	$rs = @implode(",", $rs);
	if ($rs) {
		$tm = $db->query("select *,(order_date-$time) as remain_time, if(order_date<$today_begin, 1, if(order_date>$today_end, 2, 3)) as order_sort, if(status=1,2, if(status=2,1,0)) as status_1 from $table where $stype in ($rs)");
		foreach ($tm as $li) {
			$data2[$li[$stype]][] = $li;
		}
	}
}


// id => name:
$hospital_id_name = $db->query("select id,name from " . $tabpre . "hospital", 'id', 'name');
$part_id_name = $db->query("select id,name from " . $tabpre . "sys_part", 'id', 'name');
$disease_id_name = $db->query("select id,name from " . $tabpre . "disease", 'id', 'name');

// ҳ�濪ʼ ------------------------
?>
<html>

<head>
	<title><?php echo $pinfo["title"]; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
	<link href="../../res/base.css" rel="stylesheet" type="text/css">
	<script src="../../res/base.js" language="javascript"></script>
	<script src="../../res/datejs/picker.js" language="javascript"></script>
</head>

<body>
	<!-- ͷ�� begin -->
	<div class="headers">
		<div class="headers_title" style="width:25%"><span class="tips"><?= $hospital_id_name[$user_hospital_id] ?> - �ظ�ԤԼ����</span></div>
		<div class="header_center" style="width:65%;">
			<form action="?" method="GET">
				<b>��</b>: <input name="btime" id="begin_time" class="input" style="width:80px" value="<?php echo $_GET["btime"]; ?>"> <img src="../../res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
				<b>ֹ</b>: <input name="etime" id="end_time" class="input" style="width:80px" value="<?php echo $_GET["etime"]; ?>"> <img src="../../res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
				<b>�ظ���</b>: <select name="type" class="combo">
					<?php echo list_option(array("tel" => "�绰", "name" => "����"), "_key_", "_value_", $_GET["type"]); ?>
				</select>
				<input type="submit" class="button" value="ȷ��">
			</form>
		</div>
		<div class="headers_oprate"><button onclick="history.back()" class="button" title="������һҳ">����</button></div>
	</div>
	<!-- ͷ�� end -->

	<div class="space"></div>
	<!-- �����б� begin -->
	<form name="mainform" action="" method="POST">
    <table width="100%" align="center" class="list">
        <!-- Phần Header của bảng -->
        <tr>
            <?php
            // Xử lý phần Header của bảng
            foreach ($aTdFormat as $tdid => $tdinfo) {
                list($tdalign, $tdwidth, $tdtitle) = make_td_head($tdid, $tdinfo);
            ?>
                <td class="head" align="<?php echo $tdalign; ?>" width="<?php echo $tdwidth; ?>"><?php echo $tdtitle; ?></td>
            <?php } ?>
        </tr>

        <!-- Dữ liệu của bảng -->
        <?php
        if (count($data) > 0) {
            foreach ($data as $li) {
        ?>
                <tr>
                    <td colspan="<?php echo count($aTdFormat); ?>" align="left" class="group"><?php echo $li[$stype] . " [" . $li["count"] . "]"; ?></td>
                </tr>
                <?php
                foreach ($data2[$li[$stype]] as $line) {
                    $id = $line["id"];

                    // Xử lý các phần tử của dòng dữ liệu
                    // (phần tử này sẽ thêm vào bảng)
                    $op = array();
                    $op[] = "<a href='patient.php?op=view&id=$id' class='op'><img src='/res/img/b_detail.gif' align='absmiddle' title='Xem' alt=''></a>";
                    $op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'>Xóa</a>";
                    $op_button = implode("&nbsp;", $op);

                    $hide_line = ($pinfo && $pinfo["ishide"] && $line["isshow"] != 1) ? 1 : 0;

                    $op_come = array();
                    if ($uinfo["part_id"] == 1 || $uinfo["part_id"] == 4) {
                        $op_come[] = '<a href="#" id="come_' . $id . '_1" onclick="set_come(' . $id . ',1); return false;" style="display:' . (($line["status"] == 0 || $line["status"] == 2) ? 'inline' : 'none') . '">[Đã đến]</a>';
                        $op_come[] = '<a href="#" id="come_' . $id . '_2" onclick="set_come(' . $id . ',2); return false;" style="display:' . (($line["status"] == 0 || $line["status"] == 1) ? 'inline' : 'none') . '">[Chưa đến]</a>';
                    }
                    $op_come_button = implode('', $op_come);

                    if ($uinfo["part_id"] == 1 || $uinfo["part_id"] == 4) {
                        if ($line["xiaofei"] == 0) {
                            $xiaofei_button = '<a href="#" onclick="set_xiaofei(' . $id . ',1); return false;">Đã</a>';
                        } else {
                            $xiaofei_button = '<a href="#" onclick="set_xiaofei(' . $id . ',0); return false;">Chưa</a>';
                        }
                    } else {
                        $xiaofei_button = $line["xiaofei"] ? 'Đã' : 'Chưa';
                    }

                    $line_color = array('', 'red', 'silver');
                ?>
                    <tr<?php echo $hide_line ? " class='hide'" : ""; ?> id="list_line_<?php echo $id; ?>" style="color:<?php echo $line_color[$line["status"]]; ?>">
                        <!-- Các cột dữ liệu của hàng -->
                        <td align="center" class="item" style="display:none"><input name="delcheck" type="checkbox" value="<?php echo $id; ?>" onpropertychange="set_item_color(this)" disabled="disabled"></td>
                        <td align="center" class="item"><?php echo $line["name"]; ?></td>
                        <td align="center" class="item"><?php echo $line["sex"]; ?></td>
                        <td align="center" class="item"><?php echo $line["age"] > 0 ? $line["age"] : ''; ?></td>
                        <!-- Các cột dữ liệu khác có thể thêm vào đây -->
                        <td align="center" class="item"><?php echo $op_button; ?></td>
                    </tr>
                <?php
                }
            }
        } else {
                ?>
                <tr>
                    <td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(Không có dữ liệu được tìm thấy)</td>
                </tr>
            <?php } ?>
    </table>
</form>

	<!-- �����б� end -->

	<!-- ��ҳ���� begin -->
	<div class="space"></div>
	<div class="footer_op">
		<div class="footer_op_left"><button onclick="select_all()" class="button">ȫѡ</button>&nbsp;<button onclick="unselect()" class="button">��ѡ</button>&nbsp;<?php echo $power->show_button("hide,delete"); ?></div>
		<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
	</div>
	<!-- ��ҳ���� end -->

</body>

</html>