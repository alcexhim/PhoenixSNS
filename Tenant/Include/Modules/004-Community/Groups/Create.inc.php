<?php
	use WebFX\System;
	use WebFX\Controls\TextBox;
	
	use PhoenixSNS\MasterPages\WebPage;
	use PhoenixSNS\Pages\ErrorPage;
	
	if ($CurrentUser == null)
	{
		System::Redirect(System::GetConfigurationValue("Account.LoginPath"));
		return;
	}
	
	if ($_POST["attempt"] != null && $_POST["name"] != null)
	{
		$validation_result_name = Group::ValidateName($_POST["name"]);
	}
	
	if ($_POST["attempt"] != null && $_POST["name"] != null && $_POST["title"] != null)
	{
		// Create the group in the database
		if ($validation_result_name == null)
		{
			if (!Group::Create($_POST["name"], $_POST["title"], $_POST["description"]))
			{
				$page = new ErrorPage();
				$page->ErrorCode = mysql_errno();
				$page->ErrorDescription = mysql_error();
				$page->ReturnButtonURL = "~/community/groups/create.mmo";
				$page->ReturnButtonText = "Return to Create a Group";
				$page->Render();
				return;
			}
			
			System::Redirect("~/community/groups/" . $_POST["name"]);
			return;
		}
	}
	
	$page = new WebPage("Create a Group");
	$page->BeginContent();
	?>
	<div class="CardSet Center">
		<form id="frmGroupProperties" method="POST">
			<div class="Card">
				<div class="Title">Group Properties</div>
				<div class="Content">
					<input type="hidden" name="attempt" value="1" />
					<table style="margin-left: auto; margin-right: auto;">
						<tr>
							<td><label for="txtGroupTitle">Group <u>t</u>itle:</label></td>
							<td><input type="text" id="txtGroupTitle" name="title" accesskey="t" onkeyup="AutoGenerateName('txtGroupTitle', 'txtGroupName');" style="width: 100%"<?php
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
							<td>&nbsp;</td>
							<td class="InlineError">Please enter a title for the group.</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td><label for="txtGroupName">Group <u>n</u>ame:</label></td>
							<td><input type="text" id="txtGroupName" name="name" accesskey="n" onkeyup="AutoGenerateNameInvalidate('txtGroupName');" style="width: 100%"<?php
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
							<td>&nbsp;</td>
							<td class="InlineError">Please enter a name for the group.</td>
						</tr>
						<?php
						}
						else if ($_POST["attempt"] != null && $validation_result_name != null)
						{
						?>
						<tr>
							<td>&nbsp;</td>
							<td class="InlineError"><?php echo($validation_result_name); ?></td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td><label for="txtGroupDescription">Group <u>d</u>escription:</label></td>
							<td><textarea id="txtGroupDescription" name="description" accesskey="d" cols="40" style="width: 100%;" rows="5"></textarea></td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="checkbox" id="chkGroupRequireAuthorization" name="group_require_auth" />
								<label for="chkGroupRequireAuthorization">Require authorization to join group</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="checkbox" id="chkGroupInvisible" name="group_invisible" />
								<label for="chkGroupInvisible">Make group invisible to non-members (invite only)</label>
							</td>
						</tr>
						<tr>
							<td>Invite friends to group:</td>
							<td>
							<?php
								$ttx = new TextBox("txtPromoteToFriends");
								$ttx->RequireSelectionFromChoices = true;
								$ttx->EnableMultipleSelection = true;
								$ttx->Name = "txtPromoteToFriends";
								$ttx->PlaceholderText = "Enter a friend's name...";
								$ttx->SuggestionURL = System::ExpandRelativePath("~/API/Search.php?format=json&query=%1&include=Users");
								$ttx->Render();
							?>
							<script type="text/javascript">
								txtPromoteToFriends.FormatStart = function()
								{
									var html = "";
									// html += "<img src=\"/images/logowntr.png\" style=\"width: 320px; display: block;\" />";
									html += "<div class=\"Menu\" style=\"max-height: 300px;\">";
									return html;
								};
								
								var lastItemCategory = "";
								txtPromoteToFriends.FormatItem = function(item)
								{
									return "<a class=\"MenuItem\" href=\"#\" onclick=\"txtPromoteToFriends.AddItem({ 'ID': '" + item.Item.ID + "', 'ShortName': '" + item.Item.ShortName + "', 'LongName': '" + item.Item.LongName + "' }); return false;\"><img src=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/" + item.Item.ShortName + "/images/avatar/thumbnail.png\" style=\"width: 24px;\" /> " + item.Item.LongName + "</a>";
								};
								txtPromoteToFriends.FormatItemText = function(item)
								{
									return "<img src=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/" + item.ShortName + "/images/avatar/thumbnail.png\" style=\"width: 24px;\" /> " + item.LongName;
								};
								txtPromoteToFriends.FormatEnd = function()
								{
									lastItemCategory = "";
									return "</div>";
								};
							</script>
							</td>
						</tr>
					</table>
				</div>
				<div class="Actions Horizontal">
					<a href="#" onclick="document.getElementById('frmGroupProperties').submit(); return false;"><i class="fa fa-save"></i> <span class="Text">Save Changes</span></a>
					<a href="<?php echo(System::ExpandRelativePath("~/community/groups")); ?>"><i class="fa fa-times"></i> <span class="Text">Cancel</span></a>
				</div>
			</div>
		</form>
	</div>
	<?php
	$page->EndContent();
?>