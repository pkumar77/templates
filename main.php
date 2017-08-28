<?php
include("config.php");


$page = new Page("Template code site");
$page->Header();

echo "<script type=\"text/javascript\" src=\"js/ajax_main.js?var=2\"></script>";

$page->CloseHeader();
$page->Body("onload='javscript:showfrom_db();'");
$page->Menu();


	
/******************************************************************
HTML BODY starts here
******************************************************************/


// div to display floating action action bar
echo "<div id='container'><div id=\"action_bar\" onmousedown='mydragg.startMoving(this,\"container\",event);' onmouseup='mydragg.stopMoving(\"container\");' style=\"position:fixed;display:none; width: 200px;height: 180px;left:490px;top:280px;background-color:lightyellow;border:1px\"></div></div>";


echo "<form method='post' action='".$_SERVER['PHP_SELF']."' name='mainform'>";


// main table..
echo "<table width='100%' width='".TABLE_WIDTH."%' border=1>";
echo "<tr>";


/* left hand column starts here 16% - set it to 0% if page does not have left hand */

echo "<td bgcolor='".BGCOLOR_LEFT_COLUMN."' width='".WIDTH_LEFT_COLUMN."%' valign='top'>";

	// DIV left hand column starts here
			echo "<div style='overflow:scroll; width:350px;height:800px;'>";
	
				 //START Change study table 
				echo "<table cellpadding=3>"; 
				
					// workflow study selection box. remove this if not needed
					echo "<tr><td><b>Select Study</b>: <table height='60' border='0' bgcolor='#424A53'><tr><td colspan=\"3\">&nbsp;&nbsp;<input name=\"projectbox\" value='".$_REQUEST['projectbox']."' id=\"targetDiv_sl\" size=\"30\" type=\"text\">&nbsp;<input type=\"submit\" name=\"btn_submit\" value=\"GO\"/>&nbsp;<div id=\"result_copy\"><b><font color=\"red\"></font></b></div>
						</td></tr>
					</table>
				</td></tr>
			</table>";
			
			 //END Change study table

			echo "</div>";

	// DIV left hand column starts here

echo "</td>";  /* END -- table for left hand column*/


	
						
/* RIGHT hand column - table shell body */	
echo "<td width='".WIDTH_RIGHT_COLUMN."%' bgcolor='".BGCOLOR_RIGHT_COLUMN."' valign='top'>";

echo "<div id=\"right_column_body\" style='overflow:scroll; width:100%;height:800'>";  /*START - container for right hend colum body */
echo "</div>"; /*END - container for table shell body */
echo "</form>";
echo "</td>
		</tr>
</table>";		

?>
<script type="text/javascript" src="jquery/include/jquery.js"></script>
<script type='text/javascript' src='jquery/include/jquery.autocomplete.js'></script>

<SCRIPT LANGUAGE="JavaScript">
<!--
	var $j = jQuery.noConflict();
	$j(document).ready(function() {
		
	function log(event, data, formatted) {
		$j("#result_copy").html( !data ? "<font color='red'>&nbsp;&nbsp;<b>^ Please select a valid study</b></font>" : "");
		!data? document.mainform.btn_submit.disabled=true : document.mainform.btn_submit.disabled=false;
    }

	$j("#targetDiv_sl").autocomplete("jquery/include/mysql.php", {
		width: 260,
		mustMatch:true,
		minChars: 0,
		max: 12,
		autoFill: false,
		scrollHeight: 220,
		delay:10
	});

	$j(":text").result(log).next().click(function() {
		$j(this).prev().search();
	});
});
//-->
</SCRIPT>


<?php
$page->footer(); 
?>