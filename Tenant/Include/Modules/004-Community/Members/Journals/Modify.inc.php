<?php
	$journal = Journal::GetByIDOrName($path[3]);
	if ($journal == null)
	{
		page_begin("Error: Journal does not exist.");
	?>
	<div class="Panel">
		<h3 class="PanelTitle">Error</h3>
		<div class="PanelContent">
			<p>
				The journal does not exist. It may have been deleted, or you may have spelled the name wrong.
			</p>
			<p style="text-align: center;"><a href="/community/members/<?php echo($thisuser->ShortName); ?>/journals">Return to <?php echo($thisuser->LongName); ?>'s Journals</a></p>
		</div>
	</div>
	<?php
		page_end();
		return;
	}
	if ($journal->Creator->ID != $CurrentUser->ID)
	{
		page_begin("Insufficient Privileges");
	?>
	<div class="Panel">
		<h3 class="PanelTitle">Insufficient Privileges</h3>
		<div class="PanelContent">
			<p>
				You are not allowed to do that. If you believe you are seeing this message in error, you may contact Psychatica Support.
			</p>
			<p style="text-align: center;"><a href="/community/members/<?php echo($thisuser->ShortName); ?>/journals">Return to <?php echo($thisuser->LongName); ?>'s Journals</a></p>
		</div>
	</div>
	<?php
		page_end();
		return;
	}
	
	if ($_POST["attempt"] != null && $validated_journal_name == null && $_POST["journal_title"] != null)
	{
		$result = $journal->Modify($journal, $_POST["journal_name"], $_POST["journal_title"], $_POST["journal_description"]);
		if (!$result)
		{
			page_begin("Error");
		?>
		<h1>Error</h1>
		<p><?php echo(mysql_errno() . ": " . mysql_error()); ?></p>
		<?php
			page_end();
			return;
		}
		else
		{
			header("Location: /community/members/" . $thisuser->ShortName . "/journals/" . $_POST["journal_name"]);
			return;
		}
	}
	page_begin("Modify Journal");
?>
<div class="Panel">
	<h3 class="PanelTitle">Journal Properties</h3>
	<div class="PanelContent">
		<form action="modify.mmo" method="POST">
			<input type="hidden" name="attempt" value="1" />
			<table style="margin-left: auto; margin-right: auto;">
				<tr>
					<td><label for="txtJournalName">Journal <u>n</u>ame:</label></td>
					<td><input type="text" name="journal_name" id="txtJournalName" maxlength="50" accesskey="n" value="<?php echo($journal->Name); ?>" /></td>
				</tr>
				<tr>
					<td><label for="txtJournalTitle">Journal <u>t</u>itle:</label></td>
					<td><input type="text" name="journal_title" id="txtJournalTitle" maxlength="50" accesskey="t" value="<?php echo($journal->Title); ?>" /></td>
				</tr>
				<tr>
					<td><label for="txtJournalDescription">Journal <u>d</u>escription (optional):</label></td>
					<td><textarea name="journal_description" id="txtJournalDescription" accesskey="d"><?php echo($journal->Description); ?></textarea></td>
				</tr>
				<tr>
					<td><label for="txtJournalIcon">Journal <u>i</u>con (optional):</label></td>
					<td><input type="file" name="journal_icon" id="txtJournalIcon" accesskey="i" /></td>
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
	page_end();
?>