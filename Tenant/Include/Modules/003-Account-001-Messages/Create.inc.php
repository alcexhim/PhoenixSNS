<?php
	use WebFX\System;
	use WebFX\Controls\TextBox;
	
	use PhoenixSNS\Objects\Message;
	
	if ($path[1] == "create.mmo" && $_SERVER["REQUEST_METHOD"] == "POST" && $_POST["attempt"] == "1")
	{
		// create the message in the server
		$sender = $CurrentUser;
		$receivers = array();
		
		if ($_POST["message_receiver"] != null)
		{
			$receiver = User::GetByShortName($_POST["message_receiver"]);
			$receivers[] = $receiver;
		}
		else if ($_POST["message_receivers"] != null)
		{
			$ids = explode(",", $_POST["message_receivers"]);
			foreach ($ids as $id)
			{
				$receiver = User::GetByID($id);
				if ($receiver == null) continue;
				
				$receivers[] = $receiver;
			}
		}
		
		if (count($receivers) == 0)
		{
			$page = new PsychaticaErrorPage();
			$page->Message = "You must provide at least one user to receive the message.";
			$page->Render();
			return;
		}
		
		Message::Create($sender, $receivers, $_POST["message_title"], $_POST["message_content"]);

		global $MySQL;
		if ($MySQL->errno != 0)
		{
			$page = new PsychaticaErrorPage();
			$page->ErrorCode = $MySQL->errno;
			$page->ErrorDescription = $MySQL->error;
			$page->Render();
			return;
		}
		else if ($_POST["message_receivers"] != null)
		{
			/*
			$receiver = User::GetByShortName($_POST["message_receiver"]);
			Message::Create($sender, $receiver, $_POST["message_title"], $_POST["message_content"]);
		
			if (mysql_errno() != 0)
			{
				$errno = mysql_errno();
				$error = mysql_error();
				
				page_begin("Error");
				?>
				<p><?php echo($errno . ": " . $error); ?></p>
				<?php
				page_end();
				return;
			}
			*/
		}
		
		System::Redirect("~/account/messages/outbox");
		return;
	}
	
	$page = new PsychaticaWebPage("Compose Message | Message Center");
	$page->BeginContent();
	
	/*
	$window = new Window("wndMessageAttachmentProperties", "Attachment Properties");
	$window->Width = 300;
	
	$window->BeginRender();
?>
<script type="text/javascript">
	function cmdSaveChanges_Click()
	{
		alert('submit form');
		wndMessageAttachmentProperties.Close();
	}
	function cmdCancel_Click()
	{
		wndMessageAttachmentProperties.Close();
	}
</script>
<table style="margin-left: auto; margin-right: auto;">
	<tr>
		<td><label for="txtAttachmentTitle">Attachment <u>t</u>itle:</label></td>
		<td><input type="text" name="attachment_title" id="txtAttachmentTitle" /></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: right;">
			<a class="Button" onclick="cmdSaveChanges_Click();">Save Changes</a>
			<a class="Button" onclick="cmdCancel_Click();">Cancel</a>
		</td>
	</tr>
</table>
<?php
	$window->EndRender();
	*/
