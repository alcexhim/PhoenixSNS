<?php
	if ($_POST["attempt"] != null && $_POST["entry_name"] != null)
	{
		$validate_entry_name = JournalEntry::ValidateName($_POST["entry_name"]);
	}
	if ($validate_entry_name == null && $_POST["attempt"] != null && $_POST["entry_name"] != null && $_POST["entry_title"] != null)
	{
		$result = $entry->Modify($_POST["entry_name"], $_POST["entry_title"], $_POST["entry_content"]);
		
		if (!$result)
		{
			page_begin("Error");
			?>
			<p>
				<?php echo (mysql_errno() . ": " . mysql_error()); ?>
			</p>
			<p style="text-align: center;">
				<a href="/community/members/<?php echo($thisuser->Name); ?>/journals/<?php echo($journal->Name); ?>/entries/create.mmo">Return to Create Entry</a>
			</p>
			<?php
			page_end();
			return;
		}
		
		header("Location: /community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries/" . $_POST["entry_name"]);
		return;
	}
	page_begin($entry->Name . " | " . $journal->Name . " | " . $thisuser->LongName . "'s Journal");
?>
<div class="Panel">
	<h3 class="PanelTitle">Journal Entry Properties</h3>
	<div class="PanelContent">
		<form action="modify.mmo" method="POST">
			<p>
				Entry name and title must be 50 characters or less. Name must be alphanumeric
				characters only with no spaces.
			</p>
			<input type="hidden" name="attempt" value="1" />
			<table style="margin-left: auto; margin-right: auto;">
				<tr>
					<td><label for="txtName">Entry <u>n</u>ame (for URLs):</label></td>
					<td><input type="text" id="txtName" name="entry_name" accesskey="n" value="<?php echo($entry->Name); ?>" style="width: 100%" /></td>
				</tr>
				<?php
				if ($_POST["attempt"] != null && $_POST["entry_name"] == "")
				{
				?>
				<tr>
					<td colspan="2" class="InlineError">You must enter an entry name.</td>
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
					<td><label for="txtTitle">Entry <u>t</u>itle (for display):</label></td>
					<td><input type="text" id="txtTitle" name="entry_title" accesskey="t" value="<?php echo($entry->Title); ?>" style="width: 100%" /></td>
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
					<td><label for="txtContent">Entry <u>c</u>ontent:</label></td>
					<td><textarea id="txtContent" name="entry_content" accesskey="d" rows="6" cols="50"><?php echo($entry->Content); ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: right;">
						<input type="submit" value="Save Changes" />
						<a class="Button" href="/community/members/<?php echo($thisuser->ShortName); ?>/journals/<?php echo($journal->Name); ?>/entries/<?php echo($entry->Name); ?>">Cancel</a>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php
	page_end();
?>