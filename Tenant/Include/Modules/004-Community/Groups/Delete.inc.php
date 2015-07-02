<?php
	if ($thisgroup->Creator->ID != $CurrentUser->ID)
	{
		$errorPage = new PsychaticaErrorPage();
		$errorPage->Message = "You must be the group creator to perform this action.";
		$errorPage->ReturnButtonURL = "~/community/groups/" . $thisgroup->Name;
		$errorPage->ReturnButtonText = "Return to Group";
		$errorPage->Render();
		return;
	}
	else
	{
		if ($_POST["confirm"] == "1")
		{
			$thisgroup->Delete();
			System::Redirect("~/community/groups");
		}
		else
		{
			$page = new PsychaticaWebPage();
			$page->BeginContent();
?>
<form action="delete.phnx" method="POST">
	<input type="hidden" name="confirm" value="1" />
	<div class="Panel">
		<h3 class="PanelTitle">Are you sure you want to delete this group?</h3>
		<div class="PanelContent">
			<p><?php echo($thisgroup->Title); ?></p>
			<p>If you delete this group, you will not be able to recover any of its content, including topics and comments.</p>
			<p style="text-align: center;"><input type="submit" value="Delete Group" /> <a class="Button" href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $thisgroup->Name)); ?>">Cancel</a></p>
		</div>
	</div>
</form>
<?php
			$page->EndContent();
			return;
		}
	}
?>