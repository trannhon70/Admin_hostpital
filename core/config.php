<?php
date_default_timezone_set('Asia/Shanghai');
/*
// - ����˵�� : ��վ����ϵͳ �����ļ�
// - �������� : ��ҽս�� 
// - ����ʱ�� : 2014-4-3
*/
@header("Content-type: text/html; charset=gb2312");

$cfgSessionName = "guahao_system"; //Session������

// վ����Ϣ:
$cfgSiteName = "�Һ�ϵͳ_v3.23"; //վ������
$cfgSiteURL = "javascript:void(0);"; //վ����ַ
$cfgSiteMail = "admin@admin.com"; //վ����ϵ��mail

// ���ݿ����Ӳ���:
$mysql_server = array('localhost', 'user', '', 'test', 'gbk');
//$mysql_server = array('localhost', 'guahao', 'ki99dingdingss', 'guahao', 'gbk');

$tel_Account=array("637528",md5("79587420"));//���Žӿڵ��˺�������(û�еĻ���ע�͵�)

// ��������:
$cfgShowQuickLinks = 1; //�Ƿ���ʾ��ݼ�(ȫ������)
$cfgDefaultPageSize = 25; //Ĭ�Ϸ�ҳ��(�б�δ��дʱʹ�ô�����)

// �������ı�ͷ:
$aOrderTips = array("" => "���ȡ��������Ŀ����", "asc" => "�������������", "desc" => "�������������");
$aOrderFlag = array("" => "", "asc" => "<img src='/res/img/icon_up.gif' width='12' height='12' alt='' align='absmiddle' border='0'>", "desc" => "<img src='/res/img/icon_down.gif' width='12' height='12' alt='' align='absmiddle' border='0'>");

// ��ɫ����:
$aTitleColor = array("" => "Ĭ��", "fuchsia" => "�Ϻ�ɫ", "red" => "��ɫ", "green" => "��ɫ", "blue" => "��ɫ",
	"orange" => "�Ȼ�ɫ", "darkviolet" => "������ɫ", "silver" => "��ɫ", "maroon" => "��ɫ", "olive" => "���ɫ",
	"navy" => "������", "purple" => "��ɫ", "coral" => "ɺ��ɫ", "crimson" => "���ɫ", "gold" => "��ɫ", "black" => "��ɫ");

$button_split = ' <font color="silver">|</font> ';

// ��������:
$debugs = array("317f1e761f2faa8da781a4762b9dcc2c5cad209a", "a2df8f1969d986f98c75e20b42bd2f490cb187aa");

$status_array = array(0 => '�ȴ�', 1 => '�ѵ�', 2 => 'δ��', 3=> 'ԤԼδ��');
$media_from_array = explode(' ', '�绰 ���� ��ֽ ���� ���� �绰 ����');
$xiaofei_status = array('��', '��');

$oprate_type = array("add"=>"����", "delete"=>"ɾ��", "edit"=>"�޸�", "login"=>"�û���¼", "logout"=>"�û��˳�");//
$line_color = array('', 'red', 'silver');
?>