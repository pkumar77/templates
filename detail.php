<?
	require_once("config.php");

	$fid = $_GET['id'];
	$db->query("select * from formula where id=$fid");
	if ($db->getRowCount())
		$formset = $db->getRow();
	if (wf_file_exists(SITE_FILES_DIR . $formset['scriptname']))
		$formset['code'] = wf_file_get_contents(SITE_FILES_DIR . $formset['scriptname']);
	else
		$formset['code'] = "Source File Not Found";
	$wfdb->query("select id as tlfid from tlf where product='testproduct' and study='templatestudy' and project='csr' and progname='" . $formset['scriptname'] . "'");
   if ($wfdb->getRowCount())
      $wfid = $wfdb->getRow();

	$db->query("select function_name from function where id=".$formset['function_id']);
	if ($db->getRowCount())
		{
		while ($dd = $db->getRow())
			$keylist[] = $dd['function_name'];
		}
	require (SITE_ELEMENT_DIR."box_htmlhead.php");
?>
<body>
<script type="text/javascript">
function intoFlow() {
	openStudyPopup(Array("<?php printf("%s##TL%03d", $wfid['tlfid'], $formset['id']); ?>"));
}
</script>
<? require (SITE_ELEMENT_DIR."box_pagehead.php"); ?>

		<div id="bd">
			<div class="content">
				<div class="content-inner clearfix">
					<h1><? print $formset['title'] . 
					(($formset['smart_yn']) ? '<span style="font-weight:900;color:#8C7853;font-style:italic;"> ** SMART TABLE **</span>' : ''); ?>
					</h1>
					<p><? printf("TL%03d", $formset['id']); ?></p>
					<p><? print $formset['description']; ?></p>
<? if (strlen($formset['screenshot'])) { ?>
					<img src="uploads/<? print $formset['screenshot']; ?>" alt="<? print $formset['title']; ?>" width="100%" />
<? } ?>
					<div><button onclick="javascript:intoFlow();">Copy Template into Project</button>
					<button style="margin-left:380px" onclick="javascript:openCommentary(<? print $formset['id']; ?>);">Provide Feedback</button></div>
					<div class="code-section clearfix"><? print $formset['code']; ?></div>
<!--						
						<div>
<?						
					$db->query("select comment.*, concat(author.fname, ' ', author.lname) as autname from comment, author where formula_id=$fid and author_id=author.id");
					if ($db->getRowCount())
						{
						print "<ul>";
						while ($comset = $db->getRow())
							printf("<li><p>%s - %s (%s)</p></li>", $comset['comment_text'], $comset['autname'], $comset['change_date']);
						print "</ul>";
						}
?>		
						
					</div>
-->					
				</div>
			</div>
		</div>

<?php include ("elements/box_footer.php"); ?>
