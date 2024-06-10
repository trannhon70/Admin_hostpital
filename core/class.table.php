<?php
/*
// ˵��: ���ɴ���ͷ���б�����
// ����: ��ҽս�� 
// ʱ��: 2010-07-12
*/

class table {
	var $heads = array();
	var $lines = array();

	// Ĭ������:
	var $default_sort = '';
	var $default_order = '';

	// ��ǰ����
	var $sort = '';
	var $order = '';

	// �������:
	var $baselink = ''; //���趨��Ĭ�ϵ�ǰҳ��
	var $param = array();

	// ��ʾ����:
	var $base_indent = ""; //��ʼ����
	var $one_indent = "\t"; //ÿ������

	// ��ʾ�����õ�css��:
	var $table_class = "list";

	// ������:
	var $icons = array('asc'=>'/res/img/icon_down.gif', 'desc'=>'../res/img/icon_up.gif');
	var $order_tips = array("ori"=>"ȡ������Ŀ����", "asc"=>"���������������", "desc"=>"���������������");


	// ���ʼ��:
	function table() {
		// nothing todo
	}

	// �趨����ͷ�����Լ�Ĭ������:
	function set_head($head_array = array(), $default_sort = '', $default_order = '') {
		$this->heads = $head_array;
		if ($default_sort) {
			$this->default_sort = $default_sort;
		}
		$default_order = strtolower($default_order);
		if (in_array($default_order, array("", "asc", "desc"))) {
			$this->default_order = $default_order;
		}
		return true;
	}


	// ���õ�ǰ����:
	function set_sort($sort = '', $order = '') {
		if ($sort && $this->heads && array_key_exists($sort, $this->heads)) {
			$this->sort = $sort;
		}
		$order = strtolower($order);
		if (in_array($order, array("", "asc", "desc"))) {
			$this->order = $order;
		}
		return true;
	}


	// ����һ������:
	function add($line = array()) {
		$this->lines[] = $line;
	}

	function add_tip_line($title) {
		$this->lines[] = $title;
	}

	// ��ʾ���;
	function show() {
		$s = $this->base_indent.'<table class="'.$this->table_class.'" width="100%">'."\r\n";
		$s .= $this->show_head();
		$s .= $this->show_lines();
		$s .= $this->base_indent.'</table>'."\r\n";

		return $s;
	}

	// ���ɱ���ͷ��:
	function show_head() {
		if (!$this->heads) return '';

		$s = '';
		$s .= $this->base_indent.$this->one_indent.'<tr class="head">'."\r\n";
		foreach ($this->heads as $k => $v) {
			$ind = $this->base_indent.$this->one_indent.$this->one_indent;
			$s .= $ind.'<td'.($v["align"] ? (' align="'.$v["align"].'"') : '').
				($v["width"] ? (' width="'.$v["width"].'"') : '')
				.($v["color"] ? (' style="color:'.$v["color"].'"') : '')
				.'>';
			if ($v["sort"] == '') { // ��ʹ�ô�������
				$s .= $k;
			} else {
				// δ���뵱ǰ����:
				if ($this->sort != $k && $this->default_sort != $k) {
					$next_sort = $k;
					$next_tip_order = $next_order = $v["order"];
				} else { //��ǰ����
					$order = ($this->sort == $k) ? $this->order : $this->default_order;
					$ori_order = ($this->sort == $k && $this->sort != $this->default_sort) ? $v["order"] : $this->default_order;
					if ($order == $ori_order || $this->default_sort == $k) { //Ĭ��˳��
						$next_sort = $k;
						$next_tip_order = $next_order = ($order == "asc") ? "desc" : "asc";
					} else { // ����
						$next_sort = $this->default_sort;
						$next_order = $this->default_order;
						$next_tip_order = 'ori';
					}
				}

				$link = $this->make_link('page sort order', array('sort'=>$next_sort, 'order'=>$next_order));

				$title = $k;
				if ($this->sort == $k || ($this->sort == '' && $this->default_sort == $k)) {
					$order = $this->sort ? $this->order : $this->default_order;
					if ($order == '') $order = 'asc';
					$icon = $this->icons[$order];
					$title .= '<img src="'.$icon.'" width="12" height="12" align="absmiddle" border="0" />';
				}

				$tips = $this->order_tips[$next_tip_order];

				$s .= '<a href="'.$link.'" title="'.$tips.'">'.$title.'</a>';
			}
			$s .= '</td>'."\r\n";
		}

		$s .= $this->base_indent.$this->one_indent.'</tr>'."\r\n";

		return $s;
	}

	// ��ʾ��:
	function show_lines() {
		$s = '';
		if (count($this->lines) > 0) {
			foreach ($this->lines as $k => $v) {
				if (!is_array($v)) {
					$s .= $this->base_indent.$this->one_indent.'<tr class="tip_line">'."\r\n";
					$s .= $this->base_indent.$this->one_indent.$this->one_indent.'<td colspan="'.count($this->heads).'">'.$v.'</td>'."\r\n";
					$s .= $this->base_indent.$this->one_indent.'</tr>'."\r\n";
				} else {
					$s .= $this->base_indent.$this->one_indent.'<tr class="line"'.$v["_tr_"].'>'."\r\n";
					foreach ($this->heads as $m => $n) {
						$ind = $this->base_indent.$this->one_indent.$this->one_indent;
						$s .= $ind.'<td'.($n["align"] ? (' align="'.$n["align"].'"') : '').'>';
						if (array_key_exists($m, $v)) {
							$s .= $v[$m];
						}
						$s .= '</td>'."\r\n";
					}
					$s .= $this->base_indent.$this->one_indent.'</tr>'."\r\n";
				}
			}
		} else {
			// nodata
			$s .= $this->base_indent.$this->one_indent.'<tr class="nodata">'."\r\n";
			$s .= $this->base_indent.$this->one_indent.$this->one_indent.'<td colspan="'.count($this->heads).'">(��������)</td>'."\r\n";
			$s .= $this->base_indent.$this->one_indent.'</tr>'."\r\n";
		}

		return $s;
	}

	// ���Ӵ���:
	function make_link($not_used_var='', $used_array = array()) {
		$p = $this->param;

		if (trim($not_used_var)) {
			$not_used_vars = explode(' ', $not_used_var);
			foreach ($not_used_vars as $v) {
				$v = trim($v);
				if ($v && array_key_exists($v, $p)) unset($p[$v]);
			}
		}

		$p = array_merge($p, $used_array);

		$r = array();
		foreach ($p as $k => $v) {
			$r[] = $k."=".urlencode($v);
		}

		$url = $this->baselink;
		if (substr_count($url, "?") == 0) {
			$url .= '?';
		}
		$url .= implode("&", $r);


		return $url;
	}

}


?>