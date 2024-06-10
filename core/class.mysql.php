<?php

/*

mysql class by yangming,��ҽս��

����ʱ�䣺2007-6-15 17:07



���� 2007-6-16 12:20 select �����Ϊ1��ʱ��

���� 2007-8-29 13:30 ���Ӻ���get_count,select_db����ͬʱ���Ӳ����������������ϵ�

		���ݿ�

���� 2007-05-29 15:02 ���Ӷ�charset�Ĵ������������ʱδָ����������� charset����

		��ʹ��charset���ã����ָ���ˣ���ʹ��֮����������˿�ֵ����ʹ��Ĭ�ϵ�charset����

���� 2007-05-29 15:57 �޸���һЩ����Ĭ�ϲ������Լ��Դ���Ĵ������������˴��ݱ�����

���� 2007-12-15 14:40 �޸�����ļ�����Ա���������ԣ�select, upate,insert�Ⱥ���ֻ

		��������sql��䣬ִ����ͨ��ͳһ�Ľӿ�query()���� - ��ҽս��

���� 2013-07-31 23:19 ���Ӻ��� query_key,�Լ��ڶԴ��͵ĸ��ӵ���Ŀ��

		��Ҫ�����������յ���� - ��ҽս��

���� 2013-09-05 09:05 �޸ĺ��� query() ʹ��֧�ָ��������ѯ,����ԭ query_key �е�

		���� - ��ҽս��

���� 2013-09-06 11:24 ���ݲ�Ӧ��Ϊ��Ȿ����һ����,����,��������,���ⲿ�����������

���� 2013-09-21 01:01 ��¼����ѯ������־���챣��Ϊ�ļ�

���� 2013-04-25 14:16 ɾ�������ú���

*/



// $mysql_server = array('���ݿ��ַ', '�û���', '����', '���ݿ���', '����');



// $db = new mysql($mysql_server); //�������Ӳ���

// $db = new mysql(array('���ݿ��ַ', '�û���', '����', '���ݿ���', '����')); //�ڽ�����

class mysql {

	var $host='localhost';

	var $user='root';

	var $pwd='';

	var $dbname='crm.dakhoahcm.vn';

	var $charset = 'gbk';

	var $dblink;

	var $result;

	var $sql = '';

	var $show_error = 1; //�Ƿ���ʾ����

	var $error = '';

	var $slow_query = 0; //����ѯ����,����Ϊ0��ʾ����¼

	var $slow_query_path = ""; // ��¼��־�ļ���Ŀ¼



	// ���ʼ��

	function mysql($mysql_server = array()) {
		
		if (!$mysql_server) {

			global $mysql_server;

		}

		list($host, $user, $pwd, $dbname, $charset) = $mysql_server;
		// var_dump($host, $user, $pwd, $dbname, $charset);


		if (!@$this->connect($host, $user, $pwd, $dbname, $charset)) {

			exit('mysql error: connect failed, please check the connect parameters.');

		}

	}



	// ����mysql����:

	function connect($host, $user, $pwd, $dbname, $charset = '') {

		list($this->host, $this->user, $this->pwd, $this->dbname) = array($host, $user, $pwd, $dbname);

		if (isset($charset)) {

			$this->charset = $charset;

		}
		if ($this->host && $this->user) {

			if (!$this->dblink = @mysql_pconnect($host, $user, $pwd, true)) {

				$this->error();

				return false;

			}


			if ($this->dbname) {

				$this->select_db($this->dbname);

			}

			if ($this->charset) {

				@mysql_query("SET NAMES '".$this->charset."'", $this->dblink);

			}

			@mysql_query("SET sql_mode=''");

			return true;

		} else {

			exit('mysql error: connect parameters not enough.');

		}

	}



	// ѡ�� db

	function select_db($dbname) {

		if(@mysql_select_db($dbname,$this->dblink)) {

			$this->dbname = $dbname;

			return true;

		} else {

			exit("mysql error: the database '{$dbname}' not exists.");

		}

	}



	// ������ϳ� sql �������ݸ�ʽ

	function sqljoin($data) {

		$data_array = array();

		foreach ($data as $k => $v) {

			$k = trim($k, "`");

			$data_array[] = "`$k`='{$v}'";

		}

		return implode(",", $data_array);

	}



	// query() ������ 2007-12-17 10:29 ��ҽս��

	// 2013-09-05 00:22 �޸�: ���Ӳ���

	// ���÷��������������ɺ��ԣ���ͬһ���ѯ�������������

	// $return_count_or_key_field ��������֣��򷵻ظ�����ָ�������;

	// �����һ���ִ�����Ϊ���ؽ���ļ���;

	// $value_field �Ƿ��ؽ���еļ�ֵ�������ָ����Ĭ�ϣ������ز�ѯ����ȫ���ֶ�.

	// ���������������鼴֪���ڴ����б��У���ϴ˺����ĺ������������䷽��

	// ԭʵ��Ϊ query_key, Ч���Բ����������ѭ����

	// ����������

	// ��ѯ��һ�����: $item = $db->query("select * from user order by addtime desc", 1);

	// ֱ�ӷ���һ��ֵ�� $username = $db->query("select name from user where uid=3", 1, "name");

