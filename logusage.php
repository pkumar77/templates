<?php
	require_once("config.php");
	$idset = explode(",", $_GET['multi_req_ids']);
	foreach ($idset as $id)
		{
		$pair = explode(":~", $id);
		$wfdb->query("select progname from tlf where product='testproduct' and study='templatestudy' and project='csr' and id=" . $pair[0]);
   	if ($wfdb->getRowCount())
      	$wfid = $wfdb->getRow();
		$db->query("select id from formula where scriptname='" . $wfid['progname'] . "'");
		if ($db->getRowCount())
			{
			$dd = $db->getRow();
			$db->query("insert into downloads (username, template_id, tlf_id, script_name, study_path, overwrite_program, overwrite_request) 
					values ('" . $_GET['username'] . "'," . $pair[0] . "," . $dd['id'] . ", '" . $pair[1] . "', '". $_GET['projectbox'] . "', '" . $_GET['op'] . "', '" . $_GET['or'] . "')");
			}
		}
?>