<?php
	$journal = Journal::GetByIDOrName($path[3]);
	if ($journal == null)
	{
		$page = new PsychaticaErrorPage();
		$page->Message = "The journal does not exist. It may have been deleted, or you may have spelled the name wrong.";
		$page->ReturnButtonURL = "~/community/members/" . $thisuser->ShortName . "/journals";
		$page->ReturnButtonText = "Return to " . $thisuser->LongName . "'s Journals";
		$page->Render();
		return;
	}
	if ($journal->Creator->ID != $CurrentUser->ID)
	{
		$page = new PsychaticaErrorPage();
		$page->Message = "You are not allowed to do that. If you believe you are seeing this message in error, you may contact Psychatica Support.";
		$page->ReturnButtonURL = "~/community/members/" . $thisuser->ShortName . "/journals";
		$page->ReturnButtonText = "Return to " . $thisuser->LongName . "'s Journals";
		$page->Render();
		return;
	}
	
	if ($_POST["attempt"] != null)
	{
		$result = $journal->Remove();
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
			header("Location: /community/members/" . $thisuser->ShortName . "/journals");
			return;
		}
	}
	page_begin("Remove Journal");
?>
<div class="Panel">
	<h3 class="PanelTitle">Confirm Journal Removal</h3>
	<div class="PanelContent">
		<p>Are you sure you want to remove the Journal &quot;<?php echo($journal->Title); ?>&quot;? Please note that once the Journal is removed, all of its content, including entries and comments on those entries, will be <strong>LOST FOREVER</strong>.</p>
		<form action="remove.mmo" method="POST">
			<input type="hidden" name="attempt" value="1" />
			<table style="margin-left: auto; margin-right: auto;">
				<tr>
					<td colspan="2" style="text-align: right;">
						<input type="submit" value="Remove Journal" />
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