	// ��ѯ����һ���������飺 $prod_list = $db->query("select id,prod_name,prod_pic from product where views>10000", "id");

	// ��ѯһ����������: $uid_to_name = $db->query("select uid,username from user", "uid", "username");

	// ����ֵ����һ������: $ids = $db->query("select id from product where views>10000", "", "id");

	// last modify by weelia @ 2013-09-05 00:42

	function query($sql, $return_count_or_key_field = '', $value_field = '') {

		$this->sql = trim($sql);



		// ������ѯ������,����sql��һ���� insert select update delete ...

		list($query_type, $other) = explode(' ', $this->sql, 2);

		$query_type = strtolower($query_type); //ͳһΪСд



		// ����ѯ����ʼ:

		if ($query_type == "select" && $this->slow_query > 0) {

			$begin_time = $this->now_time();

		}



		// ִ�в�ѯ:

		$this->result = @mysql_query($this->sql, $this->dblink);



		// ��¼����ѯ?

		if ($query_type == "select" && $this->slow_query > 0) {

			$end_time = $this->now_time();

			if ($end_time - $begin_time > $this->slow_query) {

				$this->log_slow_query($end_time - $begin_time);

			}

		}



		// ��������:

		if (!$this->result) {

			$this->error();

			return false;

		}



		// ��ѯ�������:

		if ($query_type == "select" || $query_type == "show") {

			// �Բ��� return_count_or_key_field �Ĵ���(�ж���Ϊ��ֵ���ʾ��ѯ��������,�����ʾ��������Ҫʹ�õļ���)

			if ($return_count_or_key_field !== "") {

				if (is_numeric($return_count_or_key_field)) {

					$return_count = $return_count_or_key_field;

				} else {

					$key_field = $return_count_or_key_field;

				}

			}



			// select ���:

			$rs = array();

			while ($row = @mysql_fetch_assoc($this->result)) {

				if ($return_count == 1) {

					return $value_field ? $row[$value_field] : $row;

				}

				if ($key_field) {

					$rs[$row[$key_field]] = $value_field ? $row[$value_field] : $row;

				} else {

					$rs[] = $value_field ? $row[$value_field] : $row;

				}

			}

			if ($return_count == 1 && $value_field != '') {

				return false;

			}

			return $rs;

		} elseif ($query_type == "insert") {

			return @mysql_insert_id($this->dblink);

		}



		// ������ѯ��������ȷִ�о����سɹ�:

		return true;

	}



	// ��ѯ����ȡ������еĵ�һ������

	function query_first($sql) {

		return $this->query($sql, 1);

	}



	function query_count($sql) {

		return $this->query($sql, 1, "count(*)");

	}



	// ��ѯ��($table)��,�ֶ�($field)��ֵΪ$value�ļ�¼������������һ���ֶ�($need_field)��ֵ (weelia@2013-05-01 00:23)

	function lookup($table, $field, $value, $need_field) {

		$tm = $this->query_first("select {$need_field} from {$table} where {$field}='{$value}' limit 1");

		if (is_array($tm) && count($tm) > 0) {

			return $tm[$need_field];

		}

		return false;

	}



	function affected_row() {

		return mysql_affected_rows($this->dblink);

	}



	function make_where($w, $with_where = 1) {

		return count($w) ? ($with_where ? "where " : "").implode(" and ", $w) : "";

	}



	function make_sort($heads, $sort='', $order='', $default_sort='', $default_order='') {

		$s = '';

		if ($sort && array_key_exists($sort, $heads) && $heads[$sort]["sort"]) {

			$s = $heads[$sort]["sort"];

			if (in_array(strtolower($order), array('', 'asc', 'desc'))) {

				$s .= " ".$order;

			}

		} else {

			if ($default_sort && array_key_exists($default_sort, $heads) && $heads[$default_sort]["sort"]) {

				$s = $heads[$default_sort]["sort"];

				if (in_array(strtolower($default_order), array('', 'asc', 'desc'))) {

					$s .= " ".$default_order;

				}

			}

		}

		if ($s != '') {

			$s = "order by ".$s;

		}



		return $s;

	}



	// ��ȡ��ǰʱ��:

	function now_time() {

		list($usec, $sec) = explode(" ", microtime());

		return ((float)$usec + (float)$sec);

	}



	// ��¼���ٲ�ѯsql���ļ�

	function log_slow_query($sql_running_time = 0) {

		$log_filename = $this->slow_query_path."mysql_slow_query_".date("Ymd").".log";



		$time = date("Y-m-d H:i:s");

		$pagename = $_SERVER["PHP_SELF"];

		$sql = $this->sql;

		$aff_rows = @mysql_affected_rows($this->dblink);



		$s = $time." ".$pagename."\n".sprintf("[%8s] [%8s] ", round($sql_running_time, 3), $aff_rows).$sql."\n\n";



		if ($handle = @fopen($log_filename, "a+")) {

			fwrite($handle, $s);

			fclose($handle);

			return true;

		}



		return false;

	}



	// ��ʾ����

	function error() {

		if (!$this->show_error) return;

		$this->error = '<br />';

		if ($this->dblink) $this->error .= @mysql_error($this->dblink).'<br />';

		if ($this->sql) $this->error .= $this->sql.'<br />';

		echo $this->error;

	}

}

?>