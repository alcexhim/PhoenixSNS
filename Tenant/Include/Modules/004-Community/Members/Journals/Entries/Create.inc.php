<?php
	if ($_POST["attempt"] != null && $_POST["entry_name"] != null)
	{
		$validate_entry_name = JournalEntry::ValidateName($_POST["entry_name"]);
	}
	if ($validate_entry_name == null && $_POST["attempt"] != null && $_POST["entry_name"] != null && $_POST["entry_title"] != null)
	{
		$result = $journal->CreateEntry($_POST["entry_name"], $_POST["entry_title"], $_POST["entry_content"]);
		
		if (!$result)
		{
			$page = new PsychaticaErrorPage();
			$page->Message = "A database error occurred.";
			$page->ErrorCode = mysql_errno();
			$page->ErrorDescription = mysql_error();
			$page->ReturnButtonURL = "~/community/members/" . $thisuser->Name . "/journals/" . $journal->Name . "/entries/create.mmo";
			$page->ReturnButtonText = "Return to " . mysql_error() . "Create an Entry";
			
			$page->Render();
			return;
		}
		
		System::Redirect("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries/" . $_POST["entry_name"]);
		return;
	}
	
	/*
	$page = new PsychaticaWebPage("Create Entry | " . $journal->Name . " | " . $thisuser->LongName . "'s Journal");
	$page->BeginContent();
	*/
?>
<div class="Panel">
	<h3 class="PanelTitle">Journal Entry Properties</h3>
	<div class="PanelContent">
		<form action="create.mmo" method="POST">
			<p>
				Entry name and title must be 50 characters or less. Name must be alphanumeric
				characters only with no spaces.
			</p>
			<input type="hidden" name="attempt" value="1" />
			<table style="margin-left: auto; margin-right: auto;">
				<tr>
					<td><label for="txtTitle">Entry <u>t</u>itle:</label></td>
					<td><input type="text" id="txtTitle" name="entry_title" accesskey="t" onkeyup="AutoGenerateName('txtTitle', 'txtName');" <?php if ($_POST["entry_title"] != null) echo("value=\"" . $_POST["entry_title"] . "\""); ?>/></td>
				</tr>
				<?php
				if ($_POST["attempt"] != null && $_POST["entry_title"] == "")
				{
				?>
				<tr>
					<td colspan="2" class="InlineError">You must enter an entry title.</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td><label for="txtName">Entry <u>U</u>RL:</label></td>
					<td><input type="text" id="txtName" name="entry_name" accesskey="n" onkeyup="AutoGenerateNameInvalidate('txtName');" <?php if ($_POST["entry_name"] != null) echo("value=\"" . $_POST["entry_name"] . "\""); ?>/></td>
				</tr>
				<?php
				if ($_POST["attempt"] != null && $_POST["entry_name"] == "")
				{
				?>
				<tr>
					<td colspan="2" class="InlineError">You must enter an entry URL.</td>
				</tr>
				<?php
				}
				else if ($validate_entry_name != null)
				{
				?>
				<tr>
					<td colspan="2" class="InlineError"><?php echo($validate_entry_name); ?></td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td><label for="txtContent">Entry <u>c</u>ontent:</label></td>
					<td><textarea id="txtContent" name="entry_content" accesskey="d"><?php if ($_POST["entry_content"] != null) echo($entry_content); ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: right;">
						<input type="submit" value="Save Changes" />
						<a class="Button" href="/community/members/<?php echo($thisuser->ShortName); ?>/journals/<?php echo($journal->Name); ?>">Cancel</a>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php
	/*
	$page->EndContent();
	*/
?>