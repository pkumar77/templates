<?php
require("config.php");

$q = (!empty($_POST) && array_key_exists("id", $_POST) && strlen($_POST['id'])) ? $_POST['id'] : $_GET["q"];

if ($q)
	$quer = "select feedback . * , formula.title from feedback, formula where formula.id = formula_id and feedback.id=$q
union select feedback . * , '(general)' as title from feedback where formula_id =0  and feedback.id=$q";

$db->query($quer);
$row = $db->getRow();
print json_encode($row);
?>