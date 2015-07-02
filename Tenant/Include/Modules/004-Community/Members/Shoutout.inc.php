<?php
	use WebFX\System;
	use WebFX\Controls\Window;
	
	use PhoenixSNS\Objects\User;
	
	function RenderMessage($message, $alternate = false)
	{
		$praises = $message->GetPraises();
		$comments = $message->GetComments();
?>
		<div class="Comment<?php if ($alternate) echo(" Alternate"); ?>">
			<div class="CommentTitle">
				<?php echo($message->Content); ?>
			</div>
			<div class="CommentInformation">
				<span class="PostedBy">Posted by <span class="Author"><a target="_blank" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $message->Sender->ShortName)); ?>" onmousemove="hcUserInfo.Show(event.clientX, event.clientY, <?php echo($message->Sender->ID); ?>);" onmouseout="hcUserInfo.Hide();"><img style="height: 32px;" src="<?php echo(System::ExpandRelativePath("~/community/members/" . $message->Sender->ShortName . "/images/avatar/thumbnail.png")); ?>" /> <?php echo($message->Sender->ToString()); ?></a></span> on <span class="Timestamp"><?php echo($message->Timestamp); ?></span></span>
				<span class="CommentActions">
					<a onclick="wndCommentPrivacy.Show(<?php echo($message->ID); ?>); return false;" href="#">Privacy</a>
					|
					<a onclick="CommentManager.SetPopularity(1, 30, 1); return false;" href="#" id="Comment_30_actions_like">Like</a>
					|
					<a onclick="CommentManager.SetPopularity(1, 30, -1); return false;" href="#" id="Comment_30_actions_dislike">Dislike</a>
					|
					<a onclick="CommentManager.Reply(1, 30); return false;" href="#" id="Comment_30_actions_reply">Reply</a>
				</span>
			</div>
			<div class="CommentList">
			<?php
				foreach ($comments as $comment)
				{
					$comment->Render();
				}
			?>
			</div>
		</div>
			<?php
			/*
			
			<div class="ShoutoutEntryActionList">
				<time><?php echo($message->Timestamp); ?></time>
				<span style="float: right;">
					<?php
						if ($CurrentUser != null)
						{
						?>
						<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/shoutout/messages/" . $message->ID . "/praises.mmo")); ?>" onclick="DisplayPraiseDialog(<?php echo($message->ID); ?>); return false;"><?php echo(LanguageString::GetByName("praise")); ?> (<?php echo(count($praises)) ?>)</a> | <a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/shoutout/messages/" . $message->ID . "/comments.mmo")); ?>"><?php echo(LanguageString::GetByName("comment")); ?></a>
						<?php
						}
						if ($message->Sender->ID == $CurrentUser->ID)
						{
						?>
						| <a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/shoutout/messages/" . $message->ID . "/edit.mmo")); ?>"><?php echo(LanguageString::GetByName("edit")); ?></a> | <a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/shoutout/messages/" . $message->ID . "/remove.mmo")); ?>"><?php echo(LanguageString::GetByName("remove")); ?></a>
						<?php
						}
					?>
				</span>
			</div>
			*/
	}
	
	$path = System::GetVirtualPath();
	if (count($path) > 3 && $path[3] == "messages")
	{
		$message_id = $path[4];
		if (is_numeric($message_id))
		{
			switch ($path[5])
			{
				case "comments.mmo":
				{
					$message = ShoutoutMessage::GetByID($message_id);
					$comments = $message->GetComments();
					foreach ($comments as $comment)
					{
						$comment->Render();
					}
					return;
				}
				case "edit.mmo":
				{
					return;
				}
				case "praise.mmo":
				{
					$message = ShoutoutMessage::GetByID($message_id);
					$message->Like();
					
					$errno = mysql_errno();
					$error = mysql_error();
					if ($errno != 0)
					{
						?>
						<div class="Panel">
							<h3 class="PanelTitle">An error has occurred</h3>
							<div class="PanelContent">
								<p><?php echo($errno . ": " . $error); ?></p>
								<p style="text-align: center;"><a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName)); ?>">Return to <?php echo($thisuser->ToString()); ?>'s Profile</a></p>
							</div>
						</div>
						<?php
						page_end();
						return;
					}
					
					System::Redirect("~/community/members/" . $thisuser->ShortName . "/shoutout");
					return;
				}
				case "praises.mmo":
				{
					$message = ShoutoutMessage::GetByID($message_id);
					$praises = $message->GetPraises();
					
					$errno = mysql_errno();
					$error = mysql_error();
					if ($errno != 0)
					{
						?>
						<div class="Panel">
							<h3 class="PanelTitle">An error has occurred</h3>
							<div class="PanelContent">
								<p><?php echo($errno . ": " . $error); ?></p>
								<p style="text-align: center;"><a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName)); ?>">Return to <?php echo($thisuser->ToString()); ?>'s Profile</a></p>
							</div>
						</div>
						<?php
						page_end();
						return;
					}
					?>
					<div class="Panel">
						<h3 class="PanelTitle"><?php $count = count($praises); if ($count == 1) echo ("1 person praises this"); else echo ($count . " people praise this"); ?></h3>
						<div class="PanelContent">
							<div class="ButtonGroup ButtonGroupHorizontal">
							<?php
							foreach ($praises as $praise)
							{
								?>
								<a class="ButtonGroupButton" target="_blank" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $praise->ShortName)); ?>">
									<img class="ButtonGroupButtonImage" src="<?php echo(System::ExpandRelativePath("~/community/members/" . $praise->ShortName . "/images/avatar/thumbnail.png")); ?>" />
									<span class="ButtonGroupButtonText"><?php echo($praise->LongName); ?></span>
								</a>
								<?php
							}
							?>
							</div>
							<hr />
							<div style="text-align: center;">
								<a class="Button" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/shoutout/messages/" . $message->ID . "/praise.mmo")); ?>">Praise</a>
								<a class="Button" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/shoutout")); ?>">Cancel</a>
							</div>
						</div>
					</div>
					<?php
					return;
				}
			}
		}
	}
	
	if (isset($_POST["shoutout_message"]))
	{
		ShoutoutMessage::Create($CurrentUser, $thisuser, $_POST["shoutout_message"]);
	
		System::Redirect("~/community/members/" . $thisuser->ShortName . "/shoutout");
		return;
	}
	
	$CurrentUser = User::GetCurrent();
	if ($CurrentUser != null)
	{
		if ($CurrentUser->ID != $thisuser->ID)
		{
?>
<div class="Panel" style="display: none">
	<h3 class="PanelTitle">Introduce yourself</h3>
	<div class="PanelContent" style="text-align: center;">
		<span style="padding-right: 16px;"><?php echo(LanguageString::GetByName("introduce_prompt")); ?></span>
		
		<?php
			$lang = Language::GetCurrent();
			$query = "SELECT * FROM phpmmo_introduction_types WHERE introduction_type_language_id = " . $lang->ID;
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				echo("<a style=\"padding-right: 8px;\" href=\"" . System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/connect/" . $values["introduction_type_name"]) . "\">" . sprintf($values["introduction_type_title"], $CurrentUser->LongName) . "</a>");
				if ($i < $count - 1) echo("<span style=\"padding-right: 8px;\">&bullet;</span>");
			}
		?>
	</div>
</div>
<?php
		}
	}
