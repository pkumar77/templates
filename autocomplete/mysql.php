<?php
$keyword = $_GET["q"];

mysql_connect("biomttbp01", "root","Biogen2015") or die("Could not connect : " .mysql_error());
mysql_select_db("workflow");

$min=0;
$max=10;
$query="select concat(product,'/',study,'/',project) as spath from projects where product != '' and study != '' and project != '' and concat(product,'/',study,'/',project) like '%$keyword%' order by product,study,project  LIMIT $min,$max";

$result = mysql_query($query);
if (mysql_num_rows($result))
	{
	while($row = mysql_fetch_array($result))
		echo $row['spath'] . "\n";
	}	
?>
