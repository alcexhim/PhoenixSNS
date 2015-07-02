<?php
	use WebFX\System;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	use WebFX\Controls\TextBox;
	
	use PhoenixSNS\Objects\Language;
	use PhoenixSNS\Objects\LanguageString;
	use PhoenixSNS\Objects\User;
	use PhoenixSNS\Objects\UserProfileVisibility;
	
	use PhoenixSNS\MasterPages\WebPage;

	if ($path[0] != "")
	{
		System::Redirect("~/account/settings");
		return true;
	}
	
	$CurrentUser = User::GetCurrent();
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$valid_result = User::ValidateShortName($_POST["member_shortname"]);
		if ($valid_result != null)
		{
			$failure = true;
			$failure_message = $valid_result;
		}
		else
		{
			if ($_POST["member_realname"] == null && $_POST["member_shortname"] == null && $_POST["member_longname"] == null && $_POST["member_birthdate"] == null && $_POST["member_email"] == null)
			{
				$failure = true;
				$failure_message = "Please change one of the options";
			}
			else
			{
				$CurrentUser->RealName = $_POST["member_realname"];
				$CurrentUser->ShortName = $_POST["member_shortname"];
				$CurrentUser->LongName = $_POST["member_longname"];
				$CurrentUser->BirthDate = $_POST["member_birthdate"];
				$CurrentUser->EmailAddress = $_POST["member_email"];
				$CurrentUser->ProfileVisibility = UserProfileVisibility::FromIndex($_POST["member_profile_visibility"]);
				
				$result = $CurrentUser->Update();
				
				global $MySQL;
				
				if (!$result)
				{
					$failure = true;
					if ($MySQL->errno != 0)
					{
						$failure_message = $MySQL->errno . ": " . $MySQL->error;
					}
					else
					{
						$failure_message = mmo_error_get();
					}
				}
				else
				{
					$lang = Language::GetByID($_POST["member_language_id"]);
					$lang->SetDefaultForUser($CurrentUser);
					
					// force refresh the user information
					$CurrentUser = User::GetCurrent();
					
					if ($MySQL->errno != 0)
					{
						$failure = true;
						$failure_message = $MySQL->errno . ": " . $MySQL->error;
					}
					else
					{
						/*
						page_begin("Settings Saved");
						?>
						<p><?php echo(LanguageString::GetByName("success_settingssaved")); ?></p>
						<p style="text-align: center;"><a href="/">Return to Psychatica</a></p>
						<?php
						page_end();
						*/
						$success = true;
					}
				}
			}
		}
	}
	
	$page = new WebPage("Settings");
	$page->BeginContent();
