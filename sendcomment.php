<?
require("config.php");
function casttoclass($class, $object)
{
  return unserialize(preg_replace('/^O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', serialize($object)));
}

if (isset($_POST) && !empty($_POST))
	{
	$asdf = unserialize($_SESSION["user"]);
	$asdf = casttoclass('stdClass', $asdf);
	$quer = sprintf("insert into feedback (requestor_name, request_type, formula_id, description, referer, last_date) values ('%s', '%s', '%s', '%s', '%s', NOW())",
					$asdf->fullname, $_POST['res'], $_POST["id"], mysql_real_escape_string($_POST["body"]), $_SERVER['HTTP_REFERER']);
	$db->query($quer);

	$title = "(general)";
	if ($_POST['id'])
		{
		$db->query("select title from formula where id=" . $_POST["id"]);
		$row  = $db->getRow();
		$title = $row['title'] . " (" . $_POST["id"] . ")";
		}

	// Emailer
	$headers  = "From: www@biomttbp01.biogen.com\r\n";
	$headers .= "Content-type: text/html\r\n";
	$msg = sprintf("<p>The following comment has been sent from the Template Library</p>
				<table><tr><td><b>Template</b></td><td>%s</td></tr><tr><td><b>Name</b></td><td>%s</td></tr><tr><td><b>Feedback Type</b></td><td>%s</td></tr><tr><td><b>Description</b></td><td>%s</td></tr></table>",
				$title, $asdf->fullname, $_POST['res'], mysql_real_escape_string($_POST["body"]));
	$success = mail("philip.brown@biogen.com,gordon.schantz@biogen.com", "Notification from Template Library", $msg, $headers);
	}
?>


