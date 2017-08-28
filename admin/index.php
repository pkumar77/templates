<?
	require_once("config.php");

	if (!isset($_SESSION['author_id']))
		header("Location: ".SITE_URL."/login.php" );

	$headtitle = "Podthing CMS home";
	require (SITE_ELEMENT_DIR . "box_htmlhead.php");
?>

<body>
<? require (SITE_ELEMENT_DIR . "box_pagehead.php"); ?>

		<div id="bd">

			<!-- content -->
			<div class="content"><div class="content-inner clearfix">
		
			<h1>Content Management System</h1>

			<p><a href='editdata.php'>[Click here]</a> to add a template to the database</p>
			<p><a href='feedback.php'>[Click here]</a> for Feedback Administration</p>
			<table border="0" cellspacing="2" cellpadding="3">
			  <thead>
			    <tr>
			      <th style="text-align:left">ID</th>
			      <th style="text-align:left">Title</th>
			      <th style="text-align:left">Program Name</th>
			      <th style="text-align:left">Category</th>
			      <th style="text-align:left">Status</th>
			      <th style="text-align:left">Author</th>
			    </tr>
			  </thead>
				
<?php
			$db->query("select formula.id, formula.scriptname, formula.title, category.cname from formula,category where category.id=formula.category_id order by formula.id");
			$data = array();
			$db2 = new db();
			while ($row = $db->getRow())
				{
				$db2->query("select author_id from history where formula_id=" . $row['id'] . " order by rev_number desc");
				if ($db2->getRowCount())
					{
					$tmp = $db2->getRow();
					$db2->query("select concat(fname, ' ' , lname) as aut from author where id=" . $tmp['author_id']);
					$tmp2 = $db2->getRow();
					$aname = $tmp2['aut'];
					}
				else
					$aname = "";


	         		$wfdb->query("select id as tlfid from tlf where product='testproduct' and study='templatestudy' and project='csr' and progname='" . $row['scriptname'] . "'");
			      if ($wfdb->getRowCount())
			      	{
		      	      $wfid = $wfdb->getRow();
		            	$lin['tlfid'] = $wfid['tlfid'];
					$wfdb->query("select status from rev where tlf_id=" . $lin['tlfid'] . " order by id desc limit 0,1");
				      if ($wfdb->getRowCount())
						{
				            $wflin = $wfdb->getRow();
						$wfstatus = $wflin['status'];
						}
					}
				printf("<tr><td>%s</td><td><a href='editdata.php?id=%s'>%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $row['id'], $row['id'], $row['title'], $row['scriptname'], $row['cname'], $wfstatus, $aname);
				}
?>
			</table>
			</div></div><!--/content-->

		</div><!-- /body -->
		
<?php include (SITE_ELEMENT_DIR . "box_footer.php"); ?>	
