<?php
	if ($entry == null)
	{
		page_begin("Error: Journal entry does not exist.");
	?>
	<div class="Panel">
		<h3 class="PanelTitle">Error</h3>
		<div class="PanelContent">
			<p>
				The journal entry does not exist. It may have been deleted, or you may have spelled the name wrong.
			</p>
			<p style="text-align: center;"><a href="/community/members/<?php echo($thisuser->ShortName); ?>/journals/<?php echo($journal->Name); ?>">Return to &quot;<?php echo($journal->Title); ?>&quot; Journal</a></p>
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
	
	if ($_POST["attempt"] != null)
	{
		$result = $entry->Remove();
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
			header("Location: /community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name);
			return;
		}
	}
	page_begin("Remove Journal Entry");
?>
<div class="Panel">
	<h3 class="PanelTitle">Confirm Journal Entry Removal</h3>
	<div class="PanelContent">
		<p>Are you sure you want to remove the Journal Entry &quot;<?php echo($entry->Title); ?>&quot;? Please note that once the Journal Entry is removed, all of its content, including comments, will be <strong>LOST FOREVER</strong>.</p>
		<form action="remove.mmo" method="POST">
			<input type="hidden" name="attempt" value="1" />
			<table style="margin-left: auto; margin-right: auto;">
				<tr>
					<td colspan="2" style="text-align: right;">
						<input type="submit" value="Remove Entry" />
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