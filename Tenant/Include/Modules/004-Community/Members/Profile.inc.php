<?php
	use WebFX\System;
	use WebFX\ModulePage;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;

	use WebFX\Controls\BreadcrumbItem;
	
	use WebFX\WebStyleSheet;
	use WebFX\WebResourceLink;
	
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantObject;
	use PhoenixSNS\Objects\TenantObjectProperty;
	use PhoenixSNS\Objects\TenantObjectInstance;
	use PhoenixSNS\Objects\TenantObjectInstanceProperty;
	use PhoenixSNS\Objects\TenantQueryParameter;
	
	use PhoenixSNS\Objects\LanguageString;
	
	use PhoenixSNS\MasterPages\WebPage;
	use PhoenixSNS\Pages\ErrorPage;

	$id = $path[1];
	
	$CurrentTenant = Tenant::GetCurrent();
	$objUser = $CurrentTenant->GetObject("User");
	$thisuser = $objUser->GetInstance(new TenantQueryParameter("URL", $id));
	
	function displayProfileTitle()
	{
		$path = System::GetVirtualPath();
		$id = $path[1];
		
		$CurrentTenant = Tenant::GetCurrent();
		$objUser = $CurrentTenant->GetObject("User");
		$thisuser = $objUser->GetInstance(new TenantQueryParameter("URL", $id));
		
		if ($thisuser == null) return;
		
?>
	<div class="ProfileTitle">
		<?php
		mmo_display_user_badges_by_user($thisuser);
		?>
		<span class="ProfileUserName"><?php echo($thisuser->LongName); ?></span>
		<?php
		if ($action != "customize")
		{
		?>
			<span class="ProfileControlBox">
			<?php
			if ($CurrentUser != null && ($thisuser->ID != $CurrentUser->ID))
			{
				if (mmo_has_user_friend_request(null, $thisuser))
				{
				?>
				<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($id); ?>/Disconnect">Withdraw Friend Request</a>
				<?php
				}
				else if (mmo_has_user_friend_request($thisuser, $CurrentUser))
				{
				?>
				<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($id); ?>/Connect/<?php echo(mmo_get_user_friendship_auth_key($thisuser, $CurrentUser)); ?>">Confirm Friendship</a>
				<?php
				}
				else if ($CurrentUser->HasFriend($thisuser))
				{
				?>
				<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($id); ?>/Disconnect"><?php echo(LanguageString::GetByName("friend_disconnect")); ?></a>
				<?php
				}
				else
				{
				?>
				<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($id); ?>/Connect"><?php echo(LanguageString::GetByName("friend_connect")); ?></a>
				<?php
				}
			?>
			<form action="<?php echo(System::$Configuration["Application.BasePath"]); ?>/Account/Messages/Create" method="POST">
				<input type="hidden" name="message_receiver" value="<?php echo($thisuser->ID); ?>" />
				<input class="LinkButton" type="submit" value="<?php echo(LanguageString::GetByName("message_send")); ?>" />
			</form>
			<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($id); ?>/trade/"><?php echo(LanguageString::GetByName("resource_trade")); ?></a>
			<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($id); ?>/block" onclick="/* ReportBlockDialog.Show(); return false; */">Report/Block</a>
			<?php
			}
			if ($thisuser->IsAuthenticated)
			{
			?>
				<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($id); ?>/Customize">Customize Profile</a>
			<?php
			}
		}
		?>
			</span>
	</div>
<?php
	}
	
					
	if (count($path) > 2 && $path[2] != "")
	{
		$action = $path[2];
	}
	else
	{
		$action = "profile";
	}
	
	if ($action == "customize" && !$thisuser->IsAuthenticated) $action = "profile";
	
	if ($action == "customize")
	{
		if ($_POST["pc"] != null)
		{
			mmo_user_update_profile_content($thisuser, $_POST["pc"]);
			System::Redirect("~/community/members/" . $thisuser->ShortName . "/information");
			return;
		}
	}


	if ($thisuser == null && $action != "images")
	{
		$errorPage = new ErrorPage();
		$errorPage->Title = "User Not Found";
		$errorPage->Message = "That user does not exist in the system. They may have deleted their account, or you may have typed their name incorrectly.";
		$errorPage->ReturnButtonURL = "~/community/members";
		$errorPage->ReturnButtonText = "Return to User List";
		
		$errorPage->Render();
		return;
	}
	
	if ($thisuser->ProfileVisibility != UserProfileVisibility::Everyone && $action != "images")
	{
		if (!$thisuser->IsVisible())
		{
			$errorPage = new ErrorPage();
			$errorPage->Title = "Private Profile";
			$errorPage->DisplayContactMessage = false;
			if ($thisuser->ProfileVisibility == UserProfileVisibility::Hidden)
			{
				$errorPage->Message = "This user's profile is private.";
			}
			else if ($thisuser->ProfileVisibility == UserProfileVisibility::Friends)
			{
				$errorPage->Message = "This user's profile is only visible to their friends. Please <a href=\"" . System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/connect") . "\">add this person as a friend</a> to see this person's profile.";
			}
			else if ($thisuser->ProfileVisibility == UserProfileVisibility::ExtendedFriends)
			{
				$errorPage->Message = "This user's profile is only visible to their friends, or friends of their friends. Please <a href=\"" . System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/connect") . "\">add this person as a friend</a> to see this person's profile.";
			}
			else if ($thisuser->ProfileVisibility == UserProfileVisibility::Sitewide)
			{
				$errorPage->Message = "This person's profile is not visible to people outside of " . System::GetConfigurationValue("Application.Name") . ". Please <a href=\"" . System::ExpandRelativePath("~/account/login.page") . "\">log in to " . System::GetConfigurationValue("Application.Name") . "</a> to see this person's profile.";
			}
			else
			{
				$errorPage->Message = "Unknown profile visibility " . $thisuser->ProfileVisibility;
			}
			$errorPage->ReturnButtonURL = "~/community/members";
			$errorPage->ReturnButtonText = "Return to User List";
			
			$errorPage->Render();
			return;
		}
	}
	
	switch ($path[2])
	{
		case "journals":
		{
			if ($path[4] == "entries.rss" || $path[4] == "entries.atom")
			{
				require("journal/detail.inc.php");
				return;
			}
			else if ($path[4] == "entries" && $path[6] == "comment")
			{
				$journal = Journal::GetByIDOrName($path[3]);
				if ($journal == null) return;
				
				$entry = JournalEntry::GetByIDOrName($path[5]);
				
				$entryUrl = System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries/" . $entry->Name);
				
				switch ($path[6])
				{
					case "comment":
					{
						if ($_SERVER["REQUEST_METHOD"] == "POST")
						{
							$reply_to = null;
							if ($_POST["reply_comment_id"] != null)
							{
								$reply_to = JournalEntryComment::GetByID($_POST["reply_comment_id"]);
							}
							
							$title = $_POST["comment_title"];
							$content = $_POST["comment_content"];
							
							if (!$entry->AddComment($title, $content, $reply_to))
							{
								$page = new ErrorPage();
								$page->ErrorCode = mysql_errno();
								$page->ErrorDescription = mysql_error();
								$page->Render();
								return;
							}
							
							System::Redirect($entryUrl);
							return;
						}
					}
				}
			}
			break;
		}
		case "rooms":
		{
			require("rooms/default.inc.php");
			return;
		}
	}
	
	switch ($action)
	{
		case "block":
		{
			require("Block.inc.php");
			return;
		}
		case "theme":
		{
			if (count($path) > 3)
			{
				switch($path[3])
				{
					case "stylesheet":
					{
						if (file_exists("style/themes/user/" . $thisuser->UserName . ".css"))
						{
							header("Content-Type: text/css");
							readfile("style/themes/user/" . $thisuser->UserName . ".css");
						}
						else
						{
							header("HTTP/1.1 404 File Not Found");
						}
					}
				}
			}
			return;
		}
		case "connect":
		{
			if (count($path) > 3 && $path[3] != "")
			{
				if (!mmo_accept_user_friend_request($thisuser, $CurrentUser, $path[3]))
				{
					$page = new ErrorPage("Friendship Authorization Key invalid");
					$page->Message = "The Friendship Authorization Key you provided was invalid.  If you copied the key from an e-mail, please make sure you typed it correctly.";
					$page->ReturnButtonURL = "~/community/members/" . $thisuser->ShortName;
					$page->ReturnButtonText = "Return to " . $thisuser->LongName . "'s Profile";
					$page->Render();
					return;
				}
				else
				{
					System::Redirect("~/community/members/" . $thisuser->ShortName);
					return;
				}
			}
			else
			{
				mmo_send_user_friend_request(null, $thisuser);
				System::Redirect("~/community/members/" . $thisuser->ShortName);
			}
			return;
		}
		case "disconnect":
		{
			if ($_POST["attempt"] == "1")
			{
				mmo_withdraw_user_friend_request(null, $thisuser);
				System::Redirect("~/community/members/" . $thisuser->ShortName);
				return;
			}
			else
			{
				$page = new WebPage(LanguageString::GetByName("friend_disconnect"));
				$page->BeginContent();
			?>
				<div class="Panel">
					<h3 class="PanelTitle"><?php echo(LanguageString::GetByName("confirm")); ?></h3>
					<div class="PanelContent">
						<p>Are you sure you want to cancel your friendship with <?php echo($thisuser->LongName); ?>? Please note that once your friendship is cancelled, <?php echo($thisuser->LongName); ?> will need to re-confirm you if you request friendship again.</p>
						<form action="disconnect" method="POST">
							<input type="hidden" name="attempt" value="1" />
							<table style="margin-left: auto; margin-right: auto;">
								<tr>
									<td colspan="2" style="text-align: right;">
										<input type="submit" value="<?php echo(LanguageString::GetByName("friend_disconnect")); ?>" />
										<a class="Button" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName)); ?>"><?php echo(LanguageString::GetByName("return_to_profile")); ?></a>
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			<?php
				$page->EndContent();
			}
			return;
		}
		case "trade":
		{
			require("trade.inc.php");
			return;
		}
		default:
		{
			if ($path[2] == "")
			{
				if (System::$Configuration["Shoutout.Enabled"])
				{
					System::Redirect("~/community/members/" . $path[1] . "/shoutout");
				}
				else
				{
					System::Redirect("~/community/members/" . $path[1] . "/information");
				}
				return;
			}
			
			$page = new WebPage(sprintf(LanguageString::GetByName("profile_title"), $thisuser->LongName));
			$page->BreadcrumbItems = array
			(
				new BreadcrumbItem("~/community", "Community"),
				new BreadcrumbItem("~/community/members", "Members"),
				new BreadcrumbItem("~/community/members/" . $thisuser->ShortName, $thisuser->LongName, true)
			);
			
			if (file_exists("style/themes/user/" . $thisuser->UserName . ".css"))
			{
				$page->StyleSheets[] = new WebStyleSheet("~/community/members/" . $thisuser->ShortName . "/theme/stylesheet");
			}
			
			if ($path[2] == "journals" && $path[3] != "")
			{
				$page->ResourceLinks[] = new WebResourceLink("alternate", "application/atom+xml", "~/community/members/" . $thisuser->ShortName . "/journals/" . $path[3] . "/entries.atom", "Atom feed");
			}
			
			$page->BeginContent();
?>
<div class="ProfilePage">
	<?php
		displayProfileTitle();
		
		$friends = $thisuser->GetFriends();
		$groups = Group::GetByUser($thisuser);
	?>
	<?php
	if ($action == "customize")
	{
		require("customize.inc.php");
	}
	else
	{
		if ($thisuser->Theme != null)
		{
			if ($thisuser->Theme->Creator != null)
			{
		?>
		<p>Theme by <a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->Theme->Creator->ShortName)); ?>"><?php echo($thisuser->Theme->Creator->LongName); ?></a></p>
		<?php
			}
		}
	?>
	<div class="Card">
		<div class="Title"><i class="fa fa-user"></i> <span class="Text"><?php echo($thisuser->LongName); ?></span></div>
		<div class="Content">
			<div class="ProfileAvatar">
				<div class="PhoenixVision" data-typename="avatar" data-member-id="<?php echo($thisuser->ID); ?>"></div>
			</div>
		</div>
	</div>
	<div class="CardSet" style="width: 50%;">
		<div class="Card">
			<div class="Title"><i class="fa fa-users"></i><span class="Text"><?php echo(LanguageString::GetByName("groups")); ?></span></div>
			<div class="Content">
				<div class="IconList">
				<?php
					$groups = Group::GetByUser($thisuser, 5);
					if (count($groups) > 0)
					{
						$btng = new ButtonGroup("btngGroups");
						$btng->ButtonSize = 64;
						foreach ($groups as $group)
						{
							$btng->Items[] = new ButtonGroupButton(null, $group->Title, null, "~/community/groups/" . $group->Name . "/images/preview.png", "~/community/groups/" . $group->Name, "GroupInformationDialog.Show(Group.GetByID(" . $group->ID . "));");
						}
						$btng->Render();
					}
					else
					{
						echo("<div style=\"text-align: center;\">This user is not a member of any groups.</div>");
					}
				?>
				</div>
			</div>
			<div class="Actions Horizontal">
			<?php
			if (count($groups) > 0)
			{
			?>
				<a href="#" title="See all this user's groups"><i class="fa fa-external-link"></i> <span class="Text">See all this user's groups</span></a>
			<?php
			}
			?>
				<a href="#" onclick="GroupInviteDialog.ShowDialog(null, [ User.GetByID(<?php echo($thisuser->ID); ?>) ]);" title="Invite this user to join a group"><i class="fa fa-comment"></i> <span class="Text">Invite this user to join a group</span></a>
			</div>
		</div>
	</div>
	<div class="CardSet" style="width: 50%;">
		<div class="Card">
			<div class="Title"><i class="fa fa-user"></i><span class="Text"><?php echo(LanguageString::GetByName("friends")); ?></span></div>
			<div class="Content">
				<div class="IconList">
				<?
					$friends = $thisuser->GetFriends(true, 5);
					if (count($friends) > 0)
					{
						$btng = new ButtonGroup("btngFriends");
						$btng->ButtonSize = 64;
						foreach ($friends as $friend)
						{
							$btng->Items[] = new ButtonGroupButton(null, $friend->User->LongName, null, "~/community/members/" . $friend->User->ShortName . "/images/preview.png", "~/community/members/" . $friend->User->ShortName, "UserInformationDialog.Show(User.GetByID(" . $friend->User->ID . "));");
						}
						$btng->Render();
					}
					else
					{
						echo("<div style=\"text-align: center;\">This user does not have any friends. <a href=\"#\">Introduce yourself</a>!</div>");
					}
				?>
				</div>
			</div>
		</div>
	</div>
	<?php
	}
	?>
</div>
<?php
			$page->EndContent();
			break;
		}
	}
?>