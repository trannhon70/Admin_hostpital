<?php
// --------------------------------------------------------
// - ����˵�� : ͳ�� ��Ŀ ����
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2010-10-13 11:34
// --------------------------------------------------------
require "../../core/core.php";
$table = "count_type";

// �����Ĵ���:
if ($op) {
	if ($op == "add") {
		include "tel_type.edit.php";
		exit;
	}

	if ($op == "edit") {
		include "tel_type.edit.php";
		exit;
	}

	if ($op == "delete") {
		$ids = explode(",", $_GET["id"]);
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
	}
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$link_param = array("page","sort","order","key");
$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}
extract($param);

// ���嵥Ԫ���ʽ:
$list_heads = array(
	"ѡ" => array("width"=>"32", "align"=>"center"),
	"��Ŀ����" => array("align"=>"left", "sort"=>"binary name", "order"=>"asc"),
	"�ͷ�" => array("align"=>"left", "sort"=>"kefu", "order"=>"asc"),
	"����Ա" => array("align"=>"left", "sort"=>"uids", "order"=>"asc"),
	"����ʱ��" => array("width"=>"15%", "align"=>"center", "sort"=>"addtime", "order"=>"desc"),
	"����" => array("width"=>"", "align"=>"center", "sort"=>"sort", "order"=>"desc"),
	"����" => array("width"=>"12%", "align"=>"center"),
);

// Ĭ������ʽ:
$default_sort = "����";
$default_order = "desc";


// �б���ʾ��:
$t = load_class("table");
$t->set_head($list_heads, $default_sort, $default_order);
$t->set_sort($_GET["sort"], $_GET["order"]);
$t->param = $param;
$t->table_class = "new_list";


// ��ѯ����:
$where = array();
$where[] = "type='tel'";
if ($key) {
	$where[] = "(binary name like '%{$key}%')";
}
$sqlwhere = $db->make_where($where);

// ������Ĵ�����
$sqlsort = $db->make_sort($list_heads, $sort, $order, $default_sort, $default_order);

// ��ҳ����:
$pagesize = 9999;
$count = $db->query_count("select count(*) from $table $sqlwhere");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$list = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

$admin_id_name = $db->query("select id,realname from sys_admin where isshow=1", "id", "realname");

foreach ($list as $id => $li) {
	$r = array();

	$r["ѡ"] = '<input name="delcheck" type="checkbox" value="'.$li["id"].'" onclick="set_item_color(this)">';
	$r["��Ŀ����"] = $li["name"];
	$r["�ͷ�"] = $li["kefu"];

	$uids = explode(",", $li["uids"]);
	$u_names = array();
	foreach ($uids as $v) {
		if (array_key_exists($v, $admin_id_name)) {
			$u_names[] = $admin_id_name[$v];
		}
	}
	$r["����Ա"] = implode("��", $u_names);
	$r["����ʱ��"] = date("Y-m-d H:i", $li["addtime"]);
	$r["����"] = intval($li["sort"]);

	$op = array();
	if (check_power("edit")) {
		$op[] = "<a href='?op=edit&id=".$li["id"]."&back_url=".$back_url."' class='op' title='�޸�����'>�޸�</a>";
	}
	if (check_power("delete")) {
		$op[] = "<a href='?op=delete&id=".$li["id"]."' onclick='return isdel()' class='op'>ɾ��</a>";
	}
	$r["����"] = implode($GLOBALS["button_split"], $op);

	$t->add($r);
}


$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");

// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../../res/base.css" rel="stylesheet" type="text/css">
<script src="../../res/base.js" language="javascript"></script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title" style="width:50%"><span class="tips">ҽԺ��Ŀ����</span></div>
	<div class="header_center"><?php echo $power->show_button("add"); ?></div>
	<div class="headers_oprate"><form name="topform" method="GET">ģ��������<input name="key" value="<?php echo $_GET["key"]; ?>" class="input" size="8">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<button onclick="location='?'" class="search" title="�˳�������ѯ">����</button>&nbsp;&nbsp;<button onclick="history.back()" class="button" title="������һҳ">����</button></form></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<!-- �����б� begin -->
<form name="mainform">
<?php echo $t->show(); ?>
</form>
<!-- �����б� end -->

<div class="space"></div>

<!-- ��ҳ���� begin -->
<div class="footer_op">
	<div class="footer_op_left"><button onclick="select_all()" class="button">ȫѡ</button>&nbsp;<button onclick="unselect()" class="button">��ѡ</button>&nbsp;<?php echo $power->show_button("close,delete"); ?></div>
	<div class="footer_op_right"><?php echo $pagelink; ?></div>
</div>
<!-- ��ҳ���� end -->

</body>
</html>