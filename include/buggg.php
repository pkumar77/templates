<div class="darkenBackground" name="darkBackgroundLayer" id="darkBackgroundLayer" style="display:none"></div>
<div id='bugggFormSection' name='bugggFormSection' style='width: 350px; height:330px; display:none; top: 20%; left: 20%; position:fixed; background-color:white; layer-background-color:#003366;padding: 1em;border-style: solid; overflow-hidden; z-index:99999;'>
	<h3>Report an Issue</h3>
	<form id="bugggForm" name="bugggForm">
		<p>Please describe the issue below</p>
		<textarea name="bugggDescription" id="bugggDescription" style="border:1px solid black;width:320px;height:175px;"></textarea>
		<br><br>
		<input class="submitButton" type="button" value="Send" onclick="closeBuggg(1);">&nbsp;&nbsp;&nbsp;<input class="submitButton" type="button" value="Cancel" onclick="closeBuggg(0);">
		<br><br>
		<input type='hidden' id='user_fullname' name='user_fullname' value='<?php echo $_SESSION['fullname']?>'>
	</form>
</div>