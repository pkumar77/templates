<?
	require_once("config.php");

	if (!isset($_SESSION['author_id']))
		header("Location: ".SITE_URL."/login.php" );

	$fid = 0;
	if (isset($_GET['id']) && $_GET['id'] != "")
		$fid = $_GET['id'];
	else if (isset($_POST['id']) && $_POST['id'] != "0")
		$fid = $_POST['id'];
	if (isset($_GET['delete']) && $_GET['delete'] != "")
		{
		$db->query("delete from formula where id=" . $_GET['delete']);	
		header("Location:index.php");
		}

	if (!empty($_POST))
		{
		$id = $_POST['id'];
		$title = mysql_real_escape_string($_POST['title']);
		$description = mysql_real_escape_string($_POST['description']);
		$screenshot = $_POST['screenshot'];
		$scriptname = $_POST['scriptname'];
		$smart_yn = $_POST['smart_yn'];
		$fc = explode("#", $_POST['category_id']);
		$category_id = $fc[0];
		$function_id = $fc[1];
		$obj_type = $_POST['obj_type'];
		$code = mysql_real_escape_string($_POST['code']);
		$filename = "";

		if (strlen($_FILES['screenshot']['name']))
			{
			$uploadfile = SITE_UPLOAD_DIR . $_FILES['screenshot']['name'];
			if (!move_uploaded_file($_FILES['screenshot']['tmp_name'], $uploadfile))
				$errorstr = "<p>Error uploading image file</p>";
			else
				$filename = $_FILES['screenshot']['name'];
			}

		if ($id == "" || $id == 0)
			{
			$db->query("insert into formula (title, scriptname, description, screenshot, code, category_id, function_id, obj_type, smart_yn) values ('$title', '$scriptname', '$description', '$filename', '$code', $category_id, $function_id, '$obj_type', $smart_yn)");	
			$statusstr = "<p>Page has been saved</p>";
			$fid = mysql_insert_id();
			$db->query("insert into history (formula_id, author_id, rev_number) values ($fid, " . $_POST['author_id'] . ", 1)");	
			header("Location:editdata.php?id=".$fid);
			}
		else
			{
			if (strlen($filename))
				$qstr = "update formula set title='$title', scriptname='$scriptname', description='$description', screenshot='$filename', code='$code', category_id=$category_id, function_id=$function_id, obj_type='$obj_type', smart_yn=$smart_yn where id=$id";
			else
				$qstr = "update formula set title='$title', scriptname='$scriptname', description='$description', code='$code', category_id=$category_id, function_id=$function_id, obj_type='$obj_type', smart_yn=$smart_yn where id=$id";
			$db->query($qstr);
			$statusstr = "<p>Page has been successfully updated</p>";
			$db->query("select max(rev_number) as rv from history where formula_id = $id");
			if ($db->getRowCount())
				$ff = $db->getRow();
			$qq = sprintf("insert into history (formula_id, author_id, rev_number) values (%s, %s, %s)", $id, $_POST['author_id'], $ff['rv'] + 1);
			$db->query($qq);	
			}
		}		
	$formset = array();
	if ($fid)
		{
		$db->query("select * from formula where id=$fid");
		if ($db->getRowCount())
			$formset = $db->getRow();
		}
	if (count($formset))
		{
		if (wf_file_exists(SITE_FILES_DIR . $formset['scriptname']))
			$formset['code'] = wf_file_get_contents(SITE_FILES_DIR . $formset['scriptname']);
		else
			$formset['code'] = SITE_FILES_DIR . $formset['scriptname'] . ": Source File Not Found";
		$pa = (object)$formset;
		}		
	else
		$pa =NULL;

	if (wf_file_exists(SITE_FILES_DIR . $formset['scriptname']))
		$formset['code'] = wf_file_get_contents(SITE_FILES_DIR . $formset['scriptname']);
	else
		$formset['code'] = "Source File Not Found";

	$headtitle = "Edit Template Data";
	require (SITE_ELEMENT_DIR . "box_htmlhead.php");
?>

<body>
<? require (SITE_ELEMENT_DIR . "box_pagehead.php"); ?>

		<div id="bd">

			<!-- content -->
			<div class="content"><div class="content-inner clearfix">
		
			<h1>Edit Template Data</h1>

<?php
	if (strlen($statusstr)) print "<p>$statusstr</p>";
	if (strlen($errorstr)) print "<p>$errorstr</p>";
