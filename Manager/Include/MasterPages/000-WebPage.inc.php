<?php
	namespace PhoenixSNS\TenantManager\MasterPages;
	
	use WebFX\WebStyleSheet;
	use WebFX\System;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\TextBox;
	
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantType;
	use PhoenixSNS\Objects\DataCenter;
	use PhoenixSNS\Objects\DataType;
	use PhoenixSNS\Objects\Organization;
	use PhoenixSNS\Objects\Module;
	use PhoenixSNS\Objects\PaymentPlan;
	use PhoenixSNS\Objects\User;
	
	class NavigationItem
	{
		
	}
	class NavigationSeparator extends NavigationItem
	{
		public $Title;
		
		public function __construct($title = null)
		{
			$this->Title = $title;
		}
	}
	class NavigationButton extends NavigationItem
	{
		public $TargetURL;
		public $Title;
		public $IconName;
		public $TargetScript;
		public $CssClass;
		public $AdditionalText;
		
		public $Items;
		public $Expanded;
		
		public function __construct($targetURL, $title, $iconName = "", $targetScript = "", $cssClass = "", $additionalText = null, $items = null)
		{
			$this->TargetURL = $targetURL;
			$this->Title = $title;
			$this->IconName = $iconName;
			$this->TargetScript = $targetScript;
			$this->CssClass = $cssClass;
			$this->AdditionalText = $additionalText;
			
			if (!is_array($items)) $items = array();
			$this->Items = $items;
			$this->Expanded = false;
		}
	}
	
	class WebPage extends \WebFX\WebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->StyleSheets[] = new WebStyleSheet("~/StyleSheets/Main.css");
			$this->StyleSheets[] = new WebStyleSheet("http://static.alcehosting.net/dropins/WebFramework/StyleSheets/Professional/Main.css");
			
			$tenants = Tenant::Get();
			$tenantsNavigationButtons = array();
			foreach ($tenants as $tenant)
			{
				$tenantsNavigationButtons[] = new NavigationButton("~/tenant/modify/" . $tenant->URL, $tenant->URL, null, null, null);
			}
			
			$this->HeaderButtons = array();
			$this->SidebarButtons = array
			(
				new NavigationSeparator("Quick Start"),
				new NavigationButton("~/", "Dashboard", "dashboard"),
				new NavigationSeparator("Management"),
				new NavigationButton("~/tenant", "Tenants", "th-list", null, null, count($tenants), $tenantsNavigationButtons),
				new NavigationButton("~/modules", "Modules", "puzzle-piece", null, null, Module::Count()),
				new NavigationButton("~/data-centers", "Data Centers", "building-o", null, null, DataCenter::Count()),
				new NavigationButton("~/payment-plans", "Payment Plans", "money", null, null, PaymentPlan::Count()),
				new NavigationButton("~/organizations", "Organizations", "suitcase", null, null, Organization::Count()),
				new NavigationButton("~/users", "Users", "users", null, null, User::Count()),
				new NavigationButton("~/data-types", "Data Types", "sitemap", null, null, DataType::Count()),
				new NavigationButton("~/tenant-types", "Tenant Types", "tenant-types", null, null, TenantType::Count()),
				new NavigationSeparator("Help and Support"),
				new NavigationButton("~/support/documentation", "Documentation", "book"),
				new NavigationButton("~/support/bugspray", "Report a Bug", "bug"),
				new NavigationButton("~/system-log", "System Log", "file-text-o")
			);
			
			$this->RenderHeader = true;
			$this->RenderSidebar = true;
		}
		
		public $SidebarButtons;
		public $HeaderButtons;
		
		public $Subtitle;
		
		public $RenderHeader;
		public $RenderSidebar;
		
		private function RenderSidebarButton($button)
		{
			if (get_class($button) == "PhoenixSNS\\TenantManager\\MasterPages\\NavigationButton")
			{
				echo("<li class=\"Button");
				if ($button->Expanded) echo(" Expanded");
				echo("\">");
				?>
					<a href="<?php echo(System::ExpandRelativePath($button->TargetURL)); ?>">
						<span class="Icon"><i class="fa fa-<?php echo($button->IconName); ?>">&nbsp;</i></span>
						<span class="Text"><?php echo($button->Title); ?></span><?php
						if ($button->AdditionalText != null)
						{
							?><span class="Info"><?php echo($button->AdditionalText); ?></span><?php
						}
					?>
					</a>
				<?php
				if (count($button->Items) > 0)
				{
					echo("<ul>");
					foreach ($button->Items as $item)
					{
						$this->RenderSidebarButton($item);
					}
					echo("</ul>");
				}
				echo("</li>");
			}
			else if (get_class($button) == "PhoenixSNS\\TenantManager\\MasterPages\\NavigationSeparator")
			{
				echo("<li class=\"Separator\">");
				echo($button->Title);
				echo("</li>");
			}
		}
		
		protected function BeforeFullContent()
		{
			?>
			<div class="Page<?php
				if (!$this->RenderHeader) echo(" HideHeader");
				if (!$this->RenderSidebar) echo(" HideSidebar");
			?>">
				<nav class="Top">
					<a class="MenuButton" onclick="toggleSidebarExpanded(); return false;" href="#"><i class="fa fa-bars">&nbsp;</i></a>
					<img class="Logo" src="<?php echo(System::ExpandRelativePath("~/Images/Logo.png")); ?>" alt="<?php echo(System::GetConfigurationValue("Application.Name")); ?>" />
					<?php
						$txtSearch = new TextBox();
						$txtSearch->ClassList[] = "SearchBar";
						$txtSearch->PlaceholderText = "Type to search for tasks";
						$txtSearch->SuggestionURL = "~/API/Search.php?q=%1";
						$txtSearch->Render();
					?>
					<div class="UserInfo">
						<div class="DropDownButton">
							<i class="fa fa-user">&nbsp;</i>
							<img class="UserIcon" alt="" />
							<span class="UserName"><?php
								$user = User::GetByID($_SESSION["Authentication.UserID"]);
								if ($user == null)
								{
									echo("Not logged in");
								}
								else
								{
									if ($user->DisplayName != null)
									{
										echo($user->DisplayName);
									}
									else
									{
										echo($user->UserName);
									}
								}
							?></span>
							<div class="Menu DropDownMenu">
								<a href="<?php echo(System::ExpandRelativePath("~/account/settings.page")); ?>">
									<span class="Icon"><i class="fa fa-cogs">&nbsp;</i></span>
									<span class="Text">Change Settings</span>
								</a>
								<a href="<?php echo(System::ExpandRelativePath("~/account/logout.page")); ?>">
									<span class="Icon"><i class="fa fa-sign-out">&nbsp;</i></span>
									<span class="Text">Log Out</span>
								</a>
							</div>
						</div>
					</div>
				</nav>
				<nav class="Sidebar" id="__SidebarFrame">
					<ul>
					<?php
					foreach ($this->SidebarButtons as $button)
					{
						$this->RenderSidebarButton($button);
					}
					?>
					</ul>
					<div class="BackstageView">
						<div class="Content">
							<div class="Column" style="width: 25%;">
								<div class="Title">Tenants</div>
								<?php
									$tenants = Tenant::Get();
									foreach ($tenants as $tenant)
									{
										echo("<a href=\"" . System::ExpandRelativePath("~/tenant/modify/" . $tenant->URL) . "\">" . $tenant->URL . "</a>");
									}
								?>
								
								<div class="Title">Actions</div>
								<a href="<?php echo(System::ExpandRelativePath("~/account/logout.page")); ?>"><i class="fa fa-sign-out"></i> <span class="Text">Log Out</span></a>
							</div>
							<div class="Column">
								<div class="Title">About</div>
								<div><img src="<?php echo(System::ExpandRelativePath("~/Images/Billboard.png")); ?>" /></div>
								<p>
									PhoenixSNS version 1.0
								</p>
							</div>
						</div>
					</div>
				</nav>
				<header>
					<div class="Title" id="__TitleFrame"><?php echo($this->Title); ?></div>
					<div class="Subtitle" id="__SubtitleFrame"><?php echo($this->Subtitle); ?></div>
					<div class="Buttons">
					<?php
						foreach ($this->HeaderButtons as $button)
						{
							echo("<a class=\"Button");
							if ($button->CssClass != "")
							{
								echo(" " . $button->CssClass);
							}
							echo("\"");
							if ($button->TargetURL != "")
							{
								echo(" href=\"" . System::ExpandRelativePath($button->TargetURL) . "\"");
							}
							if ($button->TargetScript != "")
							{
								echo(" onclick=\"" . $button->TargetScript . "\"");
							}
							echo(">");
							if ($button->IconName != "")
							{
								echo("<i class=\"fa " . $button->IconName . "\">&nbsp;</i>");
							}
							echo("<span class=\"Text\">");
							echo($button->Title);
							echo("</span>");
							echo("</a>");
						}
					?>
					</div>
				</header>
				<div class="Content" id="__ContentFrame">
					<script type="text/javascript">
						function nav(url)
						{
							// disable AJAX navigation temporarily until it's figured out
							return true;
							
							// Add an item to the history log
							history.pushState(url, "", url);
							loadc(url);
							setSidebarExpanded(false);
							return false;
						}
						function toggleSidebarExpanded()
						{
							setSidebarExpanded(!getSidebarExpanded());
						}
						function getSidebarExpanded()
						{
							var u = document.getElementById("__SidebarFrame");
							return (u.className == "Sidebar Expanded");
						}
						function setSidebarExpanded(value)
						{
							var u = document.getElementById("__SidebarFrame");
							if (value)
							{
								u.className = "Sidebar Expanded";
							}
							else
							{
								u.className = "Sidebar";
							}
						}
						function loadc(url)
						{
							if (url == null) url = "";
							
							var contentFrame = document.getElementById("__ContentFrame");
							if (url.indexOf('?') != -1)
							{
								url += "&partial";
							}
							else
							{
								url += "?partial";
							}
							WebFramework.Navigation.LoadPartialContent(url, contentFrame);
							
							var event = new Event("load");
							window.dispatchEvent(event);
						}
						function setTitles(title, subtitle)
						{
							if (title)
							{
								var titleFrame = document.getElementById("__TitleFrame");
								titleFrame.innerHTML = title;
							}
							if (subtitle)
							{
								var subtitleFrame = document.getElementById("__SubtitleFrame");
								subtitleFrame.innerHTML = subtitle;
							}
						}
						
						// Revert to a previously saved state
						window.addEventListener('popstate', function(event)
						{
							loadc(event.state);
						});
						window.addEventListener("load", function(e)
						{
							var url = window.location.pathname.substring(1);
							var sidebar = document.getElementById("__SidebarFrame");
							var navs = sidebar.childNodes;
							for (var i = 0; i < navs.length; i++)
							{
								if (navs[i].tagName != "A") continue;
								if (navs[i].attributes["data-id"] != null && navs[i].attributes["data-id"].value == url)
								{
									navs[i].className = "Selected";
								}
								else
								{
									navs[i].className = "";
								}
							}
						});
					</script>
			<?php
		}
		protected function AfterFullContent()
		{
			?>
				</div>
				<footer>
					Copyright &copy; 2014 PhoenixSNS Development Group
				</footer>
			</div>
			<?php
		}
	}
?>