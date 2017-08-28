<?php

require "config.php";
require_once("include/{$_REQUEST['class']}.class.php");

$ajax=new Ajax();

if (isset($_REQUEST['axion'])){
	
	switch($_REQUEST['axion']){
	
		case 'duplicate_shells' :
		duplicate_shells_db();
		break;

		case 'delete_shells' :
		delete_shells_db();
		break;

		case 'group_cols_remove' :
		group_cols_remove();
		break;
		
		case 'open_action_bar' :
		open_action_bar();
		break;

		case 'open_action_bar_header2' :
		open_action_bar_header2();
		break;
	
		case 'showfrom_db' :
		$ajax->showfrom_db();
		break;
	}
}


?>