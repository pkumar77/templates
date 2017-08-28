<?
	require_once("config.php");

	$headtitle = "Contact the Developers";
	$fid = $_GET['id'];
	$db->query("select * from author order by lname");
	if ($db->getRowCount())
		{
		while ($dd = $db->getRow())
			$alist[] = $dd;
		}
	require (SITE_ELEMENT_DIR."box_htmlhead.php");
?>
<body>
<? require (SITE_ELEMENT_DIR."box_pagehead.php"); ?>

		<div id="bd">
			<div class="content">
				<div class="content-inner clearfix">
					<h1>Contact the Developers</h1>
					<ul>
<?php
					foreach ($alist as $aa)
						{
						if ($aa['id'] != 1)
							print "<li>" . $aa['fname'] . " " . $aa['lname'] . " (<a href='mailto:" . $aa['email'] . "'>" . $aa['email'] . "</a>)</li>";
						}
?>						
					</ul>
				</div>
			</div>
		</div>
<?php include ("elements/box_footer.php"); ?>	
