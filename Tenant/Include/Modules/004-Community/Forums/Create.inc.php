<?php
	if ($CurrentUser == null)
	{
		System::Redirect("~/account/login");
		return;
	}
	
	if ($_POST["attempt"] != null && $_POST["name"] != null)
	{
		$validation_result_name = Forum::ValidateName($_POST["name"]);
	}
	
	if ($_POST["attempt"] != null && $_POST["name"] != null && $_POST["title"] != null)
	{
		// Create the group in the database
		if ($validation_result_name == null)
		{
			if (!Forum::Create($_POST["name"], $_POST["title"], $_POST["description"], $CurrentUser))
			{
				$page = new PsychaticaErrorPage();
				$page->ErrorCode = mysql_errno();
				$page->ErrorDescription = mysql_error();
				$page->ReturnButtonURL = "~/community/forums/create.mmo";
				$page->ReturnButtonText = "Return to Create a Forum";
				$page->Render();
				return;
			}
			
			System::Redirect("~/community/forums/" . $_POST["name"]);
			return;
		}
	}
	
	$page = new PsychaticaWebPage("Create a Forum");
	$page->BeginContent();
	?>
	<div class="Panel">
		<h3 class="PanelTitle">Forum Properties</h3>
		<div class="PanelContent">
			<form action="create.mmo" method="POST">
				<input type="hidden" name="attempt" value="1" />
				<table style="margin-left: auto; margin-right: auto;">
					<tr>
						<td><label for="txtTitle">Forum <u>t</u>itle:</label></td>
						<td><input type="text" id="txtTitle" name="title" accesskey="t" onkeyup="AutoGenerateName('txtTitle', 'txtName');" style="width: 100%"<?php
						if ($_POST["title"] != null)
						{
							echo(" value=\"" . $_POST["title"] . "\"");
						}
						?> /></td>
					</tr>
					<?php
					if ($_POST["attempt"] != null && $_POST["title"] == "")
					{
					?>
					<tr>
						<td colspan="2" class="InlineError">Please enter a title for the forum.</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td><label for="txtName">Forum <u>n</u>ame:</label></td>
						<td><input type="text" id="txtName" name="name" accesskey="n" onkeyup="AutoGenerateNameInvalidate('txtName');" style="width: 100%"<?php
						if ($_POST["name"] != null)
						{
							echo(" value=\"" . $_POST["name"] . "\"");
						}
						?> /></td>
					</tr>
					<?php
					if ($_POST["attempt"] != null && $_POST["name"] == "")
					{
					?>
					<tr>
						<td colspan="2" class="InlineError">Please enter a name for the forum.</td>
					</tr>
					<?php
					}
					else if ($_POST["attempt"] != null && $validation_result_name != null)
					{
					?>
					<tr>
						<td colspan="2" class="InlineError"><?php echo($validation_result_name); ?></td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td><label for="txtDescription">Forum <u>d</u>escription:</label></td>
						<td><textarea id="txtDescription" name="description" accesskey="d" cols="40" rows="5"></textarea></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: right;">
							<input type="submit" value="Finish" />
							<a class="Button" href="<?php echo(System::ExpandRelativePath("~/community/forums")); ?>">Cancel</a>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<?php
	$page->EndContent();
?>