<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewColumnCheckBox;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\TabContainer;
	use WebFX\Controls\TabPage;
	
	use WebFX\Controls\Window;
	
	use WebFX\ModulePage;
	
	use PhoenixSNS\TenantManager\MasterPages\NavigationButton;
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	
	use PhoenixSNS\Objects\User;
	
	class UserWebPage extends WebPage
	{
		
	}
	class UserMainPage extends UserWebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "User Management";
			
			$this->HeaderButtons[] = new NavigationButton("~/users/modify", "Create User", "fa-plus-circle", "wndCreateUser.ShowDialog(); return false;", "Default");
		}
		
		protected function RenderContent()
		{
			$wndCreateUser = new Window();
			$wndCreateUser->ID = "wndCreateUser";
			$wndCreateUser->Title = "Create User";
			$wndCreateUser->BeginContent();
			?>
			<form method="POST" action="<?php echo(System::ExpandRelativePath("~/users/modify")); ?>" onsubmit="wndCreateUser.Hide();">
				<div class="FormView" style="width: 100%">
					<div class="Field">
						<label for="txtLoginID" style="width: 280px;">Login ID:</label>
						<input type="text" id="txtLoginID" name="user_LoginID" />
					</div>
					<div class="Field">
						<label for="txtDisplayName">Display name:</label>
						<input type="text" id="txtDisplayName" name="user_DisplayName" />
					</div>
					<div class="Field">
						<label for="chkAccountLocked">Lock account</label>
						<input type="checkbox" name="user_AccountLocked" id="chkAccountLocked" />
					</div>
					<div class="Field">
						<label for="chkForcePasswordChange">Require password reset at next logon</label>
						<input type="checkbox" name="user_ForcePasswordChange" id="chkForcePasswordChange" />
					</div>
				</div>
			<?php
			$wndCreateUser->BeginButtons();
			?>
			<input type="submit" class="Button Default" value="Save Changes" />
			<a class="Button" href="#" onclick="wndCreateUser.Hide(); return false;">Discard Changes</a>
			<?php
			$wndCreateUser->EndButtons();
			?>
			</form>
			<?php
			$wndCreateUser->EndContent();
		
			$users = User::Get();
			
			$lv = new ListView();
			$lv->Width = "100%";
			$lv->Columns = array
			(
				new ListViewColumn("lvcLoginID", "Login ID"),
				new ListViewColumn("lvcDisplayName", "Display Name"),
				new ListViewColumnCheckBox("lvcAccountLocked", "Account Locked"),
				new ListViewColumnCheckBox("lvcPasswordChangeRequired", "Password Change Required")
			);
			foreach ($users as $user)
			{
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcLoginID", "<a href=\"" . System::ExpandRelativePath("~/users/modify/" . $user->ID . "\">" . $user->UserName . "</a>", $user->UserName)),
					new ListViewItemColumn("lvcDisplayName", $user->DisplayName),
					new ListViewItemColumn("lvcAccountLocked", $user->AccountLocked),
					new ListViewItemColumn("lvcPasswordChangeRequired", $user->ForcePasswordChange)
				));
			}
			$lv->Render();
		}
	}
	class UserManagementPage extends UserWebPage
	{
		public $CurrentObject;
		
		protected function Initialize()
		{
			$this->Title = "Manage User";
			$this->Subtitle = ($this->CurrentObject->DisplayName != null ? $this->CurrentObject->DisplayName : $this->CurrentObject->UserName);
		}
		
		protected function RenderContent()
		{
			?>
			<form method="POST" id="frmMain">
			<?php
			$tbs = new TabContainer("tbs");
			$tbs->TabPages[] = new TabPage("tabInformation", "Information", null, null, null, function()
			{
				?>
				<div class="FormView">
					<div class="Field">
						<label for="txtLoginID">Login ID</label>
						<input id="txtLoginID" name="user_LoginID" type="text" value="<?php echo($this->CurrentObject->UserName); ?>" />
						<span class="HelpText">The name used to log this user into the system</span>
					</div>
					<div class="Field">
						<label for="txtDisplayName">Display name</label>
						<input id="txtDisplayName" name="user_DisplayName" type="text" value="<?php echo($this->CurrentObject->DisplayName); ?>" />
						<span class="HelpText">The friendly name for this user</span>
					</div>
					<div class="Field">
						<label for="chkAccountLocked">Lock account</label>
						<input id="chkAccountLocked" name="user_AccountLocked" type="checkbox"<?php echo($this->CurrentObject->AccountLocked ? " checked=\"checked\"" : ""); ?> />
						<span class="HelpText">Check if this user should be prohibited from logging in</span>
					</div>
					<div class="Field">
						<label for="chkForcePasswordChange">Require password reset on next logon</label>
						<input id="chkForcePasswordChange" name="user_ForcePasswordChange" type="checkbox"<?php echo($this->CurrentObject->ForcePasswordChange ? " checked=\"checked\"" : ""); ?> />
						<span class="HelpText">Check if this user must change their password the next time they log in</span>
					</div>
				</div>
				<?php
			});
			$tbs->TabPages[] = new TabPage("tabPrivileges", "Privileges", null, null, null, function()
			{
				$lv = new ListView();
				$lv->EnableRowCheckBoxes = true;
				$lv->Columns = array
				(
					new ListViewColumn("lvcPrivilege", "Privilege")
				);
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcPrivilege", "Administrator")
				));
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcPrivilege", "Moderator")
				));
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcPrivilege", "User")
				));
				$lv->Render();
			});
			$tbs->SelectedTab = $tbs->TabPages[0];
			$tbs->Render();
			?>
				<div class="Buttons">
					<input class="Button Default" type="submit" value="Save Changes" />
					<a class="Button" href="<?php echo(System::ExpandRelativePath("~/users")); ?>">Discard Changes</a>
				</div>
			</form>
			<?php
		}
	}
	
	System::$Modules[] = new \WebFX\Module("net.phoenixsns.TenantManager.User", array
	(
		new ModulePage("users", array
		(
			new ModulePage("", function($page, $path)
			{
				$page = new UserMainPage();
				$page->Render();
				return true;
			}),
			new ModulePage("create", function($page, $path)
			{
				$user = new User();
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$user->UserName = $_POST["user_LoginID"];
					$user->DisplayName = $_POST["user_DisplayName"];
					$user->Update();
					
					System::Redirect("~/users");
				}
				else
				{
					$page = new UserManagementPage();
					$page->CurrentObject = null;
					$page->Render();
				}
				return true;
			}),
			new ModulePage("modify", function($page, $path)
			{
				$user = User::GetByID($path[0]);
				if ($user == null)
				{
					$user = new User();
				}
				
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$user->UserName = $_POST["user_LoginID"];
					$user->DisplayName = $_POST["user_DisplayName"];
					
					$user->AccountLocked = ($_POST["user_AccountLocked"] != "" ? true : false);
					$user->ForcePasswordChange = ($_POST["user_ForcePasswordChange"] != "" ? true : false);
					$user->Update();
					
					System::Redirect("~/users");
				}
				else
				{
					$page = new UserManagementPage();
					$page->CurrentObject = $user;
					$page->Render();
				}
				return true;
			})
		))
	));
?>