<?php
	namespace PhoenixSNS\MasterPages;
	
	use WebFX\System;
	
	use WebFX\WebPageMetadata;
	use WebFX\WebStyleSheet;
	use WebFX\WebScript;
	
	use WebFX\Controls\BreadcrumbItem;
	use WebFX\Controls\FlyoutTabStrip;
	use WebFX\Controls\FlyoutTabStripItem;
	use WebFX\Controls\Menu;
	use WebFX\Controls\MenuItemCommand;
	use WebFX\Controls\MenuItemSeparator;
	use WebFX\Controls\Panel;
	use WebFX\Controls\TextBox;
	
	use PhoenixSNS\Controls\ChatBar;
	use PhoenixSNS\Controls\ChatBarBuddy;
	use PhoenixSNS\Controls\ChatPanel;
	use PhoenixSNS\Controls\ChatPanelMessage;
	
	use PhoenixSNS\Objects\Module;
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\User;
	
	use PhoenixSNS\Objects\MarketResource;
	
	class WebPageBase extends \WebFX\WebPage
	{
		public $DisplayChrome;
		public function __construct()
		{
			parent::__construct();
			$this->DisplayChrome = true;
		}
		protected function Initialize()
		{
			parent::Initialize();
			
			$DefaultApplicationName = "PhoenixSNS";
			$DefaultOrganizationName = "PhoenixSNS Developers";
			$DefaultDescription = "A free-software (GPLv3), community-developed virtual world social network";
			
			$this->Metadata = array
			(
				// for Psychatica: "Psychatic Entertainment Group"
				new WebPageMetadata("author", System::GetConfigurationValue("Organization.Name", $DefaultOrganizationName)),
				
				// for Psychatica: "Create and explore this brand-new, community-developed, free-of-charge virtual world - no Flash required!"
				new WebPageMetadata("description", System::GetConfigurationValue("Application.Description", $DefaultDescription)),
				
				// for Psychatica: "Psychatica, virtual world, social network"
				new WebPageMetadata("keywords", System::GetConfigurationValue("Application.Keywords"))
			);
			
			$this->OpenGraph = new \WebFX\WebOpenGraphSettings();
			$this->OpenGraph->Enabled = true;
			$this->OpenGraph->Title = "Log in to " . System::GetConfigurationValue("Application.Name", $DefaultApplicationName);
			$this->OpenGraph->URL = System::ExpandRelativePath("~/");
			$this->OpenGraph->ImageURL = System::ExpandRelativePath("~/images/Logo.png");
			$this->OpenGraph->Description = System::GetConfigurationValue("Application.Description", $DefaultDescription);
			
			$compile = true;
			if (isset($_GET["compile"]))
			{
				if ($_GET["compile"] == "false")
				{
					$compile = false;
				}
			}
			
			if ($compile)
			{
				$this->StyleSheets = array(new WebStyleSheet("~/StyleSheet.css"));
			}
			else
			{
				$this->StyleSheets = array(new WebStyleSheet("~/StyleSheet.css?compile=false"));
			}
			
			$CurrentTenant = Tenant::GetCurrent();
			if ($CurrentTenant != null)
			{
				$objUser = $CurrentTenant->GetObject("User");
				
				$CurrentUser = $objUser->GetMethod("GetCurrentUser")->Execute();
				if ($CurrentUser != null)
				{
					$cutheme = $CurrentUser->GetPropertyValue("Theme");
					if ($cutheme != null) $this->StyleSheets[] = new WebStyleSheet("~/Themes/" . $cutheme->GetPropertyValue("Name") . "/StyleSheets/Main.css");
				}
			}
			
			$this->ShortcutIconURL = "~/images/favicon.ico";
			$this->Scripts[] = new WebScript(System::GetConfigurationValue("WebFramework.StaticPath", "~/") . "/dropins/JQuery/Scripts/jquery.min.js");
			$this->Scripts[] = new WebScript(System::GetConfigurationValue("WebFramework.StaticPath", "~/") . "/dropins/Strophe/Scripts/strophe.js");
			
			$this->Scripts[] = new WebScript("~/Resources/Common/Scripts/System.js.php");
			
			if ($compile)
			{
				$this->Scripts[] = new WebScript("~/Script.js");
			}
			else
			{
				$this->Scripts[] = new WebScript("~/Script.js?compile=false");
			}
			
			$this->Scripts[] = new WebScript("~/Resources/Common/Scripts/Controls/ChatBar.js.php");
			
			global $RootPath;
			$filenames = glob($RootPath . "/Objects/*.js");
			foreach ($filenames as $filename)
			{
				$this->Scripts[] = new WebScript("~/Resources/Common/Scripts/Objects/" . basename($filename));
			}
			$filenames = glob($RootPath . "/Objects/*.js.php");
			foreach ($filenames as $filename)
			{
				$this->Scripts[] = new WebScript("~/Resources/Common/Scripts/Objects/" . basename($filename));
			}
			$filenames = glob($RootPath . "/Controls/*.js");
			foreach ($filenames as $filename)
			{
				$this->Scripts[] = new WebScript("~/Resources/Common/Scripts/Controls/" . basename($filename));
			}
			$filenames = glob($RootPath . "/Controls/*.js.php");
			foreach ($filenames as $filename)
			{
				$this->Scripts[] = new WebScript("~/Resources/Common/Scripts/Controls/" . basename($filename));
			}
			
			if ($this->Title == null)
			{
				$this->Title = System::GetConfigurationValue("Application.Name");
			}
			else
			{
				$this->Title .= " | " . System::GetConfigurationValue("Application.Name");
			}
		}
	}
	
	class WebPage extends WebPageBase
	{
		protected function BeforeContent()
		{
			$CurrentTenant = Tenant::GetCurrent();
			if ($CurrentTenant != null)
			{
				$objUser = $CurrentTenant->GetObject("User");
				$CurrentUser = $objUser->GetMethod("GetCurrentUser")->Execute();
			}
			
			if ($CurrentUser != null)
			{
?>
<script type="text/javascript">
	// configure the XMPP client for the currently logged-in user
	var xmpp = new XMPPClient("<?php echo($CurrentUser->GetPropertyValue("URL")); ?>", "<?php echo($_SESSION["CurrentPassword"]); ?>");
</script>
<?php
			}
			
			if ($this->DisplayChrome)
			{
				$mnuApplication = new Menu("mnuApplication");
				$mnuApplication->Top = "48px";
				
				$LoadedModules = Module::Get();
				foreach ($LoadedModules as $module)
				{
					$menuitems = $module->GetMainMenuItems();
					foreach ($menuitems as $menuitem)
					{
						$mnuApplication->Items[] = new MenuItemCommand($menuitem->Title, $menuitem->TargetURL, $menuitem->TargetScript, $menuitem->Description);
					}
				}
				$mnuApplication->Render();
				
?>
	<div class="Toolbar ApplicationToolbar">
		<a href="<?php echo(System::ExpandRelativePath("~/")); ?>" class="ApplicationButton" onclick="mnuApplication.Show(); return false;"><i class="fa fa-bars"></i></a>
		<span class="ApplicationTitle"><?php echo(System::GetConfigurationValue("Application.Name")); ?></span>
		<span class="SearchBar">
			<form name="frmSearch" id="frmSearch" method="GET" action="search">
				<?php
					$typeahead = new TextBox("txtSearch", "search_bar");
					$typeahead->CssClass = "SearchTextBox";
					$typeahead->SuggestionURL = System::ExpandRelativePath("~/API/Search.php?format=json&query=%1");
					$typeahead->PlaceholderText = "Type to find something";
					$typeahead->Width = "500px";
					$typeahead->Render();
				?>
				<script type="text/javascript">
					txtSearch.FormatStart = function()
					{
						var html = "";
						// html += "<img src=\"/images/logowntr.png\" style=\"width: 320px; display: block;\" />";
						html += "<div class=\"Menu\" style=\"max-height: 300px;\">";
						return html;
					};
					
					var lastItemCategory = "";
					txtSearch.FormatItem = function(item)
					{
						var html = "";
						if (lastItemCategory != item.Category)
						{
							html += "<h2>" + item.Category + "</h2>";
						}
						lastItemCategory = item.Category;
						
						switch (item.Category)
						{
							case "Tasks":
							{
								html += "<a class=\"MenuItem\" href=\"" + System.ExpandRelativePath(item.Item.NavigateURL) + "\">" + item.Item.Title + "</a>";
								break;
							}
							case "Administrative Tasks":
							{
								html += "<a class=\"MenuItem\" href=\"" + System.ExpandRelativePath(item.Item.NavigateURL) + "\">" + item.Item.Title + "</a>";
								break;
							}
							case "Members":
							{
								html += "<a class=\"MenuItem\" href=\"<?php echo(System::GetConfigurationValue("Application.BasePath")); ?>/community/members/" + item.Item.ShortName + "\"><img src=\"<?php echo(System::GetConfigurationValue("Application.BasePath")); ?>/community/members/" + item.Item.ShortName + "/images/avatar/thumbnail.png\" style=\"width: 24px;\" /> " + item.Item.LongName + "</a>";
								break;
							}
							case "Groups":
							{
								html += "<a class=\"MenuItem\" href=\"<?php echo(System::GetConfigurationValue("Application.BasePath")); ?>/community/groups/" + item.Item.Name + "\"><img src=\"<?php echo(System::GetConfigurationValue("Application.BasePath")); ?>/community/groups/" + item.Item.Name + "/images/avatar/thumbnail.png\" style=\"width: 24px;\" /> " + item.Item.Title + "</a>";
								break;
							}
							case "Pages":
							{
								html += "<a class=\"MenuItem\" href=\"<?php echo(System::GetConfigurationValue("Application.BasePath")); ?>/community/pages/" + item.Item.Name + "\"><img src=\"<?php echo(System::GetConfigurationValue("Application.BasePath")); ?>/community/groups/" + item.Item.Name + "/images/avatar/thumbnail.png\" style=\"width: 24px;\" /> " + item.Item.Title + "</a>";
								break;
							}
							case "Places":
							{
								html += "<a class=\"MenuItem\" href=\"<?php echo(System::GetConfigurationValue("Application.BasePath")); ?>/world/" + item.Item.Name + "\">" + item.Item.Title + "</a>";
								break;
							}
						}
						return html;
					};
					txtSearch.FormatEnd = function()
					{
						lastItemCategory = "";
						return "</div>";
					};
				</script>
				<input type="submit" value="Search" style="display: none;" />
			</form>
		</span>
		
		<?php
		if ($CurrentUser != null)
		{
		?>
		<span class="TrayCollection" style="position: absolute; right: 128px;">
			<a id="TrayContainer_t1_trayFriendRequests_Button" title="Friend Requests" alt="Friend Requests" class="TrayButton" href="<?php echo(System::ExpandRelativePath("~/account/requests")); ?>" onclick="switchtray('trayFriendRequests'); return false;"><i class="fa fa-users"></i></a>
			<span class="TrayPopup" id="TrayContainer_t1_trayFriendRequests_Tray" style="left: -192px; width: 320px;">
				You have no new friend requests.
			</span>
			<a title="Messages" alt="Messages" class="TrayButton" href="<?php echo(System::ExpandRelativePath("~/account/messages")); ?>" id="TrayContainer_t1_trayMessages_Button" onclick="switchtray('trayMessages'); return false;"><i class="fa fa-envelope"></i></a>
			<span class="TrayPopup" id="TrayContainer_t1_trayMessages_Tray" style="left: -160px; width: 320px;">
				testing messages...
			</span>
			<a id="TrayContainer_t1_trayNotifications_Button" title="Notifications" alt="Notifications" class="TrayButton" href="<?php echo(System::ExpandRelativePath("~/account/notifications")); ?>" onclick="switchtray('trayNotifications'); return false;"><i class="fa fa-warning"></i></a>
			<span class="TrayPopup" id="TrayContainer_t1_trayNotifications_Tray" style="left: -128px; width: 320px;">
				You have no new notifications.
			</span>
		</span>
		<script type="text/javascript">
			function cleartrays()
			{
				var w = document.getElementsByClassName("TrayButton Selected");
				for (var i = 0; i < w.length; i++) w[i].className = "TrayButton";
				w = document.getElementsByClassName("TrayPopup");
				for (var i = 0; i < w.length; i++) w[i].style.display = "none";
			}
			function switchtray(trayid)
			{
				cleartrays();
				
				var d = document.getElementById("TrayContainer_t1_" + trayid + "_Tray");
				d.style.display="block";
				
				d = document.getElementById("TrayContainer_t1_" + trayid + "_Button");
				d.className = "TrayButton Selected";
			}
			window.addEventListener("mousedown", cleartrays);
		</script>
		<a class="UserLink" href="#"><img src="<?php echo(System::ExpandRelativePath("~/community/members/" . $CurrentUser->GetPropertyValue("URL") . "/images/avatar/thumbnail.png")); ?>" /> <?php echo($CurrentUser->GetPropertyValue("DisplayName")); ?></a>
		<?php
		}
		else
		{
		?>
		<form style="display: inline; position: absolute; right: 0px; top: 4px;" class="ToolbarLoginForm" name="frmLogin" id="frmLogin" action="<?php echo(System::ExpandRelativePath("~/account/login.page")); ?>" method="POST">
		<input type="text" name="member_username" placeholder="Login ID" />
		<input type="password" name="member_password" placeholder="Password" />
		<input type="submit" value="Log In" />
		</form>
		<?php
		}
		?>
	</div>
<?php
		}
?>
	<div class="CardLayout">
		<div class="CardSet">
<?php
		}
		protected function AfterContent()
		{
			$CurrentTenant = Tenant::GetCurrent();
			if ($CurrentTenant != null)
			{
				$objUser = $CurrentTenant->GetObject("User");
				$CurrentUser = $objUser->GetMethod("GetCurrentUser")->Execute();
			}
?>
		</div>
		<?php
		if ($CurrentUser != null)
		{
			/*
			// Here we have the notifications panel...
		?>
		<div class="CardSet Right">
		<?php
			if ($CurrentUser->CountInventoryItems() == 0)
			{
				// we do not have any inventory items, so display a notification to go to the market
?>
			<div class="Card">
				<div class="Title">
					<i class="fa fa-warning"></i> <span class="Text">Welcome, newcomer! Now put on some clothes...</span>
				</div>
				<div class="Content">
					You cannot enter the World until you have equipped a Starter Pack. Visit the Market to obtain your
					first set of clothes FREE!
				</div>
				<div class="Actions Vertical">
					<a href="<?php echo(System::ExpandRelativePath("~/market/starter")); ?>">Get your Starter Pack now!</a>
				</div>
			</div>
<?php
			}
		?>
			<div class="Card">
				<div class="Title">
					<a href="#">jeremie</a> wants to send you <span class="Emphasis">5600</span> credits!
				</div>
				<div class="Buttons">
					<a class="Accept" href="#">Accept</a>
					<a class="Decline" href="#">Decline</a>
				</div>
			</div>
			<?php
				$hc = new \PhoenixSNS\Controls\Hovercard("hc1");
				$hc->RequestMethod = "POST";
				$hc->ContentURL = "~/API/Hovercard/ItemInfo.php";
				$hc->Render();
			?>
			<div class="Card">
				<div class="Title">
					<a href="#">Avondale~Arcadia</a> wants to trade her <a onmouseover="hc1.Show({'ID': 1});" onmouseout="hc1.Hide();" class="Emphasis" href="#">Laced Dress Black</a> for your <a class="Emphasis" href="#" onmouseover="hc1.Show({'ID': 2});" onmouseout="hc1.Hide();">Short Haircut White</a>
				</div>
				<div class="Buttons">
					<a class="Accept" href="#">Accept</a>
					<a class="Decline" href="#">Decline</a>
					<a href="#">View Item</a>
				</div>
			</div>
			<div class="Card">
				<div class="Title">
					<a href="#">TEST ACCOUNT</a> sent you a message
				</div>
				<div class="Content">
					hey just checking to see the status of the new UI system?
				</div>
				<div class="Buttons">
					<a class="Accept" href="#">Respond</a>
					<a class="Decline" href="#">Delete</a>
				</div>
			</div>
		</div>
		<?php
			*/
		}
		?>
	</div>
<?php
		if ($this->DisplayChrome)
		{
			if ($CurrentUser != null)
			{
				$friends = $CurrentUser->GetFriends(false);
				$chatpanel = new ChatBar("caMain", $CurrentUser->GetPropertyValue("URL"));
				foreach ($friends as $friend)
				{
					$chatpanel->Buddies[] = new ChatBarBuddy($friend->User->ShortName, $friend->User->LongName, "~/community/members/" . $friend->User->ShortName . "/images/avatar/thumbnail.png");
				}
				/*
				foreach ($friends as $friend)
				{
					$panel = new ChatPanel($friend->User->ShortName, $friend->User->LongName, "~/community/members/" . $friend->User->ShortName . "/images/avatar/thumbnail.png");
					$panel->PopoutURL = "~/community/members/" . $friend->User->ShortName;
					$chatpanel->Panels[] = $panel;
				}
				*/
				$chatpanel->Render();
?>
<script type="text/javascript">
	xmpp.UserDisconnected.Add(function(sender, e)
	{
		caMain.UpdatePresence(caMain.GetChatPanelIDForUserName(e.User.UserName), PresenceType.Offline);
	});
	xmpp.MessageReceived.Add(function(sender, e)
	{
		caMain.ReceiveMessage(caMain.GetChatPanelIDForUserName(e.User.UserName), e.Content);
	});
	xmpp.PresenceUpdated.Add(function(sender, e)
	{
		switch (e.PresenceType)
		{
			case PresenceType.Active:
			{
				break;
			}
			case PresenceType.Composing:
			{
				// Create a new chat panel for this user
				caMain.CreateChatPanelForUserName(e.User.UserName);
				break;
			}
			case PresenceType.Paused:
			{
				break;
			}
		}
		caMain.UpdatePresence(caMain.GetChatPanelIDForUserName(e.User.UserName), e.PresenceType);
	});
</script>
<?php
				}
			}
		}
	}
?>