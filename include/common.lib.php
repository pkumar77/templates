<?
class glossary {
	
	 public function __construct()
  {
      //echo 'The class "', __CLASS__, '" was initiated!<br />';
  }
 
  public function __destruct()
  {
      //echo 'The class "', __CLASS__, '" was destroyed.<br />';
  }
  

 
}
	


/* As long as we don't have any parsing except the translation from */
/* camel case to underscore with lower case, don't think too much */
/* If we have to do something more, the if (0) block is the */
/* character-by-character way to translate */
function field_name_format($str) {
//		if (0) {
//			$rval = "";
//			for ($i = 0; $i < strlen($str); ++$i)
//				$rval .= (($i && ctype_upper($str{$i})) ? "_" : "") . strtolower($str{$i});
//			return $rval;		
//		} else {
	$str = preg_replace('/OID/', 'Oid', $str);
	$str = preg_replace('/ID/', 'Id', $str);
	$str = preg_replace('/def:/', '', $str);
	$str = preg_replace('/xml:/', '', $str);
	$str = preg_replace('/xlink:/', '', $str);

	return(strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $str)));
//		}
}

function wf_file_exists($input_file){
	if(trim($input_file)=="")
		return 0;
	else{
		
		$cmd_file= WF_SHELL_COMMAND . " ls $input_file"; 
		$cmd_response_file = shell_exec($cmd_file);
		if(trim($cmd_response_file)!="")
			return 1;
		else
			return 0;
	}
}

function wf_dir_exists($input_dir){
	if(trim($input_dir)=="")
		return 0;
	else{
		$cmd_dir=WF_SHELL_COMMAND . " ls -ld $input_dir"; 
		$cmd_response_dir = shell_exec($cmd_dir);
		if(trim($cmd_response_dir)!="")
			return 1;
		else
			return 0;
	}
}

function wf_file_get_contents($filename) {
  $rand = rand(0, 5000);
  $cmd_response = shell_exec(WF_SHELL_COMMAND . " cp $filename " . SITE_DOCS_DIR . "/tmp/tempfile.$rand");
  $rval .= file_get_contents(SITE_DOCS_DIR . "/tmp/tempfile.$rand");
  $cmd_response = shell_exec(WF_SHELL_COMMAND . " rm " . SITE_DOCS_DIR . "/tempfile.$rand");
  return($rval);
}

	$clone_study_id = 0;
	
/* NOTE: The offsets in the result point to the first character AFTER the needle */
function strallpos($haystack, $needle, $offset=0)
{ 
	$result = array(); 
	
	for($i = $offset; $i<strlen($haystack); $i++){
		 
		$pos = strpos($haystack,$needle,$i); 
		if($pos !== FALSE)
			{ 
			$offset =  $pos;
			if($offset >= $i)
				{ 
				$i = $offset; 
				$result[] = $offset + strlen($needle); 
				} 
			} 
		} 
return $result; 
} 

function buildSelector($fldname, $vals, $default, $value_field_name="id", $toprow_text="Please Select") {
$out = "<select name='$fldname' id='$fldname'><option value='-1'" . (($default == 0) ? (" selected") : ("")) . ">$toprow_text</option>";
foreach ($vals as $val)
	$out .= "<option value='" . $val[$value_field_name] . "'" . (($default == $val[$value_field_name]) ? (" selected") : ("")) . ">" . $val['name'] . "</option>";
$out .= "</select>";
return $out;
}

function xLineOut($line, $tabs=0, $crlf=1,$noblank=1) {
$output = "";
while ($tabs--)
	$output .= "\t";
/* This could be coming from a calculation, so make sure we have something to */
/* output, unless we're okay with blank lines */
if (strlen($line) || $noblank == 0)
	{
	$linedata = str_replace("\r\n", "~", $output . $line);
	$linedata  = str_replace("\n", "~", $linedata);
	$linedata  = str_replace("~ ", "~&#160;", $linedata);
	while (strpos($linedata, "&#160; ") !== FALSE)
		$linedata  = str_replace("&#160; ", "&#160;&#160;", $linedata);
	print $linedata;
	if ($crlf)
		print "\r\n";
	}
}

