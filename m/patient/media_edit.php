<?php

/*

// - 功能说明 : 媒体类型新增、修改

// - 创建作者 : 爱医战队 

// - 创建时间 : 2013-05-03 14:48

*/



if ($_POST) {

	$record = array();
	$record["name"] = $_POST["name"];


	if ($op == "add") {

		$record["hospital_id"] = $user_hospital_id;

		$record["addtime"] = time();

		$record["author"] = $username;

	}



	$sqldata = $db->sqljoin($record);

	if ($op == "edit") {

		$sql = "update $table set $sqldata where id='$id' limit 1";

	} else {

		$sql = "insert into $table set $sqldata";

	}



	if ($db->query($sql)) {

		msg_box("部门资料提交成功", "?", 1);

	} else {

		msg_box("资料提交失败，系统繁忙，请稍后再试。", "back", 1, 5);

	}

}



$title = $editmode ? "修改就诊来源" : "添加新的来源";



$hospital_list = $db->query("select id,name from ".$tabpre."hospital");

?>

<html>

<head>

<title><?php echo $title; ?></title>

<meta http-equiv="Content-Type" content="text/html;charset=gb2312">

<link href="/res/base.css" rel="stylesheet" type="text/css">

<script src="/res/base.js" language="javascript"></script>

<script language="javascript">

function Check() {

	var oForm = document.mainform;

	if (oForm.name.value == "") {

		alert("请输入“名称”！"); oForm.name.focus(); return false;

	}

	return true;

}

</script>

</head>



<body>

<!-- 头部 begin -->

<div class="headers">

	<div class="headers_title"><span class="tips"><?php echo $title; ?></span></div>

	<div class="headers_oprate"><button onClick="history.back()" class="button">返回</button></div>

</div>

<!-- 头部 end -->



<div class="space"></div>



<div class="description">

	<div class="d_title">提示：</div>

	<div class="d_item">1.输入名称，点击提交即可</div>

</div>



<div class="space"></div>



<form name="mainform" action="" method="POST" onSubmit="return Check()">

<table width="100%" class="edit">

	<tr>

		<td colspan="2" class="head">就诊类型资料</td>

	</tr>

	<tr>

		<td class="left">名称：</td>

		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">名称必须填写</span></td>

	</tr>

</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">

<input type="hidden" name="op" value="<?php echo $op; ?>">



<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>

</form>

</body>

</html>