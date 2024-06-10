<?php
/*
// - 功能说明 : 新增、修改病人资料
// - 创建作者 : 爱医战队 
// - 创建时间 : 2013-05-01 05:57
*/
require "../../core/core.php";
if(isset($_GET["id"]))
{
	$id=@$_GET["id"];
	$page=@$_GET["page"];
	$d_id = $db->query("DELETE FROM yy_list WHERE id='{$id}'");
	
	if(!empty($_GET["page"]))
	{
		echo '<script language="javascript">location.href="w_list.php?page='.$page.'"</script>';
	}
	else
	{
		echo '<script language="javascript">location.href="w_list.php";</script>';
	}
}
?>