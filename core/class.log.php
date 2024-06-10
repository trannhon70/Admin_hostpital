<?php

/*

// - ����˵�� : ��־��¼ϵͳ

// - �������� : ��ҽս�� 

// - ����ʱ�� : 2013-05-10 => 2013-05-23

*/



class log {

	var $close = 0; // 0-��¼��־, 1-�ر���־ϵͳ

	var $table = "sys_log";

	var $outtime = 30; //����ʱ�䣬���죬���Զ�ɾ��������־



	function add($type='login|delete|add|edit|...', $title, $data='', $table_name='', $view_url='') {

		global $debug_mode;

		if ($this->close || $debug_mode) return false;

		global $db, $uid, $username, $table;


		$r = array();
		$r["type"] = $type;
		$r["title"] = $title;
		$r["pagename"] = $_SERVER["REQUEST_URI"];
		$r["view_url"] = $view_url;
		$r["data"] = is_array($data) ? serialize($data) : $data;
		$r["table_name"] = $table_name ? $table_name : ("(".$table.")");
		$r["username"] = $username;

		$r["uid"] = $uid;

		$r["ip"] = get_ip();

		$r["addtime"] = time();



		$sqldata = $db->sqljoin($r);

		$res = $db->query("insert into ".$this->table." set $sqldata");

		if (!$res) exit($db->sql);





		// ������־ɾ��:

		if (mt_rand(1, 1000) <= 10) {

			$this->log_clear();

		}

		return $res;

	}


	function log_clear() {

		global $db;

		if ($this->outtime > 0) {

			$outtime = strtotime("-".intval($this->outtime)." days");

			$db->query("delete from ".$this->table." where addtime<$outtime");

		}

	}

}

?>