?>
<form id="sasgraph_form" name="sasgraph_form" method="post" enctype="multipart/form-data" onsubmit="return formValidate();">
	<input name="id" id="id" type="hidden" value="<? if ($pa) print $pa->id; ?>">
	<label for="title">Title:</label><br><input name="title" id="title" type="text" size="120" maxlength="300" value="<? if ($pa) print $pa->title; ?>"><br><br>
	<label for="title">SAS File Name:</label><br><input name="scriptname" id="scriptname" type="text" size="60" maxlength="80" value="<? if ($pa) print $pa->scriptname; ?>"><br><br>
	<label for="description">Description:</label><br><textarea name="description" id="description" style="width:590px;height:100px"><? if ($pa) print $pa->description; ?></textarea><br><br>
	<label for="category_id">Category:<?php print $pa->category_id . "#" . $pa->function_id; ?></label><br><select name="category_id" id="category_id">
<?php		
	$db->query("SELECT submenu . * , category.cname, function.fdisplay_name 
			FROM submenu, function, category WHERE function.id = submenu.function_id AND category.id = submenu.category_id ORDER BY category_id, order_id");
	while ($row = $db->getRow())
		printf("<option value='%s#%s' %s>%s - %s</option>", $row['category_id'], $row['function_id'], ($pa && ($pa->category_id . "#" . $pa->function_id) == ($row['category_id'] . "#" . $row['function_id'])) ? "selected" : "", $row['cname'], $row['fdisplay_name']);
?>		
</select><br><br>
	<label for="obj_type">Content Type:</label><br><select name="obj_type" id="obj_type">
<?php
	printf("<option value='template' %s>Template</option>", ($pa && $pa->obj_type == "template") ? "selected" : "");
	printf("<option value='table' %s>Table</option>", ($pa && $pa->obj_type == "table") ? "selected" : "");
?>		
</select><br><br>
	<label for="smart_yn">SMART:</label><br><select name="smart_yn" id="obj_type">
<?php
	printf("<option value='0' %s>No</option>", ($pa && $pa->smart_yn == "0") ? "selected" : "");
	printf("<option value='1' %s>Yes</option>", ($pa && $pa->smart_yn == "1") ? "selected" : "");
?>		
</select><br><br>
	<label for="author_id">Author:</label><br><select name="author_id" id="author_id">
<?php
	$db2 = new db();
	if ($pa && $pa->id)
		{
		$db2->query("select author_id from history where formula_id=" . $pa->id . " order by rev_number desc");
		if ($db2->getRowCount())
			{
			$tmp = $db2->getRow();
			$aid = $tmp['author_id'];
			}
		}
	else
		$aid = 0;
	$db2->query("select concat(fname, ' ' , lname) as aut, id from author where id > 1");
	while ($row2 = $db2->getRow())
		printf("<option value='%s' %s>%s</option>", $row2['id'], ($aid == $row2['id']) ? "selected" : "", $row2['aut']);
?>		
</select><br><br>
<?php if ($pa && strlen($pa->screenshot)) { ?>
	<div>
	<div style="width:375px;display:inline;float:left">
	<label>Current Screenshot:</label><br>
	<b><?php print $pa->screenshot; ?></b>
	</div>
	<div style="display:inline;">
		<?php if ($pa && $pa->screenshot) { ?><a href="../uploads/<? print $pa->screenshot; ?>" target="_blank"><img src="../uploads/<? print $pa->screenshot; ?>" width="50px" /></a><?php } else print "&nbsp;"; ?>
	</div>
	</div>
	<br><br>
	<label for="screenshot">Change Screenshot:</label><br>
<?php } else { ?>
	<label for="screenshot">Screenshot:</label><br>
<?php } ?>
	<input type="file" name="screenshot" id="screenshot" size="30">
	<br><br>
	<label for="code">Code:</label>
	<div class="code-section clearfix"><? print $pa->code; ?></div>

	<input type="submit" value="Save" />&nbsp;<input type="button" value="Cancel" onclick="javascript:back(-1);"/>
</form>
<br><br>
<script type="text/javascript">
function removeme(id) {
	if (confirm("Are you sure that you want to delete this template?"))
		window.location="editdata.php?delete="+id;
}
</script>
<?php if ($pa && $pa->id) { ?>
	<a href="javascript:removeme(<? print $pa->id; ?>);">Delete this template</a><br>
	<a href="../detail.php?id=<? print $pa->id; ?>">Click here to go to this page on the website</a><br>
<?php } ?>		
<a href="index.php">Click here to return to the main Admin menu</a>
</div>
<?php include (SITE_ELEMENT_DIR . "box_footer.php"); ?>  

</body>
</html>