?>
<div class="Panel">
	<h3 class="PanelTitle">Shoutout</h3>
	<div class="PanelContent">
		<?php $hcUserInfo = new Hovercard("hcUserInfo"); $hcUserInfo->Render(); ?>
		<script type="text/javascript">
			hcUserInfo.OnBeforeShow = function(sender, e)
			{
				$.ajax(
				{
					type: "POST",
					url: "<?php echo(System::ExpandRelativePath("~/ajax/hovercard.php")); ?>",
					data:
					{
						'type': 'member',
						'id': e.Data
					},
					dataType: "json",
					success: function(data)
					{
						if (data.result == "success")
						{
							sender.SetInnerHTML(data.content);
						}
						else
						{
							sender.SetInnerHTML("<div style=\"color: #FF0000; font-weight: bold;\">Could not load Hovercard</div>");
						}
					},
					error: function(data)
					{
						sender.SetInnerHTML("<div style=\"border: solid 1px #FF0000; padding: 16px; background-color: #330000; color: #FF0000; font-weight: bold;\">Could not load Hovercard</div>");
					}
				});
			};
		</script>
		<div class="Shoutout">
			<?php
			if ($CurrentUser != null)
			{
				if ($thisuser->ID == $CurrentUser->ID || $CurrentUser->HasFriend($thisuser))
				{
			?>
			<form name="frmShoutout" action="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/shoutout")); ?>" method="POST">
				<input type="hidden" name="attempt" value="1" />
				<input type="text" placeholder="<?php
				if ($thisuser->IsAuthenticated)
				{
					echo(LanguageString::GetByName("shoutout_publish_status"));
				}
				else
				{
					echo(sprintf(LanguageString::GetByName("shoutout_publish_shoutout"), $thisuser->LongName));
				}
				?>" name="shoutout_message" id="txtMessage" style="width: 100%;" />
			</form>
			<?php
				}
				else
				{
			?>
			<p><?php echo(LanguageString::GetByName("shoutout_error_not_friends")); ?></p>
			<?php
				}
			}
			
			$dlgPraises = new Window("dlgPraises", "Praises");
			$dlgPraises->Visible = false;
			$dlgPraises->BeginContent();
			?>
			<div class="ButtonGroup ButtonGroupHorizontal" id="pnlPraises">
			</div>
			<hr />
			<div style="text-align: center;">
				<a class="Button" id="cmdPraise" href="#">Praise</a>
				<a class="Button" href="#" onclick="dlgPraises.Close();">Close</a>
			</div>
			<?php
			$dlgPraises->EndContent();
			?>
			<script type="text/javascript">
			function DisplayPraiseDialog(messageID)
			{
				$.ajax(
				{
					type: "GET",
					url: "<?php echo(System::ExpandRelativePath("~/ajax/praise.php?action=list&message_id=")); ?>" + messageID,
					/*
					data:
					{
						'action': 'send',
						'message': message
					},
					*/
					dataType: "json",
					success: function(data)
					{
						if (data.result == "success")
						{
							var html = "";
							for (var i = 0; i < data.content.length; i++)
							{
								var member = data.content[i];
								html += "<a target=\"_blank\" href=\"<?php echo(System::ExpandRelativePath("~/community/members/")); ?>" + member.shortName + "\" class=\"ButtonGroupButton\">";
								html += "<img class=\"ButtonGroupButtonImage\" src=\"<?php echo(System::ExpandRelativePath("~/community/members/")); ?>" + member.shortName + "/images/avatar/thumbnail.png\" />";
								html += "<span class=\"ButtonGroupButtonText\">" + member.longName + "</span>";
								html += "</a>";
							}
							
							var pnlPraises = document.getElementById("pnlPraises");
							pnlPraises.innerHTML = html;
							
							var cmdPraise = document.getElementById("cmdPraise");
							cmdPraise.href = "<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/shoutout/messages/")); ?>" + messageID + "/praise.mmo";
							
							dlgPraises.ShowDialog();
						}
						else
						{
							// dlgError.ShowDialog();
						}
					}
				});
			}
			</script>

