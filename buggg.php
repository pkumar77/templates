<!-- display bugggs form here-->
<div id='bugggFormSection' name='bugggFormSection' style='width: 350px; height:300px; display:none; top: 20%; left: 20%; position:fixed; background-color:white; layer-background-color:#003366;padding: 1em;border-style: solid; overflow:hidden; z-index:99999;'>
	<h3>Report a bug</h3>
	<form id="bugggForm" name="bugggForm">
		<p>Please describe the issue below</p>
		<textarea name="bugggDescription" id="bugggDescription" style="resize:none;border:1px solid black;width:320px;height:175px;min-height:175px;"></textarea>
		<br><br>
		<input class="submitButton" type="button" value="Send" onclick="closeBuggg(1);">&nbsp;&nbsp;&nbsp;<input class="submitButton" type="button" value="Cancel" onclick="closeBuggg(0);">
	</form>
</div>

<div id='commentFormSection' name='commentFormSection' style='width: 350px; height:350px; display:none; top: 20%; left: 20%; position:fixed; background-color:white; layer-background-color:#003366;padding: 1em;border-style: solid; overflow:hidden; z-index:99999;'>
	<h3>Feedback</h3>
	<form id="commentForm" name="commentForm">
		<p>Please describe the issue below</p>
		<input type="hidden" name="commentID" id="commentID">
		<select name="commentReason" id="commentReason">
			<option value="Improvement">Template Improvement</option>
			<option value="Bug Report">Report a Bug</option>
			<option value="Suggestion">Other Suggestion</option>
		</select><br><br>
		<textarea name="commentDescription" id="commentDescription" style="resize:none;border:1px solid black;width:320px;height:175px;min-height:175px;"></textarea>
		<br><br>
		<input class="submitButton" type="button" value="Send" onclick="closeCommentary(1);">&nbsp;&nbsp;&nbsp;<input class="submitButton" type="button" value="Cancel" onclick="closeCommentary(0);">
	</form>
</div>
