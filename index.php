<?php
require("config.php");

if(isset($_REQUEST['logout'])){
	// unset login session varialbe
	unset($_SESSION['userid']);
}


/************START: when submit button is clicked. **********/
if (isset($_REQUEST['fusername']) && isset($_REQUEST['fpassword'])){
	$errormsg="";
	
	$db->query("select id, login, fullname,email  from people where username='".$_REQUEST['fusername']."'");
	$count_ppl=$db->getRowCount();
	$rows_ppl = $db->getRow();
		
	if($count_ppl>0){
		if (checkldapuser_ldap($_REQUEST['fusername'],$_REQUEST['fpassword'])){
			$_SESSION['userid']=$_REQUEST['fusername'];
			$_SESSION['fullname']=$rows_ppl['fullname'];
			$_SESSION['user_email']=$rows_ppl['email'];
			header("location:main.php");
		}
		else{
			$errormsg="<font color='red'>Invalid Login, try again</font>";
		}
	}
	else{
		$errormsg="<font color='red'>Invalid Login, try again!</font>";
	}
}


/************ END: when submit button is clicked. **********/



/************ START: HTML BODY **********/
 	$page = new Page("Title here");
	$page->Header();

	$page->CloseHeader();
	$page->Body();
?>	
 <table width="100%" border="0" cellspacing="0" cellpadding="2" valign="top">
  <tr>
      <td colspan='2' scope="row" bgcolor="lightblue" align=left width=94%><font size=5 color='#000000' style='font-family:verdana'><strong>Site Title here</strong>
	  </font></td>
  </tr>
 </table>
<p><br></p><p><br></p><p><br></p>
<table align='center' width='35%' bgcolor='lightblue' cellpadding="3">
<form name=login method=post action=index.php>
<input type='hidden' name='ispostback' value='1'>
<tr><td colspan='2' align='center'><font size='2' face='verdana' color='#000000'><b>Enter your Login Information</b></font>
	<br><b><?php echo $errormsg?></b>
	</td></tr><tr>
	<td colspan='2'><hr></td>
	</tr>
 <tr><td align='right'>
	<font color='#000000'>Username:</font>
	</td>
	<td>
	<input type=text name='fusername' size='25'>
	</td>
 </tr>
	<tr>
	<td align='right'><font color='#000000'>Password:</font>
	</td>
	<td><input type=password name='fpassword' size='25'></td>
 </tr>
 <tr>
	<td>&nbsp;</td>
	<td>
	<input type=submit class='button' name=submit value="Login">
</td></tr>
 <tr>
	<td colspan='2'>&nbsp;</td>
	</tr>
</form>
<?php
	$page->footer(); 
?>

