<?php
/*
// ˵��: �����б�
// ����: ��ҽս�� 
// ʱ��: 2010-09-07 11:01
*/


if ($_GET["btime"]) {
	$_GET["begin_time"] = strtotime($_GET["btime"]." 0:0:0");
}
if ($_GET["etime"]) {
	$_GET["end_time"] = strtotime($_GET["etime"]." 23:59:59");
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$link_param = explode(" ", "page sort order key begin_time end_time time_type show come kefu_23_name kefu_4_name doctor_name xiaofei disease part_id from depart names date list_huifang media");

$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}
extract($param);



// ���嵥Ԫ���ʽ:
if($_SESSION[$cfgSessionName]["part_id"]==11){
	$list_heads = array(
		"����" => array("width"=>"50", "align"=>"center", "sort"=>"name", "order"=>"asc"),
		"�Ա�" => array("align"=>"center", "sort"=>"sex", "order"=>"asc"),
		"����" => array("align"=>"center", "sort"=>"age", "order"=>"asc"),
		"�绰" => array("align"=>"center", "sort"=>"tel", "order"=>"asc"),
		//"QQ" => array("align"=>"center", "sort"=>"qq", "order"=>"asc"),
		"ר�Һ�" => array("align"=>"center", "sort"=>"zhuanjia_num", "order"=>"asc"),
		"�Ӵ�" => array("align"=>"center", "sort"=>"jiedai", "order"=>"asc"),
		"ԤԼʱ��" => array("width"=>"70", "align"=>"center", "sort"=>"order_sort", "order"=>"desc"),
		"����" => array("align"=>"center", "sort"=>"remain_time", "order"=>"desc"),
		"��������" => array("align"=>"center", "sort"=>"disease_id", "order"=>"asc"),
		"ý����Դ" => array("align"=>"center", "sort"=>"media_from", "order"=>"asc"),
		//"�ؼ���" => array("width"=>"80","align"=>"center", "sort"=>"engine_key", "order"=>"asc"),
		"����" => array("align"=>"center", "sort"=>"part_id", "order"=>"asc"),
		"����" => array("align"=>"center", "sort"=>"depart", "order"=>"asc"),
		"����" => array("align"=>"center", "sort"=>"is_local", "order"=>"asc"),
		"�ͷ�" => array("width"=>"50", "align"=>"center", "sort"=>"author", "order"=>"asc"),
		"�ط�" => array("width"=>"24", "align"=>"center", "sort"=>"huifang", "order"=>"desc"),
		"��Լ���" => array("align"=>"center", "sort"=>"status_1", "order"=>"desc", "sort2"=>"addtime desc"),
		"����ʱ��" => array("width"=>"70", "align"=>"center", "sort"=>"addtime", "order"=>"desc"),
		"����" => array("width"=>"80", "align"=>"center"),
	);
}
else
{
	$list_heads = array(
		"����" => array("width"=>"50", "align"=>"center", "sort"=>"name", "order"=>"asc"),
		"�Ա�" => array("align"=>"center", "sort"=>"sex", "order"=>"asc"),
		"����" => array("align"=>"center", "sort"=>"age", "order"=>"asc"),
		"�绰" => array("align"=>"center", "sort"=>"tel", "order"=>"asc"),
		//"QQ" => array("align"=>"center", "sort"=>"qq", "order"=>"asc"),
		"ר�Һ�" => array("align"=>"center", "sort"=>"zhuanjia_num", "order"=>"asc"),
		//"��ѯ����" => array("align"=>"left", "sort"=>"content", "order"=>"asc"),
		"�Ӵ�" => array("align"=>"center", "sort"=>"jiedai", "order"=>"asc"),
		"ԤԼʱ��" => array("width"=>"70", "align"=>"center", "sort"=>"order_sort", "order"=>"desc"),
		"����" => array("align"=>"center", "sort"=>"remain_time", "order"=>"desc"),
		"��������" => array("align"=>"center", "sort"=>"disease_id", "order"=>"asc"),
		"ý����Դ" => array("align"=>"center", "sort"=>"media_from", "order"=>"asc"),
		// "�ؼ���" => array("width"=>"80","align"=>"center", "sort"=>"engine_key", "order"=>"asc"),
		"����" => array("width" => "80","align"=>"center", "sort"=>"engine_key", "order"=>"asc"),
		"����" => array("align"=>"center", "sort"=>"depart", "order"=>"asc"),
		"����" => array("align"=>"center", "sort"=>"is_local", "order"=>"asc"),
		"��ע" => array("align"=>"center", "sort"=>"memo", "order"=>"asc"),
		"�ͷ�" => array("width"=>"50", "align"=>"center", "sort"=>"author", "order"=>"asc"),
		"�ط�" => array("width"=>"24", "align"=>"center", "sort"=>"huifang", "order"=>"desc"),
		"��Լ���" => array("align"=>"center", "sort"=>"status_1", "order"=>"desc", "sort2"=>"addtime desc"),
		"����ʱ��" => array("width"=>"70", "align"=>"center", "sort"=>"addtime", "order"=>"desc"),
		"����" => array("width"=>"80", "align"=>"center"),
	);
}

