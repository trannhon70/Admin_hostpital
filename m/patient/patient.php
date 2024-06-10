<?php
/*
// - ����˵�� : �����б�
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2013-05-01 05:09
*/
require "../../core/core.php";
$mod = "patient";
$table = "patient_".$user_hospital_id;

if ($user_hospital_id == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

// ��ɫ���� 2010-07-31
$line_color = array('black', 'red', 'silver', '#8AC6C6', '#8000FF');
$line_color_tip = array("�ȴ�", "�ѵ�", "δ��", "����", "�ط�");
$area_id_name = array(0 => "δ֪", 1 => "����", 2 => "���");

// �����Ĵ���:
if ($op = $_GET["op"]) {
	include "patient.op.php";
}

include "patient.list.php";

?>