<?php
	$wndCommentPrivacy = new Window("wndCommentPrivacy", "Comment Privacy Settings");
	$wndCommentPrivacy->Visible = false;
	$wndCommentPrivacy->BeginContent();
?>
<table style="width: 100%;">
	<tr>
		<td><label for="cboCommentVisibility">Visibility:</label></td>
		<td>
			<select id="cboCommentVisibility" name="comment_visibility">
				<option <?php if ($message->Visibility == ShoutoutMessageVisibility::Hidden) { echo("selected=\"selected\" "); } ?>value="0">Hidden</option>
				<option <?php if ($message->Visibility == ShoutoutMessageVisibility::Friends) { echo("selected=\"selected\" "); } ?> value="1">Friends only</option>
				<option <?php if ($message->Visibility == ShoutoutMessageVisibility::Network) { echo("selected=\"selected\" "); } ?> value="2">Anyone who has a Psychatica account</option>
				<option <?php if ($message->Visibility == ShoutoutMessageVisibility::Everyone) { echo("selected=\"selected\" "); } ?> value="3">Everyone on the Internet</option>
				<option <?php if ($message->Visibility == ShoutoutMessageVisibility::Blacklist) { echo("selected=\"selected\" "); } ?> value="4">Everyone but the people I specify (blacklist)</option>
				<option <?php if ($message->Visibility == ShoutoutMessageVisibility::Whitelist) { echo("selected=\"selected\" "); } ?> value="5">Only certain people I specify (whitelist)</option>
			</select>
		</td>
	</tr>
	<tr id="trWhitelistOrBlacklist<?php echo($message->ID); ?>">
		<td colspan="2">
			Test...
		</td>
	</tr>
</table>
<?php
		$wndCommentPrivacy->EndContent();
?>
<script type="text/javascript">
	wndCommentPrivacy.Show = function(shoutoutMessageID)
	{
		var message = ShoutoutMessage.GetByID(shoutoutMessageID);
		var cboCommentVisibility = document.getElementById("cboCommentVisibility");
		cboCommentVisibility.selectedIndex = message.Visibility;
		
		wndCommentPrivacy.ShowDialog();
	};
</script>

			<div class="ShoutoutEntryCollection">
				<?php
				$messages = ShoutoutMessage::GetByUser($thisuser);
				$i = 0;
				foreach ($messages as $message)
				{
					RenderMessage($message, ($i % 2) != 0);
					$i++;
				}
				?>
			</div>
		</div>
	</div>
</div>