// Ĭ������ʽ:
if ($uinfo["part_id"] == 4) {
	$default_sort = "ԤԼʱ��"; // ��ҽ�ȽϹ�ע���쵽�Ĳ���
	$default_order = "desc";
} else {
	$default_sort = "����ʱ��"; //�ͷ������Ա���ע�����������˶��ٲ���
	$default_order = "desc";
}

if ($show == 'today') {
	$begin_time = mktime(0, 0, 0);
	$end_time = mktime(23, 59, 59);
} else if ($show == 'yesterday') {
	$begin_time = mktime(0, 0, 0) - 24 * 3600;
	$end_time = mktime(0, 0, 0);
} else if ($show == "thismonth") {
	$begin_time = mktime(0,0,0,date("m"),1);
	$end_time = mktime("+1 month", $begin_time);
} else if ($show == "lastmonth") {
	$end_time = mktime(0,0,0,date("m"),1);
	$begin_time = strtotime("-1 month", $end_time);
}

// ���������� 2010-09-29:
if ($_GET["date"]) {
	$begin_time = strtotime($_GET["date"]." 0:0:0");
	$end_time = strtotime($_GET["date"]." 23:59:59");
}


// �б���ʾ��:
$t = load_class("table");
$t->set_head($list_heads, $default_sort, $default_order);
$t->set_sort($_GET["sort"], $_GET["order"]);
$t->param = $param;
$t->table_class = "new_list";


// ������ʼ:
$where = array();

if ($key = trim(stripslashes($key))) {
	$sk = "%{$key}%";
	$fields = explode(" ", "name tel qq zhuanjia_num content memo");
	$sfield = array();
	foreach ($fields as $_tm) {
		$sfield[] = "binary $_tm like '{$sk}'";
	}
	$where[] = "(".implode(" or ", $sfield).")";
}

// ��ȡȨ��:
$today_where = '';

if (!$debug_mode) {
	$read_parts = get_manage_part(); //�����Ӳ��ţ���ͬ����������)
	if ($uinfo["part_admin"] || $uinfo["part_manage"]) { //���Ź���Ա�����ݹ���Ա
		$where[] = "(part_id in (".$read_parts.") or binary author='".$realname."')";
	} else { //��ͨ�û�ֻ��ʾ�Լ�������
		$where[] = "binary author='".$realname."'";
	}
}

// �绰�ط�ֻ��ʾ�ѵ�����:
if ($uinfo["part_id"] == 12) {
	//$where[] = "status=1";
}

