<?php
class Page {
	public $pagetitle = "";
	public $timestack = array();
	
	public function Page($title) {
		$this->pagetitle = $title;
	}
	public function Header() {
		$this->timestack[] = array("name"=>"start", "tim"=>microtime(true));
		print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		print "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
		print "<head>\n";
		print "<meta charset=\"utf-8\">\n";
		print "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
		print "<title>" . $this->pagetitle . "</title>\n";
		print "<link rel=\"stylesheet\" href=\"" . SITE_URL . "/css/main.css\">\n";
		print "<link rel=\"stylesheet\" href=\"" . SITE_URL . "/css/js_menu.css\">\n";
		print "<script src=\"//code.jquery.com/jquery-1.11.0.min.js\" type=\"text/javascript\"></script>\n";
		print "<script type=\"text/javascript\" src=\"" . SITE_URL . "/js/shortcut.js\"></script>\n";
		print "<link rel=\"stylesheet\" href=\"//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css\">";
		print "<script src=\"https://code.jquery.com/ui/1.12.0/jquery-ui.js\"></script>";
		print "<link rel=\"stylesheet\" type=\"text/css\" href=\"jquery/include/jquery.autocomplete.css\" />";
		print "<script type=\"text/javascript\" src=\"js/js_menu.js\"></script>";
		}
	public function CloseHeader() {
		print "</head>\n";
	}
	public function Body($params="") {
		print "<body $params>";
		print "<div class=\"darkenBackground\" name=\"darkBackgroundLayer\" id=\"darkBackgroundLayer\" style=\"display:none\"></div>\n";
	}
	public function Menu() {
		print '<div><table width=\'100%\'><tr><td width=\'60%\' align=\'left\'><font size=\'3\'>logo</font></td><td width=\'40%\' align=\'right\'><input type=\'text\'></td></tr></table></div>
		<div style="background-color:#5970B2;width:100%;height:40px;line-height:40px;"><center><ul style="display: table-cell;" id="sddm">
    <li><a href="#" 
        onmouseover="mopen(\'m1\')" 
        onmouseout="mclosetime()">Home</a>
        <div id="m1" 
            onmouseover="mcancelclosetime()" 
            onmouseout="mclosetime()">
        <a href="#">HTML/CSS</a>
        <a href="#">DHTML Menu</a>
        <a href="#">JavaScript</a>
        </div>
    </li>
    <li><a href="#" 
        onmouseover="mopen(\'m2\')" 
        onmouseout="mclosetime()">Download</a>
        <div id="m2" 
            onmouseover="mcancelclosetime()" 
            onmouseout="mclosetime()">
        <a href="#">ASP Server-side</a>
        <a href="#">Pulldown navigation</a>
        <a href="#">AJAX Drop Submenu</a>
        <a href="#">DIV Cascading </a>
        </div>
    </li>
    <li><a href="#">Order</a></li>
    <li><a href="#">Help</a></li>
    <li><a href="#">Contact</a></li>
</ul>
<div style="clear:both"></div></center></div><br>';
		}
	public function Footer() {
		print "<div id='popwindow' name='popwindow' style='width: 90%; height:90%; display:none; top: 5%; left: 5%; position:fixed; background-color:white; layer-background-color:#003366;padding: 1em;border-style: solid; overflow-x:auto;'>&nbsp;</div>";
		include SITE_DIR . "/include/buggg.php";
		print "</div><p>&nbsp;</p></body></html>";
	}		
}
?>