?>
<form id="frmSettings" method="POST">
	<?php
	if ($success)
	{
	?>
	<div class="InlineMessage InlineMessageSuccess"><?php echo(LanguageString::GetByName("success_settingssaved")); ?></div>
	<?php
	}
	else if ($failure)
	{
	?>
	<div class="InlineMessage InlineMessageFailure"><?php echo($failure_message); ?></div>
	<?php
	}
	else
	{
	?>
	<div class="InlineMessage"></div>
	<?php
	}
	?>
	<div class="Card">
		<div class="Title"><?php echo(LanguageString::GetByName("personal")); ?></div>
		<div class="Content">
			<table style="width: 100%;">
				<tr>
					<td style="width: 200px"><label for="txtRealName"><?php echo(LanguageString::GetByName("realname_label")); ?></label></td>
					<td><input type="text" accesskey="R" id="txtRealName" name="member_realname" value="<?php echo($CurrentUser->RealName); ?>" /></td>
				</tr>
				<tr>
					<td><label for="txtBirthDate"><?php echo(LanguageString::GetByName("birthdate_label")); ?></label></td>
					<td><input type="text" accesskey="B" id="txtBirthDate" name="member_birthdate" value="<?php echo($CurrentUser->BirthDate); ?>" /></td>
				</tr>
				<tr>
					<td><label for="txtEmailAddress"><?php echo(LanguageString::GetByName("email_label")); ?></label></td>
					<td><input type="text" accesskey="E" id="txtEmailAddress" name="member_email" value="<?php echo($CurrentUser->EmailAddress); ?>" /></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="Card">
		<div class="Title"><?php echo(LanguageString::GetByName("appearance")); ?></div>
		<div class="Content">
			<div class="FormSection">
				<div class="FormItem">
					<label for="txtLongName"><?php echo(LanguageString::GetByName("longname_label")); ?></label>
					<input type="text" accesskey="D" id="txtLongName" name="member_longname" value="<?php echo($CurrentUser->LongName); ?>" />
				</div>
				<div class="FormItem">
					<label for="Textbox_cboDefaultPage_textbox">When I log in, take me to:</label>
					<?php
						/*
						<noscript>
							<select id="cboDefaultPage" name="member_default_page_url">
								<option value="~/account">Account</option>
								<option value="~/dashboard" selected="selected">Dashboard</option>
								<option value="~/market">Market</option>
								<option value="~/community/members/<?php echo($CurrentUser->ShortName); ?>">Profile</option>
								<option value="~/world">World</option>
							</select>
						</noscript>
						*/
						$textbox = new TextBox("cboDefaultPage");
						$textbox->EnableMultipleSelection = false;
						$textbox->RequireSelectionFromChoices = true;
						$textbox->SuggestionURL = System::ExpandRelativePath("~/API/Search.php?format=json&query=%1&include=StartPages");
						$textbox->Render();
					?>
					<script type="text/javascript">
					cboDefaultPage.FormatStart = function()
					{
						var html = "";
						// html += "<img src=\"/images/logowntr.png\" style=\"width: 320px; display: block;\" />";
						html += "<div class=\"Menu\" style=\"max-height: 300px;\">";
						return html;
					};
					
					var lastItemCategory = "";
					cboDefaultPage.FormatItem = function(item)
					{
						var html = "";
						html += "<a class=\"MenuItem\" href=\"#\" onclick=\"cboDefaultPage.AddItem(StartPage.GetByID(" + item.Item.ID + ")); cboDefaultPage.DropDown.Close();\">" + item.Item.Title + "</a>";
						return html;
					};
					cboDefaultPage.FormatItemText = function(item)
					{
						return item.Title;
					};
					cboDefaultPage.FormatEnd = function()
					{
						lastItemCategory = "";
						return "</div>";
					};
					</script>
				</div>
				<div class="FormItem">
					<label for="txtShortName"><?php echo(LanguageString::GetByName("shortname_label")); ?></label>
					<input type="text" accesskey="U" id="txtShortName" name="member_shortname" value="<?php echo($CurrentUser->ShortName); ?>" />
				</div>
				<div class="FormItem">
					<label for="cboLanguage"><?php echo(LanguageString::GetByName("language_label")); ?></label>
					<select id="cboLanguage" accesskey="L" name="member_language_id">
						<?php
							$languages = Language::Get();
							foreach ($languages as $language)
							{
						?>
						<option value="<?php echo($language->ID); ?>"<?php if ($language->ID == $CurrentUser->Language->ID) echo(" selected=\"selected\""); ?>><?php echo($language->Title); ?></option>
						<?php
							}
						?>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="Card">
		<div class="Title"><?php echo(LanguageString::GetByName("security")); ?></div>
		<div class="Content">
			<table style="width: 100%;">
				<tr>
					<td><label for="cboProfileVisibility"><?php echo(LanguageString::GetByName("profile_visibility_label")); ?></label></td>
					<td>
						<select id="cboProfileVisibility" accesskey="V" name="member_profile_visibility">
							<option value="4"<?php if ($CurrentUser->ProfileVisibility == UserProfileVisibility::Hidden) { echo(" selected=\"selected\""); } ?>>Hidden from everyone</option>
							<option value="3"<?php if ($CurrentUser->ProfileVisibility == UserProfileVisibility::Friends) { echo(" selected=\"selected\""); } ?>>Visible only to friends</option>
							<option value="2"<?php if ($CurrentUser->ProfileVisibility == UserProfileVisibility::ExtendedFriends) { echo(" selected=\"selected\""); } ?>>Visible to friends, and friends of friends</option>
							<option value="1"<?php if ($CurrentUser->ProfileVisibility == UserProfileVisibility::Sitewide) { echo(" selected=\"selected\""); } ?>>Visible to any member of Psychatica</option>
							<option value="0"<?php if ($CurrentUser->ProfileVisibility == UserProfileVisibility::Everyone) { echo(" selected=\"selected\""); } ?>>Visible to everyone on the Internet</option>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="Card">
		<div class="Title"><?php echo(LanguageString::GetByName("deactivate_account")); ?></div>
		<div class="Content">
			<table style="width: 100%;">
				<tr>
					<td colspan="2">
						<strong><?php echo(LanguageString::GetByName("use_with_caution")); ?></strong>
						<p>
							<?php echo(LanguageString::GetByName("deactivate_account_warning")); ?>
						</p>
						<p>
							<a href="<?php echo(System::ExpandRelativePath("~/account/settings/deactivate")); ?>"><?php echo(LanguageString::GetByName("deactivate_account_button")); ?></a>
						</p>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="Card">
		<div class="Actions Horizontal">
			<a href="#" onclick="document.getElementById('frmSettings').submit(); return false;" accesskey="S"><i class="fa fa-check"></i> <span class="Text"><?php echo(LanguageString::GetByName("button_savechanges")); ?></span></a>
			<a accesskey="C" href="<?php echo(System::ExpandRelativePath("~/")); ?>"><i class="fa fa-times"></i> <span class="Text"><?php echo(LanguageString::GetByName("button_cancel")); ?></span></a>
		</div>
	</div>
</form>
<?php
	$page->EndContent();
?>