$time_type = empty($time_type) ? 'order_date' : $time_type;
if ($begin_time > 0) {
	$where[] = $time_type.'>='.$begin_time;
}
if ($end_time > 0) {
	$where[] = $time_type.'<'.$end_time;
}
if ($come != '') {
	if ($come == 1) {
		$where[] = "status=1";
	} else {
		$where[] = "status in (0,2)";
	}
}
if ($kefu_23_name != '') {
	$where[] = "author='$kefu_23_name'";
}
if ($kefu_4_name != '') {
	$where[] = "jiedai='$kefu_4_name'";
}
if ($doctor_name != '') {
	$where[] = "doctor='$doctor_name'";
}
if ($disease != '') {
	$where[] = "disease_id=$disease";
}
if ($part_id != '') {
	$where[] = "part_id=$part_id";
}
if ($depart != '') {
	$where[] = "depart=$depart";
}
if ($list_huifang) {
	$where[] = "huifang like '%[".$realname."]%'";
}
if ($media) {
	$where[] = "media_from='".trim($media)."'";
}

$sqlwhere = $db->make_where($where);
$sqlsort = $db->make_sort($list_heads, $sort, $order, $default_sort, $default_order);


if ($come == 3)
{
	if($_GET["key"]=="")
	{
		$sqlwhere=str_replace("status in (0,2)","status=3",$sqlwhere);
	}
}
elseif($come == "")
{
	if($_GET["key"]=="")
	{
		$sqlwhere = $sqlwhere." and status<>-1";
	}
}

// ��ҳ����:
$count = $db->query("select count(*) as count from $table $sqlwhere $sqlgroup", 1, "count");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$time = time();
$today_begin = mktime(0,0,0);
$today_end = $today_begin + 24 * 3600;
$list_data = $db->query("select *,(order_date-$time) as remain_time, if(order_date<$today_begin, 1, if(order_date>$today_end, 2, 3)) as order_sort, if(status=1,2, if(status=2,1,0)) as status_1 from $table $sqlwhere $sqlgroup $sqlsort limit $offset,$pagesize");
$s_sql = $db->sql;

//echo "<!--";
//print_r($uinfo);
//echo "-->";


//���ݹ��� 13.1.12
if($uinfo['part_id']==2&&$uinfo['part_admin']!=1){
  foreach($list_data as $key=>$data){
    if($data['author']!=$uinfo['name']&&$data['author']!=$uinfo['realname']){
      $data['tel'] = '--'; 
      $list_data[$key] = $data; 
    }
  }
}elseif($uinfo['part_id']==4){
  foreach($list_data as $key=>$data){
      $data['tel'] = '--'; 
      $list_data[$key] = $data; 
  }  
}


// id => name:
$hospital_id_name = $db->query("select id,name from hospital", 'id', 'name');
$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');
$disease_id_name = $db->query("select id,name from disease", 'id', 'name');
$depart_id_name = $db->query("select id,name from depart where hospital_id=$user_hospital_id", 'id', 'name');

$use_depart = 1;
if (count($depart_id_name) == 0) {
	$use_depart = 0;
	unset($list_heads["����"]); //û�п���
}


// ������ͳ������ 2013-05-13 16:46
$res_report = '';
//if ($_GET["from"] == "search") {
	$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status=1") : "where status=1";
	$count_come = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");

	$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status!=1") : "where status!=1";
	$count_not = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");
	//echo "<br>".$db->sql;

	$count_all = $count_come + $count_not;

	$res_report = "�ܹ�: <b>".$count_all."</b> &nbsp; �ѵ�: <b>".$count_come."</b> &nbsp; δ��: <b>".$count_not."</b>";
//}

// ͳ�ƽ�������:
$t_time_type = "order_date";

$today_where = ($today_where ? ($today_where." and") : "")." $t_time_type>=".$today_begin;
$today_where .= " and $t_time_type<".$today_end;
$sqlwhere_s = "where ".($today_where ? ($today_where." and status=1") : "status=1");
$count_today_come = $db->query("select count(*) as count from $table $sqlwhere_s order by id desc", 1, "count");

$sqlwhere_s = "where ".($today_where ? ($today_where." and status!=1") : "status!=1");
$count_today_not = $db->query("select count(*) as count from $table $sqlwhere_s order by id desc", 1, "count");

$count_today_all = $count_today_come + $count_today_not;

