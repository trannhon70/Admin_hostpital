<?php
/*
// - ����˵�� : ҽԺ�б�
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2013-05-01 00:36
*/
require "../../core/core.php";
$table = "disease";

if ($user_hospital_id == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

// �����Ĵ���:
if ($op = $_GET["op"]) {
	switch ($op) {
		case "add":
			include "disease_edit.php";
			exit;

		case "edit":
			$line = $db->query_first("select * from $table where id='$id' limit 1");
			include "disease_edit.php";
			exit;

		case "delete":
			$ids = $_GET["ids"];
			$del_ok = $del_bad = 0; $op_data = array();
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
				$log->add("delete", "ɾ������", serialize($op_data));
			}

			if ($del_bad > 0) {
				msg_box("ɾ���ɹ� $del_ok �����ϣ�ɾ��ʧ�� $del_bad �����ϡ�", "back", 1);
			} else {
				msg_box("ɾ���ɹ�", "back", 1);
			}

		case "hebing":
			$ids = $_GET["ids"];
			// ����:
			foreach ($ids as $k => $v) {
				if (intval($v) > 0) {
					$ids[$k] = intval($v);
				} else {
					unset($ids[$k]);
				}
			}
			if (count($ids) < 2) {
				exit("�����������ֲ��ܺϲ�");
			}
			$dis_list = (array) $db->query("select id,name from disease where id in (".implode(",", $ids).") order by id asc", "id", "name");
			$dis_ids = array_keys($dis_list);
			$to_id = $dis_ids[0];
			$to_name = implode("_", $dis_list);
			// �������˵����ò�ɾ������:
			for ($i = 1; $i < count($dis_ids); $i++) {
				$cur_id = $dis_ids[$i];
				$db->query("update patient_{$hid} set disease_id='$to_id' where disease_id='$cur_id'");
				$db->query("delete from disease where id='$cur_id' limit 1");
			}
			// ���²�������:
			$db->query("update disease set name='$to_name' where id='$to_id' limit 1");
			msg_box("��ѡ�����Ѿ��ϲ�Ϊ��{$to_name}��", "back", 1);

		case "update_sort2":
			$ac = array();
			$dis_list = $db->query("select id,name from disease where hospital_id='$hid'", "id", "name");
			foreach ($dis_list as $k => $v) {
				$count = $db->query("select count(id) as c from patient_{$hid} where concat(',', disease_id, ',') like '%,{$k},%'", 1, "c");
				$db->query("update disease set sort2='$count' where id='$k' limit 1");
			}
			msg_box("������ɣ�", "back", 1);

		default:
			msg_box("����δ����...", "back", 1);
	}
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$aLinkInfo = array(
	"page" => "page",
	"sortid" => "sort",
	"sorttype" => "sorttype",
	"searchword" => "searchword",
);

// ��ȡҳ����ò���:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

// ���嵥Ԫ���ʽ:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"ѡ", "width"=>"4%", "align"=>"center"),
	8=>array("title"=>"ID", "width"=>"5%", "align"=>"center", "sort"=>"id", "defaultorder"=>1),
	1=>array("title"=>"��������", "width"=>"15%", "align"=>"center", "sort"=>"binary name", "defaultorder"=>1),
	2=>array("title"=>"������Ŀ", "width"=>"", "align"=>"left", "sort"=>"binary xiangmu", "defaultorder"=>1),
	6=>array("title"=>"���ȶ�", "width"=>"8%", "align"=>"center", "sort"=>"sort", "defaultorder"=>2),
	7=>array("title"=>"������", "width"=>"8%", "align"=>"center", "sort"=>"sort2", "defaultorder"=>2),
	3=>array("title"=>"����ʱ��", "width"=>"15%", "align"=>"center", "sort"=>"addtime", "defaultorder"=>2),
	4=>array("title"=>"����", "width"=>"12%", "align"=>"center"),
);

// Ĭ������ʽ:
$defaultsort = 3;
$defaultorder = 1;


