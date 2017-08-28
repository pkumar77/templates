<?
	require_once("config.php");

	if (isset($_SESSION['author_id']))
		header("Location: admin/index.php");

	$userid = $_POST['userid'];
	$password = $_POST['password'];
	$authorized = -1;
	if (strlen($userid) && strlen($password))
		{
		$command_ldap = "/usr/share/centrifydc/bin/ldapsearch -x -h camvmcorpdc01 -b \"dc=corp,dc=biogen,dc=com\"  -D \"corp\\$userid\" -w \"$password\" \"(sAMAccountName=$userid)\"";
		$results = shell_exec($command_ldap);
		$namestr = preg_match('/(CN=)+(\w+\s+\w+)/', $results, $name);
		$emailstr = preg_match('/(SMTP:)+(\w+\.\w+(@)+(\w+\.\w+))/', $results, $mail);
		if (trim($name[2]) != "")
			$namearray = explode(" ", trim($name[2]));
		$db->query("select * from author where fname = '" . $namearray[0] . "' and lname = '" . implode(" ", array_slice($namearray, 1)) . "'");
		if ($db->getRowCount())
			{
			$autline = $db->getRow();
			$authorized = $autline['permission'];
			$_SESSION['author_id'] = $autline['id'];
			header("Location: admin/index.php");
			}
		else
			$authorized = 0;
		}

	require (SITE_ELEMENT_DIR . "box_htmlhead.php");
?>
<script>
function getuserValidator() {
	var uuu = document.getElementById('uid');
	if (isTrimEmpty(uuu.value, "You must specify a User ID"))
		return false;
	return true;
}
</script>
	<body class="home">
		<!-- body -->
<? 
		$currtab = "home";
		require (SITE_ELEMENT_DIR."box_pagehead.php");
?>
		<div id="bd" class="clearfix">
			<div class="content"><div class="content-inner clearfix">
<?php
	if (!$authorized)
		print '<p>You are not currently a member of the developer list for this application. For further instructions, contact Yan Chang at <a href="mailto:yan.chang@biogenidec.com">yan.chang@biogenidec.com</a>.</p>';
	else if ($authorized == 1)
		print '<p>Login successful. Author ID: ' . $_SESSION['author_id'] . '</p>';
		
?>
<h1>Login</h1>
<form name="Logform" method=post action="login.php">
<table border="2" cellspacing="2">
<tr><td align="right"><label>User name</label></td><td align="left"><input name="userid" id="userid" type="text" size="32" maxlength="32"></td></tr>
<tr><td align="right"><label>Password</label></td><td align="left"><input name="password" id="password" type="password" size="32" maxlength="32"></td></tr>
</table>
<br><input type="submit" value="Submit">
</form>
				</div></div></div>
		</div><!-- /body -->
	
		
	</body>
</html>