$today_report = "<a href='?show=today'>�ܹ�: <b>".$count_today_all."</b></a> &nbsp; <a href='?show=today&come=1'>�ѵ�: <b>".$count_today_come."</b></a> &nbsp; <a href='?show=today&come=0'>δ��: <b>".$count_today_not."</b></a>&nbsp;";

// ��������ͳ��(����):
if (in_array($uinfo["part_id"], array(2,3))) {
	$basewhere = "part_id=".$uinfo["part_id"];
	$part_today_all = $db->query("select count(*) as count from $table where $basewhere and order_date>=$today_begin and order_date<$today_end", 1, "count");
	$part_today_come = $db->query("select count(*) as count from $table where $basewhere and order_date>=$today_begin and order_date<$today_end and status=1", 1, "count");
	$part_today_not = $part_today_all - $part_today_come;

	$part_report = "�ܹ�: <b>".$part_today_all."</b>  �ѵ�: <b>".$part_today_come."</b>  δ��: <b>".$part_today_not."</b>&nbsp;";
}


// ���б����ݷ���:
if ($sort == "����ʱ��" || ($sort == "" && $default_sort == "����ʱ��")) {
	if ($order == "desc" || $default_order == "desc") {
		$today_begin = mktime(0,0,0);
		$today_end = $today_begin + 24*3600;
		$yesterday_begin = $today_begin - 24*3600;

		$list_data_part = array();
		
		foreach ($list_data as $line) {
			if ($line["addtime"] < $yesterday_begin) {
				$list_data_part[3][] = $line;
			} else if ($line["addtime"] < $today_begin) {
				$list_data_part[2][] = $line;
			} else if ($line["addtime"] < $today_end) {
				$list_data_part[1][] = $line;
			}
		}

		$list_data = array();
		if (count($list_data_part[1]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[1])."]");
			$list_data = array_merge($list_data, $list_data_part[1]);
		}
		if (count($list_data_part[2]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[2])."]");
			$list_data = array_merge($list_data, $list_data_part[2]);
		}
		if (count($list_data_part[3]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"ǰ������ [".count($list_data_part[3])."]");
			$list_data = array_merge($list_data, $list_data_part[3]);
		}
		unset($list_data_part);
	}
} else if ($sort == "��Լ���" || ($sort == "" && $default_sort == "��Լ���")) {
	$list_data_part = array();
	foreach ($list_data as $line) {
		if ($line["status_1"] == 2) { //�ѵ�
			$list_data_part[1][] = $line;
		} else if ($line["status_1"] == 1) { //δ��
			$list_data_part[2][] = $line;
		} else if ($line["status_1"] == 0) { //�ȴ�
			$list_data_part[3][] = $line;
		}
	}

	$list_data = array();
	if (count($list_data_part[1]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"�ѵ� (�Ѹ�Լ) [".count($list_data_part[1])."]");
		$list_data = array_merge($list_data, $list_data_part[1]);
	}
	if (count($list_data_part[2]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"δ�� (ȷ�ϲ��ḰԼ) [".count($list_data_part[2])."]");
		$list_data = array_merge($list_data, $list_data_part[2]);
	}
	if (count($list_data_part[3]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"�ȴ� (��δ��Լ�������ܻḰԼ) [".count($list_data_part[3])."]");
		$list_data = array_merge($list_data, $list_data_part[3]);
	}
	unset($list_data_part);

} else if ($sort == "ý����Դ" || ($sort == "" && $default_sort == "ý����Դ")) {
	$list_data_part = array();
	foreach ($list_data as $line) {
		if ($line["media_from"] == "����") {
			$list_data_part[1][] = $line;
		} else if ($line["media_from"] == "�绰") {
			$list_data_part[2][] = $line;
		} else {
			$list_data_part[3][] = $line;
		}
	}

	$list_data = array();
	if (count($list_data_part[1]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[1])."]");
		$list_data = array_merge($list_data, $list_data_part[1]);
	}
	if (count($list_data_part[2]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"�绰 [".count($list_data_part[2])."]");
		$list_data = array_merge($list_data, $list_data_part[2]);
	}
	if (count($list_data_part[3]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[3])."]");
		$list_data = array_merge($list_data, $list_data_part[3]);
	}
	unset($list_data_part);
} else if ($sort == "ԤԼʱ��" || ($sort == "" && $default_sort == "ԤԼʱ��")) {
	$today_begin = mktime(0,0,0);
	$today_end = $today_begin + 24*3600;
	$yesterday_begin = $today_begin - 24*3600;

	$list_data_part = array();
	foreach ($list_data as $line) {
		if ($line["order_date"] < $yesterday_begin) {
			$list_data_part[1][] = $line;
		} else if ($line["order_date"] < $today_begin) {
			$list_data_part[2][] = $line;
		} else if ($line["order_date"] < $today_end) {
			if ($line["status"] == 0) {
				$list_data_part[31][] = $line;
			} else if ($line["status"] == 1) {
				$list_data_part[32][] = $line;
			} else {
				$list_data_part[33][] = $line;
			}
			$list_data_part[3][] = $line;
		} else {
			$list_data_part[4][] = $line;
		}
	}

	$list_data = array();
	if (count($list_data_part[31]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� (�ȴ���) [".count($list_data_part[31])."]");
		$list_data = array_merge($list_data, $list_data_part[31]);
	}
	if (count($list_data_part[32]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� (�ѵ�) [".count($list_data_part[32])."]");
		$list_data = array_merge($list_data, $list_data_part[32]);
	}
	if (count($list_data_part[33]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� (������) [".count($list_data_part[33])."]");
		$list_data = array_merge($list_data, $list_data_part[33]);
	}
	if (count($list_data_part[4]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"������Ժ� (ʱ��δ��) [".count($list_data_part[4])."]");
		$list_data = array_merge($list_data, $list_data_part[4]);
	}
	if (count($list_data_part[2]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[2])."]");
		$list_data = array_merge($list_data, $list_data_part[2]);
	}
	if (count($list_data_part[1]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"ǰ������ [".count($list_data_part[1])."]");
		$list_data = array_merge($list_data, $list_data_part[1]);
	}
	unset($list_data_part);
}

$back_url = make_back_url();

// ��������:
foreach ($list_data as $li) {
	$id = $li["id"];
	if ($id == 0) {
		$t->add_tip_line($li["name"]);
	} else {
		$r = array();
		$r["����"] = $li["name"].($li["status"] == 1 ? '<br><a href="javascript:;" onclick="alert(this.title)" title="�ѵ�Ժ">��</a>' : "");
		$r["�Ա�"] = $li["sex"];
		$r["����"] = $li["age"] > 0 ? $li["age"] : "";
		//$r["�绰"] = ec($li["tel"], "DECODE", md5($encode_password));
		if ($uinfo["show_tel"] == 1 || $li["author"] == $username) {
			$r["�绰"] = $li["tel"];
		} else {
			$r["�绰"] = "-";
		}
		$r["QQ"] = $li["qq"];
		$r["ר�Һ�"] = $li["zhuanjia_num"];
		$r["��ѯ����"] = cut($li["content"], 22, "��");
		$r["�Ӵ�"] = noe($li["doctor"], "");
		$r["ԤԼʱ��"] = str_replace('|', '<br>', @date("Y-m-d|H:i", $li["order_date"]));
		$r["����"] = ($li["order_date"]-time() > 0 ? ceil(($li["order_date"]-time())/24/3600) : '0');

		$dis_text = array();
		foreach (explode(",", $li["disease_id"]) as $dis_id) {
			if ($dis_id > 0) $dis_text[] = $disease_id_name[$dis_id];
		}
		$r["��������"] = implode("|", $dis_text);
		$r["ý����Դ"] = $li["media_from"];
		$r["�ؼ���"] = $li["engine_key"];
		$r["����"] = $part_id_name[$li["part_id"]];
		$r["����"] = $li["depart"] > 0 ? $depart_id_name[$li["depart"]] : "";
		$r["����"] = $li["is_local"] == 2 ? $li["area"] : $area_id_name[$li["is_local"]];
		$r["��ע"] = cut($li["memo"], 22, "��");
		$r["�ͷ�"] = $li["author"]. ($li["edit_log"] ? ("<br><a href='javascript:;' onclick='alert(this.title)' title='".str_replace("<br>", "&#13", strim($li["edit_log"], '<br>'))."' style='color:#8050C0'>��</a>") : '');
		$r["��Լ���"] = $status_array[$li["status"]];
		$r["�ط�"] = $li["huifang"] != '' ? ('<a href="javascript:;" onclick="alert(this.title)" title="'.trim(strip_tags($li["huifang"])).'">��</a>') : '';
		$r["����ʱ��"] = str_replace('|', '<br>', @date("Y-m-d|H:i", $li["addtime"]));

		// var_dump($r);
		// ����:
		$op = array();
		if (check_power("view")) {
			$op[] = "<a href='?op=view&id=$id' class='op'><img src='/res/img/b_detail.gif' width='16' height='16' align='absmiddle' alt='�鿴����'></a>";
		}
		// �ͷ�û���޸�Ȩ�ޣ���ҽ�����ϴ�����Ϻ��Ҹ���û���޸�Ȩ�ޣ�����Ա��ҽԺ����Ա���޸�Ȩ��
		$can_edit = 0;
		if ($uinfo["part_id"] == 2) { //����ͷ�
			$can_edit = 1; 
			//if ($li["author"] == $realname) {
				//$can_edit = 1; //�������Լ����ӵĲ����޸�
			//}
		} else if ($uinfo["part_id"] == 3) { //�绰�ͷ�
			$can_edit = 1; //�绰�ͷ������طã�����ʼ���ܽ����޸ģ�����Ȩ�����޸��п���
		} else {
			$can_edit = 1;
		}
		if ((check_power("edit") && $can_edit && $_SESSION[$cfgSessionName]["part_id"]!=11) || $debug_mode) {//111111111111111111111111111
			$op[] = "<a href='?op=edit&id=$id&go=back' class='op'><img src='/res/img/b_edit.gif' width='16' height='16' align='absmiddle' alt='�޸�'></a>";
		}
		//�ж�ɾ��Ȩ��:
		$can_delete = 0;
		if (check_power("delete")) {
			// �����ύ�߱��ˣ���û���޸ĵ�����£�����ɾ��
			if ($li["author"] == $realname) {
				if ($li["status"] == 0 && $line["edit_log"] == '') {
					$can_delete = 1;
				}
			} else {
				// ���Ǳ��ˣ�����ǹ���Ա�Ļ����Ҿ���ɾ��Ȩ�ޣ�����ɾ��:
				if (in_array($uinfo["part_id"], array(1,9)) || $uinfo["part_admin"]) {
					$can_delete = 1;
				}
			}
		}
		if ($can_delete == 1 || $debug_mode) {
			$op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'><img src='/res/img/b_delete.gif' width='16' height='16' align='absmiddle' alt='ɾ��'></a>";
		}
		$r["����"] = implode(" ", $op);

		// �и�������;
		$_tr = ' id="#'.$li["id"].'"';
		$color_status = $li["status"];
		if ($color_status == 0 && date("Ymd", $li["order_date"]) < date("Ymd")) {
			$color_status = 3;
		}
		if ($color_status == 0 && $li["huifang"] != '') {
			$color_status = 4;
		}
		$color = $line_color[$color_status];

		// 2010-12-17 �޸ģ�������֮��Ĳ��ˣ���ɫ��һ��
		if ($li["order_date"] > strtotime("+2 month")) {
			$color = "#FF00FF";
		}

		$_tr .= ' style="color:'.$color.'"';
		//$_tr .= ' onmouseover="mi(this)" onmouseout="mo(this)"';
		$r["_tr_"] = $_tr;

		$t->add($r);
	}
}

$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");
include $mod.".list.tpl.php";

?>