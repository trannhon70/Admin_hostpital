<?php
/*
// - ����˵�� : ԤԼ�����б�
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2013-05-18 21:40
*/
require "../../core/core.php";
$table = "guahao";

if (!$user_hospital_id) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

// ���ݿ����:
$hospital_id_name = $db->query("select id,name from ".$tabpre."hospital", 'id', 'name');

check_power('', $pinfo) or msg_box("û�д�Ȩ��...", "back", 1);

// �����Ĵ���:
if ($op = $_GET["op"]) {
	switch ($op) {
		case "insert":
			check_power("i", $pinfo, $pagepower) or msg_box("û������Ȩ��...", "back", 1);
			header("location:".$pinfo["insertpage"]);
			break;

		case "delete":
			check_power("delete") or msg_box("û��ɾ��Ȩ��...", "back", 1);

			$ids = explode(",", $_GET["id"]);
			$del_ok = $del_bad = 0; $op_data = array();
			foreach ($ids as $opid) {
				if (($opid = intval($opid)) > 0) {
					$tmp_data = $db->query("select * from $table where id='$opid' limit 1", 1);
					if ($db->query("delete from $table where id='$opid' limit 1")) {
						$del_ok++;
						$op_data[] = $tmp_data;
					} else {
						$del_bad++;
					}
				}
			}

			if ($del_ok > 0) {
				$log->add("delete", "ɾ������", serialize($op_data));
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
	$_GET["begin_time"] = strtotime($_GET["btime"]);
}
if ($_GET["etime"]) {
	$_GET["end_time"] = strtotime($_GET["etime"]);
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$aLinkInfo = array(
	"page" => "page",
	"sortid" => "sort",
	"sorttype" => "sorttype",
	"searchword" => "searchword",
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
	0=>array("title"=>"ѡ", "width"=>"40", "align"=>"center"),
	1=>array("title"=>"����", "width"=>"50", "align"=>"center", "sort"=>"name", "defaultorder"=>1),
	2=>array("title"=>"�Ա�", "width"=>"", "align"=>"center", "sort"=>"sex", "defaultorder"=>1),
	3=>array("title"=>"�绰", "width"=>"", "align"=>"center", "sort"=>"tel", "defaultorder"=>1),
	10=>array("title"=>"����", "width"=>"", "align"=>"center", "sort"=>"city", "defaultorder"=>1),
	4=>array("title"=>"EMAIL", "width"=>"", "align"=>"center", "sort"=>"email", "defaultorder"=>1),
	5=>array("title"=>"ԤԼʱ��", "width"=>"8%", "align"=>"center", "sort"=>"order_date", "defaultorder"=>2),
	6=>array("title"=>"��ѯ����", "width"=>"", "align"=>"center", "sort"=>"content", "defaultorder"=>1),
	7=>array("title"=>"��ע", "width"=>"", "align"=>"center", "sort"=>"memo", "defaultorder"=>2),
	8=>array("title"=>"����ʱ��", "width"=>"8%", "align"=>"center", "sort"=>"addtime", "defaultorder"=>2),
	11=>array("title"=>"�ύ��վ", "width"=>"", "align"=>"center", "sort"=>"site", "defaultorder"=>1),
	9=>array("title"=>"����", "width"=>"80", "align"=>"center"),
);

// Ĭ������ʽ:
$defaultsort = 8;
$defaultorder = 2;

// ��ѯ����:
$where = array();
$where[] = "hospital_id=$user_hospital_id";
if ($searchword) {
	$where[] = "(binary name like '%{$searchword}%' or tel like '%{$searchword}%' or content like '%{$searchword}%' or memo like '%{$searchword}%')";
}

$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";

// ������Ĵ�����
if ($sortid > 0) {
	$sqlsort = "order by ".$aTdFormat[$sortid]["sort"]." ";
	if ($sorttype > 0) {
		$sqlsort .= $aOrderType[$sorttype];
	} else {
		$sqlsort .= $aOrderType[$aTdFormat[$sortid]["defaultorder"]];
	}
	if ($aTdFormat[$sortid]["sort2"]) {
		$sqlsort .= ','.$aTdFormat[$sortid]["sort2"];
	}
} else {
	if ($defaultsort > 0 && array_key_exists($defaultsort, $aTdFormat)) {
		$sqlsort = "order by ".$aTdFormat[$defaultsort]["sort"]." ".$aOrderType[$defaultorder];
	} else {
		$sqlsort = "";
	}
}
$sqlsort = $sqlsort ? ($sqlsort.",addtime asc") : "addtime desc";

// ��ҳ����:
$count = $db->query("select count(*) as count from $table $sqlwhere", 1, "count");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

// ���б����ݷ���:
if ($sortid == 8 || ($sortid == 0 && $defaultsort == 8)) {

	$today_begin = mktime(0,0,0);
	$today_end = $today_begin + 24*3600;
	$yesterday_begin = $today_begin - 24*3600;

	$data_part = array();
	foreach ($data as $line) {
		if ($line["addtime"] < $yesterday_begin) {
			$data_part[3][] = $line;
		} else if ($line["addtime"] < $today_begin) {
			$data_part[2][] = $line;
		} else if ($line["addtime"] < $today_end) {
			$data_part[1][] = $line;
		}
	}

	$data = array();
	if (count($data_part[1]) > 0) {
		$data[] = array("id"=>0, "name"=>"���� [".count($data_part[1])."]");
		$data = array_merge($data, $data_part[1]);
	}
	if (count($data_part[2]) > 0) {
		$data[] = array("id"=>0, "name"=>"���� [".count($data_part[2])."]");
		$data = array_merge($data, $data_part[2]);
	}
	if (count($data_part[3]) > 0) {
		$data[] = array("id"=>0, "name"=>"ǰ������ [".count($data_part[3])."]");
		$data = array_merge($data, $data_part[3]);
	}
	unset($data_part);
}

// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title" style="width:45%"><span class="tips"><?=$hospital_id_name[$user_hospital_id]?> - �Һ��б�</span></div>
	<div class="header_center"><?php echo $power->show_button("add"); ?></div>
	<div class="headers_oprate"><form name="topform" method="GET">ģ��������<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="8">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<button onClick="location='?'" class="search" title="�˳�������ѯ">�˳�</button>&nbsp;<button onClick="history.back()" class="button" title="������һҳ">����</button></form></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<!-- �����б� begin -->
<form name="mainform">
<table width="100%" align="center" class="list">
	<!-- ��ͷ���� begin -->
	<tr>
<?php
// ��ͷ����:
foreach ($aTdFormat as $tdid => $tdinfo) {
	list($tdalign, $tdwidth, $tdtitle) = make_td_head($tdid, $tdinfo);
?>
		<td class="head" align="<?php echo $tdalign; ?>" width="<?php echo $tdwidth; ?>"><?php echo $tdtitle; ?></td>
<? } ?>
	</tr>
	<!-- ��ͷ���� end -->

	<!-- ��Ҫ�б����� begin -->
<?php
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];
		if ($id == 0) {
?>
	<tr>
		<td colspan="<?php echo count($aTdFormat); ?>" align="left" class="group"><?php echo $line["name"]; ?></td>
	</tr>
<?php
		} else {

		$op = array();
		if (check_power("v", $pinfo, $pagepower)) {
			$op[] = "<a href='".$pinfo["viewpage"]."?id=$id' class='op'><img src='/res/img/b_detail.gif' align='absmiddle' title='�鿴' alt=''></a>";
		}
		if (check_power("edit")) {
			$op[] = "<a href='".$pinfo["editpage"]."?id=$id&go=back' class='op'>�޸�</a>";
		}
		if (check_power("delete")) {
			$op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'>ɾ��</a>";
		}
		$op_button = implode("&nbsp;", $op);

		$hide_line = ($pinfo && $pinfo["ishide"] && $line["isshow"] != 1) ? 1 : 0;

?>
	<tr<?php echo $hide_line ? " class='hide'" : ""; ?>>
		<td align="center" class="item"><input name="delcheck" type="checkbox" value="<?php echo $id; ?>" onpropertychange="set_item_color(this)"></td>
		<td align="center" class="item"><?php echo $line["name"]; ?></td>
		<td align="center" class="item"><?php echo $line["sex"]; ?></td>

		<td align="center" class="item"><?php echo $line["tel"]; ?></td>
		<td align="center" class="item"><?php echo $line["city"]; ?></td>
		<td align="center" class="item"><?php echo $line["email"]; ?></td>
		<td align="center" class="item"><?php echo $line["order_date"] > 0 ? str_replace("*", "<br>", date("Y-m-d*H:i", $line["order_date"])) : ''; ?></td>
		<td align="left" class="item"><?php echo $line["content"]; ?></td>
		<td align="left" class="item"><?php echo $line["memo"]; ?></td>
		<td align="center" class="item"><?php echo str_replace("*", "<br>", date("Y-m-d*H:i", $line["addtime"])); ?></td>
		<td align="center" class="item"><?php echo $line["site"]; ?></td>
		<td align="center" class="item"><?php echo $op_button; ?></td>
	</tr>
<?php
		}
	}
} else {
?>
	<tr>
		<td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(û������...)</td>
	</tr>
<?php } ?>
	<!-- ��Ҫ�б����� end -->

</table>
</form>
<!-- �����б� end -->

<!-- ��ҳ���� begin -->
<div class="space"></div>
<div class="footer_op">
	<div class="footer_op_left"><button onClick="select_all()" class="button">ȫѡ</button>&nbsp;<button onClick="unselect()" class="button">��ѡ</button>&nbsp;<?php echo $power->show_button("hdie,delete"); ?></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->

</body>
</html>