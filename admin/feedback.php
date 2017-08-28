<?php
require("config.php");

if (isset($_POST) && !empty($_POST))
	{
	$uix = $_POST['uid'];
	$db->query("update feedback set biib_notes='" . mysql_real_escape_string($_POST['biib_notes']) . "' where id=$uix");
	}

$headtitle = "Feedback Administration";
require (SITE_ELEMENT_DIR . "box_htmlhead.php");
?>

<body>
<? require (SITE_ELEMENT_DIR . "box_pagehead.php"); ?>

		<div id="bd">

			<!-- content -->
			<div class="content"><div class="content-inner clearfix">
		
			<h1>Feedback Administration</h1>
	
<script type="text/javascript">

function usedPopUserDisplay(id) {
if (window.XMLHttpRequest) xmlhttp=new XMLHttpRequest();
else xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
xmlhttp.open("get","getfbdata.php?q="+id,false);
xmlhttp.send();
if (xmlhttp.responseText.length)
	{
	var obj = JSON.parse(xmlhttp.responseText);
	document.getElementById('uid').value = id;
	document.getElementById('requestor_name').innerHTML = obj.requestor_name;
	document.getElementById('request_type').innerHTML = obj.request_type;
	document.getElementById('title').innerHTML = obj.title;
	document.getElementById('request_date').innerHTML = obj.request_date;
	document.getElementById('description').innerHTML = obj.description;
	document.getElementById('biib_notes').value = obj.biib_notes;
	}
document.getElementById("popup_new_user").style.display = "block";
}
function usedPopUserClose() {
	document.getElementById("popup_new_user").style.display = "none";
}
</script>
<?

print "<table id='table-1'><tr><th>User</th><th>Script</th><th>Type</th><th>Feedback</th><th>Date</th><th>Comment</th></tr>";
$db->query("select feedback . * , formula.title from feedback, formula where formula.id = formula_id
union select feedback . * , '(general)' as title from feedback where formula_id =0
order by request_date desc");
while ($data = $db->getRow())
	{
	printf("<tr id='%s'><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", 
			$data['id'], $data['requestor_name'], $data['title'], $data['request_type'], nl2br($data['description']), $data['last_date'], nl2br($data['biib_notes']));
	}
print "</table>";
?>

<script type="text/javascript">
	
$("#table-1 tr").not(':first').hover(	// not the header row
  function () {
    $(this).css("background","yellow");
  }, 
  function () {
    $(this).css("background","");
  }
);

$('#table-1 tr').click(function() {
	var linid = $(this).attr("id");
	if (linid)
		usedPopUserDisplay(linid);
});

</script>

	<div id='popup_new_user' name='popup_new_user' style='width: 400px; height:320px; display:none; top: 7%; left: 5%; position:fixed; background-color:white; layer-background-color:#003366;padding: 1em;border-style: solid; overflow:scroll;'>
		<h3>Feedback</h3>
		<div class="standard-form">
		<form method="post" id="new-val" name="new_val">
			<input type='hidden' id="uid" name='uid' value='0'>
			<div class="line-field"><b>User ID: </b><span id='requestor_name'>&nbsp;</span></div>
			<div class="line-field"><b>Type: </b><span id='request_type'>&nbsp;</span></div>
			<div class="line-field"><b>Template </b><span id='title'>&nbsp;</span></div>
			<div class="line-field"><b>Date </b><span id='request_date'>&nbsp;</span></div>
			<div class="line-field"><b>Feedback</b><br><span id='description'>&nbsp;</span></div>
			<div class="line-field"><b>Comments </b><br><textarea style="width:320px;height:100px" name="biib_notes" id="biib_notes"></textarea></div>
			<div class="submit-line-field"><label for="submit">&nbsp;</label><input class="submitButton" type="submit" value="Save Changes">&nbsp;&nbsp;&nbsp;&nbsp;<input class="submitButton" type="button" value="Cancel" onclick="usedPopUserClose();"></div>
		</form>
		</div>
	</div>
<a href="index.php">Click here to return to the main Admin menu</a>
</div>
<?php include (SITE_ELEMENT_DIR . "box_footer.php"); ?>  

</body>
</html>