// ��ѯ����:
$where = array();
$where[] = "hospital_id=$user_hospital_id";
if ($searchword) {
	$where[] = "(binary t.name like '%{$searchword}%')";
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
} else {
	if ($defaultsort > 0 && array_key_exists($defaultsort, $aTdFormat)) {
		//$sqlsort = "order by ".$aTdFormat[$defaultsort]["sort"]." ".$aOrderType[$defaultorder];
		$sqlsort = "order by sort desc,sort2 desc,id asc";
	} else {
		$sqlsort = "";
	}
}
//$sqlsort = "order by hospital, id asc";

// ��ҳ����:
$pagesize = 9999;
$count = $db->query_count("select count(*) from $table $sqlwhere");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

$hospital_id_name = $db->query("select id,name from ".$tabpre."hospital", 'id', 'name');


// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
<style></style>
<script language="javascript">
function set_op(op) {
	if (op == "delete") {
		if (!confirm("ɾ��֮���ָܻ���ȷ��Ҫɾ����")) {
			return false;
		}
	}
	if (op == "hebing") {
		if (!confirm("�ϲ�����֮�󣬲��ָܻ����ϲ�ǰ״̬��ȷ����")) {
			return false;
		}
	}
	byid("op").value = op;
	byid("form1").submit();
	return false;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><span class="tips"><?php echo $hospital_id_name[$user_hospital_id]; ?> - �����б�</span></div>
	<div class="header_center">
		<?php echo $power->show_button("add"); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<button onclick="location='?op=update_sort2'" class="buttonb">��������</button>
	</div>
	<div class="headers_oprate"><form name="topform" method="GET">ģ��������<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="8">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<button onclick="location='?'" class="search" title="�˳�������ѯ">����</button>&nbsp;&nbsp;<button onclick="history.back()" class="button" title="������һҳ">����</button></form></div>
</div>

<!-- ͷ�� end -->

<div class="space"></div>

<!-- �����б� begin -->
<form name="mainform" id="form1">
<table width="100%" align="center" class="list">
	<!-- ��ͷ���� begin -->
	<tr>
<?php
// ��ͷ����:
foreach ($aTdFormat as $tdid => $tdinfo) {
	list($tdalign, $tdwidth, $tdtitle) = make_td_head($tdid, $tdinfo);
?>
		<td class="head" align="<?php echo $tdalign; ?>" width="<?php echo $tdwidth; ?>"><?php echo $tdtitle; ?></td>
<?php } ?>
	</tr>
	<!-- ��ͷ���� end -->

	<!-- ��Ҫ�б����� begin -->
<?php
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];

		$op = array();
		if (check_power("edit")) {
			$op[] = "<a href='?op=edit&id=$id' class='op'>�޸�</a>";
		}
		if (check_power("delete")) {
			//$op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'>ɾ��</a>";
		}
		$op_button = implode(" ", $op);

		$hide_line = ($pinfo && $pinfo["ishide"] && $line["isshow"] != 1) ? 1 : 0;
?>
	<tr<?php echo $hide_line ? " class='hide'" : ""; ?>>
		<td align="center" class="item"><input name="ids[]" type="checkbox" value="<?php echo $id; ?>"></td>
		<td align="center" class="item"><?php echo $line["id"]; ?></td>
		<td align="center" class="item"><?php echo $line["name"]; ?></td>
		<td align="left" class="item"><?php echo $line["xiangmu"]; ?></td>
		<td align="center" class="item"><?php echo $line["sort"]; ?></td>
		<td align="center" class="item"><?php echo $line["sort2"]; ?></td>
		<td align="center" class="item"><?php echo date("Y-m-d H:i", $line["addtime"]); ?></td>
		<td align="center" class="item"><?php echo $op_button; ?></td>
	</tr>
<?php
	}
} else {
?>
	<tr>
		<td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(û������...)</td>
	</tr>
<?php } ?>
	<!-- ��Ҫ�б����� end -->
</table>
<input type="hidden" name="op" id="op" value="">
</form>
<!-- �����б� end -->

<div class="space"></div>

<!-- ��ҳ���� begin -->
<div class="footer_op">
	<div class="footer_op_left">
		<!-- <button onclick="set_op('delete')" class="button">ɾ��</button>&nbsp;&nbsp; -->
		<button onclick="set_op('hebing')" class="buttonb">�ϲ�����</button>
	</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->

</body>
</html>