function xMultiLineOut($lines, $tabs=0, $crlf=1,$noblank=1) {
$linarray = explode("\r\n", $lines);
$output = "";
while ($tabs--)
	$output .= "\t";
foreach ($linarray as $line)
	{
	if (strlen($line) || $noblank == 0)
		{
		print (str_replace("\n", "~", $output . $line));
		if ($crlf)
			print "\r\n";
		}
	}
}

if(!function_exists('get_called_class')) {
function get_called_class() {
	$matches=array();
	$bt = debug_backtrace();
	$l = 0;
	do {
		$l++;
		if(isset($bt[$l]['class']) AND !empty($bt[$l]['class']))
			return $bt[$l]['class'];
		$lines = file($bt[$l]['file']);
		$callerLine = $lines[$bt[$l]['line']-1];
		preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/', $callerLine, $matches);
		if (!isset($matches[1]))
			$matches[1]=NULL; //for notices
		if ($matches[1] == 'self')
			{
			$line = $bt[$l]['line']-1;
			while ($line > 0 && strpos($lines[$line], 'class') === false) 
				$line--;                 
			preg_match('/class[\s]+(.+?)[\s]+/si', $lines[$line], $matches);
			}
		} while ($matches[1] == 'parent'  && $matches[1]);
	return $matches[1];
	}
}

function convertspecialchars($src) {
	$translation=array(
	"\"" => "&#034;",
	"'" => "&#039;",
	"<" => "&lt;",
	">" => "&gt;"
	);
	return str_replace(array_keys($translation),array_values($translation),$src);
}

function sanitize_output($text) {
	if(!get_magic_quotes_gpc())
		$text=stripslashes($text);

	$text=trim($text);
	$text = str_replace("<", "&lt;", $text); 
	$text = str_replace(">", "&gt;", $text); 
	$text = str_replace("\"", "&quot;", $text); 
    return $text; 
} 


function sanitize_input($src){
	$src=trim($src);
	//UnicodeStripper
	$bypass = array(
		"&quot;"=>"[token: quote]",
		"&gt;"=>"[token: gt]",
		"&lt;"=>"[token: lt]",
		"&#039;"=>"[token: singleq]",
		"&#034;"=>"[token: doubleq]",
	);
	$translation = array(
//		"&" => "&amp;",
		"\xe2\x80" => "",
		"\xa0" => "",
		"\x82" => "&#8218;",
		"\x83" => "&#402;",
		"\x84" => "&#8222;",
		"\x85" => "&#8230;",
		"\x86" => "&#8224;",
		"\x87" => "&#8225;",
		"\x88" => "&#710;",
		"\x89" => "&#8240;",
		"\x8a" => "&#352;",
		"\x8b" => "&#8249;",
		"\x8c" => "&#338;",
		"\x91" => "\'",
		"\x92" => "\'",
		"\x93" => "\"",
		"\x94" => "\"",
		"\x95" => "&#8226;",
		"\x96" => "-",
		"\x97" => "&#8212;",
		"\x98" => "\'",
		"\x99" => "\'",
		"\x9a" => "&#353;",
		"\x9b" => "&#8250;",
		"\x9c" => "\"",
		"\x9d" => "\"",
		"\x9f" => "&#376;"
	);
	$src = str_replace(array_keys($bypass),array_values($bypass),$src);
	$src = html_entity_decode($src);
	$src = str_replace(array_keys($translation),array_values($translation),$src);
	$src = str_replace(array_values($bypass),array_keys($bypass),$src);

	if(!get_magic_quotes_gpc())
		$src=mysql_real_escape_string($src);

	return($src);
}


function removewhitespace($instream)
{
	return preg_replace('/([ ]+)"/', ('"'), $instream);
}

function array_field_list($key, $arr)
{
	$rval = array();
	foreach ($arr as $lin)
		$rval[] = $lin[$key];
	return $rval;
}	

function checkldapuser_ldap($username,$password){
   if($connect=@ldap_connect(LDAP_SERVER)){ 

		ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
	
	    // bind to ldap connection
	   if(($bind=@ldap_bind($connect,"corp\\$username","$password")) == false){
		// print "Username, Password didn't match....<br>\n";
		return false;
	   }
	   return true;
	   @ldap_close($connect);
  } 
  else {                                  
	 echo "no connection to '$ldap_server'<br>\n";
	 mail("pankaj.kumar@biogenidec.com","ldap connection problem","","From:pankaj.kumar@biogenidec.com");
  }

  @ldap_close($connect);
  return(false);
}					

function get_field_label(){
}


?>
