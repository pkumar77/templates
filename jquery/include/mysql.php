<?php
error_reporting(0);
session_start();

require_once "../../config.php";

$keyword = $_GET["q"];

$min=0;
$max=10;

//Study Search
$query="select product,study,project from projects where concat(product,'/',study,'/',project) like '%$keyword%' order by product,study,project  LIMIT $min,$max";

if (isset($keyword) && $keyword==''){
	// Filter for favorites
	//$query = "select distinct product,study,project from tlf left join rev on tlf.id=rev.tlf_id where (rev.assigned_id='$_SESSION[valid_user]' or rev.assigned_by_id='$_SESSION[valid_user]') and status!='Retired' order by tlf.id desc LIMIT $min,$max ";
}

$wfdb->query($query);
if($wfdb->getRowCount()){
	while($row = $wfdb->getRow()){
		if ($row[product]!='' && $row[study]!='' && $row[project]!=''){
			$array[]="$row[product]/$row[study]/$row[project]";
		}
	 }
}

if(count($array)>0){
	foreach($array as $key=>$value){
		echo $value."\n";
	}
}

?>