<?php
	require_once("config.php");

	$idset = explode(",", $_GET['multi_req_ids']);
	print "<table><tr><th>Program name (default)</th><th>Rename program (optional)<input type='hidden' name='tid' id='tid' value='" . $_GET['tmlids'] . "'></th></tr>";
	foreach ($idset as $id)
		{
		$wfdb->query("select progname from tlf where product='testproduct' and study='templatestudy' and project='csr' and id=$id");
	   	if ($wfdb->getRowCount())	/* If not, what are they doing? */
			{
	      	$wfid = $wfdb->getRow();
			$db->query("select id from formula where scriptname='" . $wfid['progname'] . "'");
			if ($db->getRowCount())
				{
				$dd = $db->getRow();
				printf("<tr><td>%s</td><td><input type='text' class='TLtemplate' name='TLtemplate[%d]' id='TLtemplate[%d]' value='%s'></td></tr>", $wfid['progname'], $id, $id, $wfid['progname']);
				}
			}
		}
	print "</table>";
?>