?>
<div class="Panel">
	<h3 class="PanelTitle">Private Messages</h3>
	<div class="PanelContent">
		<table style="width: 100%">
			<tr>
				<td style="width: 25%; vertical-align: top;">
					<div class="ActionList">
						<a href="<?php echo(System::ExpandRelativePath("~/account/messages/inbox")); ?>">Inbox</a>
						<a href="<?php echo(System::ExpandRelativePath("~/account/messages/outbox")); ?>">Sent Messages</a>
						<span class="Selected">Create Message</span>
					</div>
				</td>
				<td style="vertical-align: top;">
					<form name="MessageCreateForm" method="POST" style="display: block;">
						<input type="hidden" name="attempt" value="1" />
						<table style="width: 100%">
							<tr>
								<td style="width: 100px; padding-top: 4px;"><label for="txtReceiver">Receiver(s):</label></td>
								<td>
									<?php
									$txtReceiver = new TextBox("txtReceiver");
									$txtReceiver->RequireSelectionFromChoices = true;
									$txtReceiver->SuggestionURL = "~/API/Search.php?format=json&include=members&query=%1";
									$txtReceiver->EnableMultipleSelection = true;
									$txtReceiver->Render();
									/*
									if ($_POST["message_receivers"] != null)
									{
										// $_POST["message_receivers"] stores receiver ID's as comma-separated lists of ID numbers
										$receivers = explode(",", $_POST["message_receivers"]);
										foreach ($receivers as $receiver)
										{
											$user = User::GetByID($receiver);
											if ($user != null)
											{
												$txtReceiver->Items[] = new TypeaheadTextboxItem($user->LongName, $user);
											}
										}
									}
									*/
									/*
									<div class="TypeaheadTextbox TypeaheadTextboxMustSelect" id="TypeaheadTextbox_txtReceiver" onclick="document.getElementById('TypeaheadTextbox_txtReceiver_textbox').focus();">
										<div class="TypeaheadTextboxContent">
											<span class="TypeaheadTextboxSelectedItems" id="TypeaheadTextbox_txtReceiver_items">
											</span>
											<input type="text" id="TypeaheadTextbox_txtReceiver_textbox" name="message_receiver" onkeyup="TypeaheadTextboxRefreshSuggestions('txtReceiver', '/api/GetUserList.php?query=' + document.getElementById('TypeaheadTextbox_txtReceiver_textbox').value)" placeholder="Enter a name..."<?php
												if ($_POST["message_receiver"] != null)
												{
													$receiver = User::GetByID($_POST["message_receiver"]);
													echo(" value=\"" . $receiver->ShortName . "\"");
												}
											?>/>
										</div>
										<div class="TypeaheadTextboxSuggestionList" id="TypeaheadTextbox_txtReceiver_suggestions">
										<?php
										?>
										</div>
									</div>
									*/
									?>
									<script type="text/javascript">
										txtReceiver.FormatStart = function()
										{
											var html = "";
											// html += "<img src=\"/images/logowntr.png\" style=\"width: 320px; display: block;\" />";
											html += "<div class=\"Menu\" style=\"max-height: 300px;\">";
											return html;
										};
										txtReceiver.FormatItem = function(item)
										{
											var html = "";
											html += "<a class=\"MenuItem\" onclick=\"txtReceiver.AddItem('" + item.Item.LongName + "'); txtReceiver.ClearText();\" href=\"#\">";
											html += "<img src=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/" + item.Item.ShortName + "/images/avatar/thumbnail.png\" style=\"width: 24px;\" /> ";
											html += item.Item.LongName;
											html += "</a>";
											return html;
										};
										txtReceiver.FormatEnd = function()
										{
											return "</div>";
										};
									</script>
								</td>
							</tr>
							<tr>
								<td><label for="txtSubject">Subject:</label></td>
								<td><input type="text" name="message_title" style="width: 100%" /></td>
							</tr>
							<tr>
								<td>Content:</td>
								<td>
									<textarea style="width: 100%" rows="6" name="message_content"><?php echo($_POST["message_content"]); ?></textarea>
								</td>
							</tr>
							<tr>
								<td>Attachments:</td>
								<td>
									<div class="ProfilePage">
										<div class="ProfileTitle">
											<span class="ProfileUserName">0 attachments</span>
											<span class="ProfileControlBox">
												<a href="#" onclick="wndMessageAttachmentProperties.ShowDialog(); return false;">Add Attachment</a>
											</span>
										</div>
										<div class="ProfileContent">
											<div class="ListBox">
											</div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: right;">
									<input type="submit" value="Send Message" />
									<a class="Button" href="/account/messages">Cancel</a>
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
	$page->EndContent();
?>