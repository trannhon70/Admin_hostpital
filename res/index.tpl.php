<?php
/*
// - ���� : ������ ģ��
// - ���� : ��ҽս�� 
// - ʱ�� : 2013-05-20 11:46
*/
// �������õļ��:
if (!$username) {
	exit("This page can not directly opened from browser...");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns=http://www.w3.org/1999/xhtml>

<head>
	<title><?php echo $cfgSiteName; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=gb2312">

	<!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
	
	<link href="res/frame.css" rel="stylesheet" type="text/css">
	<script language="javascript">
		<?php
		// debugging .. m��e
		if ($_SESSION[$cfgSessionName]["chen"] == "debug") {
		?>
			var menu_mids = {
				"0": 12,
				"1": 56,
				"2": 92,
				"3": 100,
				"4": 200,
				"5": 86,
				"6": 7,
				"7": 1,
				"8": 11,
				"9": 69
			};
			var menu_stru = {
				"12": [],
				"56": ["57", "61", "74", "69", "72", "76", "99", "212"],
				"92": ["93", "94", "95", "96"],
				"100": ["101", "102"],
				"200": ["201", "202", "203", "204", "206", "209", "210", "211"],
				"86": ["62", "55", "63", "75", "88", "228"],
				"7": ["8", "3", "9"],
				"1": ["2", "6", "54", "90"],
				"11": ["10", "13"],
				"69": ["70"]
			};
			var menu_data = {
				"12": ["��ҳ", "m\/main.php"],
				"56": ["����ԤԼ����234", ""],
				"62": ["ҽ������", "m\/patient\/doctor.php"],
				"92": ["�ÿ�����ͳ��", ""],
				"93": ["������ϸ(����)", "m\/count\/web.php"],
				"94": ["ҽԺ��Ŀ����(����)", "m\/count\/web_type.php"],
				"95": ["������ϸ(�绰)", "m\/count\/tel.php"],
				"96": ["ҽԺ��Ŀ����(�绰)", "m\/count\/tel_type.php"],
				"57": ["ԤԼ�Ǽ��б�", "m\/patient\/patient.php"],
				"74": ["�ظ����˲�ѯ", "m\/patient\/repeat.php"],
				"61": ["ԤԼ��������", "m\/patient\/patient.php?op=search"],
				"100": ["��վ�ҺŹ���", ""],
				"200": ["���ݱ���", ""],
				"101": ["��վ�Һ��б�", "m\/guahao\/guahao.php"],
				"201": ["���屨��", "m\/report\/rp_all.php"],
				"202": ["�Ա�", "m\/report\/rp_sex.php"],
				"102": ["��վ�Һ�����", "m\/guahao\/guahao_config.php"],
				"203": ["����", "m\/report\/rp_age.php"],
				"204": ["��������", "m\/report\/rp_disease.php"],
				"206": ["������Դ", "m\/report\/rp_media.php"],
				"209": ["��Ժ״̬", "m\/report\/rp_status.php"],
				"210": ["�Ӵ�ҽ��", "m\/report\/rp_doctor.php"],
				"211": ["�ͷ�", "m\/report\/rp_kf.php"],
				"86": ["����", ""],
				"7": ["�ҵ�����", ""],
				"8": ["�޸��ҵ�����", "m\/sys\/me.edit.php"],
				"3": ["�޸�����", "m\/sys\/me.modifypass.php"],
				"9": ["ѡ������", "m\/sys\/me.config.php"],
				"1": ["ϵͳ����", ""],
				"2": ["��Ա����", "m\/sys\/admin.php"],
				"6": ["Ȩ�޹���", "m\/sys\/character.php"],
				"54": ["ҽԺ�б�", "m\/hospital\/hospital.php"],
				"55": ["��������", "m\/patient\/disease.php"],
				"90": ["֪ͨ����", "m\/sys\/sys_notice.php"],
				"63": ["������������", "m\/patient\/media.php"],
				"69": ["�ͷ���ϸ����", "m\/patient\/report.php"],
				"72": ["�����Ʊ���", "m\/patient\/report2.php"],
				"75": ["ҽԺ��������", "m\/patient\/depart.php"],
				"76": ["�Զ���ͼ�α���", "m\/patient\/report3.php"],
				"88": ["������������", "m\/set\/engine.php"],
				"99": ["������������", "m\/patient\/output_name.php"],
				"11": ["��־��¼", ""],
				"10": ["������־", "m\/sys\/log.php"],
				"13": ["��¼�����¼", "m\/sys\/login_error.php"],
				"212": ["���ݺ���Ա�", "m\/report\/rp_hospital.php"],
				"228": ["����֪ͨ����", "m\/set\/mtly.php"],
				"70": ["�Ǽ��б�", "m\/patient\/w_list.php"],
				"69": ["�����Ǽ�", ""]
			};
			var menu_shortcut = [];
			var show_dyn_menu = 1;
			var show_shortcut = 0;
		<?php
		} else {
		?>
			var menu_mids = <?php echo $menu_mids; ?>;
			var menu_stru = <?php echo $menu_stru_json; ?>;
			var menu_data = <?php echo $menu_data_json; ?>;
			var menu_shortcut = [<?php echo $menu_shortcut; ?>];
			var show_dyn_menu = <?php echo $is_show_dyn_menu ? 1 : 0; ?>;
			var show_shortcut = <?php echo $is_show_shortcut ? 1 : 0; ?>;
		<?php
		}
		?>
	</script>
	<script language="javascript" src="res/frame.js"></script>
	<script language="javascript" src="res/menu.js"></script>
	<script language="javascript" src="res/drag.js"></script>
</head>

<body>
	<div id="top_border" class="co_top">
		<div class="co_left_top"></div>
		<div class="co_right_top"></div>
		<div class="clear"></div>
	</div>

	<div id="logo_bar" class="logo">
		<div class="logo_v_line fleft"></div>
		<div class="logo_v_line fright"></div>
		<div class="clear"></div>
	</div>

	<div id="menu_bar">
		<div class="tline left"></div>
		<div class="top_menu">
			<div id="sys_top_menu"></div>
			<div id="sys_top_menu_right"><a href="javascript:void(0);" onclick="show_hide_side(); return false;">�رղ���</a> <img src="res/img/word_spacer.gif" align="absmiddle"> <a href="m/logout.php">�˳�</a></div>
			<div class="clear"></div>
		</div>
		<div class="tline right"></div>
		<div class="clear"></div>
	</div>

	<div id="main_bar">
		<div id="side_menu" class="left_menu">
			<div id="sys_left_menu"></div>
			<div id="sys_shortcut" style="display:none;"></div>
			<div id="sys_online"></div>
			<!-- 
        <div>
        <table width='100%' class='leftmenu_online'>
            <tr><td class='head'><div style='float:left;'>�����û�</div><div style='float:right;'><a href='javascript:void(0);' onclick=\"load_url('m/sys/online_all.php')\" title='��ʾ���������û�'>����>></a></div><div class='clear'></div></td></tr>
            <tr>
              <td class='item' onmouseover='mi(this)' onmouseout='mo(this)'><a href="/m/patient/1111.php">aasdd</a></td></tr>
        </table>
        </div>
        -->
			<div id="sys_notice"></div>
		</div>
		<div id="frame_content"><iframe id="sys_frame" name="main" onload="frame_loaded_do(this)" src="" mid="" framesrc="" frameborder="0" scrolling="auto" width="100%" height="365" onreadystatechange="update_navi()"></iframe></div>
		<div class="clear"></div>
	</div>

	<div id="bottom_border" class="co_bottom">
		<div class="co_left_bottom"></div>
		<div class="co_right_bottom"></div>
		<div class="clear"></div>
	</div>


	<!-- loading status table -->
	<table id="sys_loading" style="display:none; position:absolute; border:1px solid #00D5D5; background:#D9FFFF; line-height:120%">
		<tr>
			<td style="padding:1px 0 0 6px"><img src='res/img/loading.gif' width='16' height='16' align='absmiddle' /></td>
			<td id="sys_loading_tip" style="padding:2px 6px 0px 6px"></td>
		</tr>
	</table>
	<!-- music player -->
	<div style="display:none; position:absolute">
		<object classid="CLSID:22D6F312-B0F6-11D0-94AB-0050C74C7E95" codeBase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,05,0509" type="application/x-oleobject" width="300" height="45" id="sys_music_player">
			<param name="autostart" value="1">
			<param name="filename" value="">
			<param name="volume" value="-450">
			<param name="playcount" value="1">
		</object>
	</div>
	<!-- sys dialog box -->
	<div id="dl_layer_div" style="position:absolute; filter:Alpha(opacity=70); display:none; background:#404040; z-index:998; opacity:0.7;"></div>
	<div id="dl_box_div" onmousedown="handlestart(event, this)" class="obox" style="position:absolute; display:none; z-index:999">
		<div id="dl_box_title_box">
			<div id="dl_box_title"></div>
			<div id="dl_box_op"><a href="javascript:load_box(0);">�ر�</a></div>
			<div class="clear"></div>
		</div>
		<div id="dl_box_loading" style="position:absolute; display:none;"><img src="res/img/loading.gif" align="absmiddle"> �����У����Ժ�... </div>
		<div id="dl_iframe"><iframe src="about:blank" frameborder="0" scrolling="auto" width="100%" id="dl_set_iframe" onload="update_title(this)"></iframe></div>
		<div id="dl_content" style="display:none;"></div>
	</div>
	<!-- msg_box -->
	<div id="sys_msg_box" style="display:none; position:absolute;cursor:pointer;" onclick="msg_box_hide()" onmouseover="msg_box_hold()" onmouseout="msg_box_delay_hide()" title="����ر�">
		<table cellpadding="0">
			<tr>
				<td class="left_div"></td>
				<td class="center_div">
					<table>
						<tr>
							<td id="sys_msg_box_content"></td>
						</tr>
					</table>
				</td>
				<td class="right_div"></td>
			</tr>
		</table>
	</div>

	<script language="JavaScript">
		dom_loaded.load(init);
	</script>

	<?php if ($submenu_pos == 2) { ?>
		<script language="javascript">
			swap_node('side_menu', 'frame_content');
		</script>
	<?php } else if ($submenu_pos == 0) { ?>
		<script language="javascript">
			show_hide_side();
		</script>
	<?php } ?>

</body>

</html>