<?php
	if ($CurrentUser == null)
	{
		System::Redirect("~/account/login");
		return;
	}
	
	if ($_POST["attempt"] != null && $_POST["name"] != null)
	{
		$validation_result_name = Page::ValidateName($_POST["name"]);
	}
	
	if ($_POST["attempt"] != null && $_POST["name"] != null && $_POST["title"] != null)
	{
		// Create the page in the database
		if ($validation_result_name == null)
		{
			if (!Page::Create($_POST["name"], $_POST["title"], $_POST["description"]))
			{
				$errno = mysql_errno();
				$error = mysql_error();
				
				$page = new PsychaticaErrorPage();
				$page->ErrorCode = $errno;
				$page->ErrorDescription = $error;
				$page->ReturnButtonURL = "~/community/pages/create.mmo";
				$page->ReturnButtonText = "Return to Create a Page";
				$page->Render();
				return;
			}
			
			System::Redirect("~/community/pages/" . $_POST["name"]);
			return;
		}
	}
	
	$page = new PsychaticaWebPage("Create a Page");
	$page->BeginContent();
	?>
	<div class="Panel">
		<h3 class="PanelTitle">Page Properties</h3>
		<div class="PanelContent">
			<form action="create.mmo" method="POST">
				<input type="hidden" name="attempt" value="1" />
				<table style="margin-left: auto; margin-right: auto;">
					<tr>
						<td><label for="txtPageName">Page <u>n</u>ame:</label></td>
						<td><input type="text" id="txtPageName" name="name" accesskey="n" style="width: 100%"<?php
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
						<td colspan="2" class="InlineError">Please enter a name for the page.</td>
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
						<td><label for="txtPageTitle">Page <u>t</u>itle:</label></td>
						<td><input type="text" id="txtPageTitle" name="title" accesskey="t" style="width: 100%"<?php
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
						<td colspan="2" class="InlineError">Please enter a title for the page.</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td><label for="txtPageDescription">Page <u>d</u>escription:</label></td>
						<td><textarea id="txtPageDescription" name="description" accesskey="d" cols="40" rows="5"></textarea></td>
					</tr>
					<tr>
						<td>Invite friends to page:</td>
						<td>
						<?php
						/*
							$ttx = new AutoSuggestTextBox();
							$ttx->RequireSelectionFromChoices = true;
							$ttx->Name = "txtPromoteToFriends";
							$ttx->PlaceholderText = "Enter a friend's name...";
							$ttx->Render();
						*/
						?>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: right;">
							<input type="submit" value="Finish" />
							<a class="Button" href="<?php echo(System::ExpandRelativePath("~/community/pages")); ?>">Cancel</a>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<?php
	$page->EndContent();
?>