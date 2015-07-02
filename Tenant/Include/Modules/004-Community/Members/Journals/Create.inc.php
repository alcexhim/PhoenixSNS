<?php
	if ($_POST["attempt"] != null && $validated_journal_name == null && $_POST["journal_title"] != null)
	{
		$result = Journal::Create($_POST["journal_name"], $_POST["journal_title"], $_POST["journal_description"]);
		if (!$result)
		{
			$page = new PsychaticaErrorPage();
			$page->BeginContent();
		?>
		<h1>Error</h1>
		<p><?php echo(mysql_errno() . ": " . mysql_error()); ?></p>
		<?php
			$page->EndContent();
			return;
		}
		else
		{
			System::Redirect("~/community/members/" . $thisuser->ShortName . "/journals/" . $_POST["journal_name"]);
			return;
		}
	}
?>
<div class="ProfilePage">
	<?php $journals = Journal::GetByUser($thisuser); ?>
	<div class="Panel">
		<h3 class="PanelTitle">Journal Properties</h3>
		<div class="PanelContent">
			<form action="create.mmo" method="POST">
				<input type="hidden" name="attempt" value="1" />
				<table style="margin-left: auto; margin-right: auto;">
					<tr>
						<td><label for="txtJournalName">Journal <u>n</u>ame:</label></td>
						<td><input type="text" name="journal_name" id="txtJournalName" maxlength="50" accesskey="n" /></td>
					</tr>
					<tr>
						<td><label for="txtJournalTitle">Journal <u>t</u>itle:</label></td>
						<td><input type="text" name="journal_title" id="txtJournalTitle" maxlength="50" accesskey="t" /></td>
					</tr>
					<tr>
						<td><label for="txtJournalDescription">Journal <u>d</u>escription (optional):</label></td>
						<td><textarea name="journal_description" id="txtJournalDescription" accesskey="d"></textarea></td>
					</tr>
					<tr>
						<td><label for="txtJournalIcon">Journal <u>i</u>con (optional):</label></td>
						<td><input type="file" name="journal_icon" id="txtJournalIcon" accesskey="i" /></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: right;">
							<input type="submit" value="Save Changes" />
							<a class="Button" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals")); ?>">Cancel</a>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>