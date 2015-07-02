<?php
	namespace PhoenixSNS\Modules\Admin\MasterPages;
	
	use WebFX\Controls\Ribbon;
	use WebFX\Controls\RibbonButtonCommand;
	use WebFX\Controls\RibbonDropDownCommand;
	use WebFX\Controls\RibbonTab;
	use WebFX\Controls\RibbonTabGroup;
	use WebFX\Controls\RibbonCommandReferenceItem;
	use WebFX\Controls\RibbonSeparatorItem;
	
	use WebFX\Controls\Window;
	
	use WebFX\System;
	
	use PhoenixSNS\Controls\SearchTextBox;
	
	use PhoenixSNS\Objects\User;
	
	class WebPage extends \PhoenixSNS\MasterPages\WebPage
	{
		public $Ribbon;
		
		public function __construct()
		{
			parent::__construct();
			
			$CurrentUser = User::GetCurrent();
			$this->Styles = array
			(
				"margin-top" => "0px"
			);
			
			$this->Ribbon = new Ribbon("ribbon", "PhoenixSNS Manager");
			$this->Ribbon->UserName = $CurrentUser->LongName;
			$this->Ribbon->HelpButton->Visible = true;
			$this->Ribbon->ApplicationMenu->ToolTipTitle = "PhoenixSNS Manager";
			$this->Ribbon->ApplicationMenu->ToolTipText = "Access quick administrative functions from the application menu.";
			$this->Ribbon->ApplicationMenu->Items = array
			(
				new RibbonCommandReferenceItem("rcmdUsers")
			);
			
			$this->Ribbon->Commands = array
			(
				new RibbonButtonCommand("rcmdUserBrowse", "Browse Users", "~/admin/users", "alert('Find a way to get user list'); return false;", null, "Browse Users", "Search for users in the social network directory"),
				new RibbonButtonCommand("rcmdUserBan", "Ban a User", null, "BanUserDialog.ShowDialog();", null, "Ban Specified User", "Prevents the specified user from entering the Web site."),
				new RibbonButtonCommand("rcmdUserResourceTransfer", "Transfer", null, "ResourceTransferDialog.ShowDialog(); return false;", null, "Transfer Resources", "Gives a particular user (the receiver) a certain amount of resources, optionally revoking the same amount of resources from another user (the sender)."),
				
				new RibbonButtonCommand("rcmdWorldCreatePlace", "Place", null, "PlacePropertiesDialog.ShowDialog(); return false;", "~/images/Common/Icons/Ribbon/Place.png", "Create Place", "Creates a new Place in the World. Places can be private or public, and can be accessed via hotspots or triggers."),
				
				
				new RibbonButtonCommand("rcmdWorldCreateObjectNPC", "NPC", null, "ObjectPropertiesDialog.ShowDialog(); return false;", null, "NPC", "Creates a new NPC in the World. NPCs are Avatars that roam the World and can optionally trigger an action when clicked."),
				new RibbonButtonCommand("rcmdWorldCreateObjectHotspot", "Hotspot", null, "ObjectPropertiesDialog.ShowDialog(); return false;", null, "Hotspot", "Creates a new Hotspot in the World. Hotspots are visible objects that activate an action when the player clicks on them."),
				new RibbonButtonCommand("rcmdWorldCreateObjectTrigger", "Trigger", null, "ObjectPropertiesDialog.ShowDialog(); return false;", null, "Trigger", "Creates a new Trigger in the World. Triggers are invisible blocks that activate an action when the player character enters the covered area."),
				new RibbonDropDownCommand("rcmdWorldCreateObject", "Object", null, null, "~/images/Common/Icons/Ribbon/Objects.png", "Create Object", "Creates a new object in the current Place.", array
				(
					new RibbonCommandReferenceItem("rcmdWorldCreateObjectNPC"),
					new RibbonCommandReferenceItem("rcmdWorldCreateObjectHotspot"),
					new RibbonCommandReferenceItem("rcmdWorldCreateObjectTrigger")
				))
			);
			
			$this->Ribbon->Tabs = array
			(
				new RibbonTab
				(
					"tabHome",
					"Home",
					array
					(
					)
				),
				new RibbonTab
				(
					"tabUsers",
					"Users",
					array
					(
						new RibbonTabGroup
						(
							"rtgUsersManagement",
							"Management",
							array
							(
								new RibbonCommandReferenceItem("rcmdUserBrowse"),
								new RibbonCommandReferenceItem("rcmdUserBan")
							)
						),
						new RibbonTabGroup
						(
							"rtgUsersResources",
							"Resources",
							array
							(
								new RibbonCommandReferenceItem("rcmdUserResourceTransfer")
							)
						)
					)
				),
				new RibbonTab
				(
					"tabWorld",
					"World",
					array
					(
						new RibbonTabGroup
						(
							"rtgWorldCreate",
							"Create",
							array
							(
								new RibbonCommandReferenceItem("rcmdWorldCreatePlace"),
								new RibbonCommandReferenceItem("rcmdWorldCreateObject")
							)
						)
					)
				)
			);
			if ($this->Ribbon->SelectedTab !== null) $this->Ribbon->SelectedTab = $this->Ribbon->Tabs[0];
		}
		protected function BeforeContent()
		{
			$this->Ribbon->Render();
			
			$BanUserDialog = new Window("BanUserDialog", "Ban User");
			$BanUserDialog->Visible = false;
			$BanUserDialog->BeginContent();
			?>
			<table style="width: 100%;">
				<tr>
					<td style="width: 100px;"><label for="txtBanUserName">User to ban:</label></td>
					<td>
					<?php
						$txtBanUserName = new SearchTextBox("txtBanUserName");
						$txtBanUserName->SearchScopes[] = "Users";
						$txtBanUserName->Render();
					?>
					</td>
				</tr>
				<tr>
					<td><label for="txtBanReason">Reason:</label></td>
					<td>
						<textarea id="txtBanReason" rows="5" style="width: 512px;" placeholder="Describe the situation which led up to the ban"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="checkbox" id="chkBanReasonVisible" /> <label for="chkBanReasonVisible">Allow user to see ban reason</label></td>
				</tr>
			</table>
			<?php
			$BanUserDialog->BeginButtons();
			?>
			<a class="Button" href="#">Ban User</a>
			<a class="Button" href="#" onclick="BanUserDialog.Hide();">Cancel</a>
			<?php
			$BanUserDialog->EndButtons();
			$BanUserDialog->EndContent();
			
			
			$PlacePropertiesDialog = new Window("PlacePropertiesDialog", "Place Properties");
			$PlacePropertiesDialog->Visible = false;
			$PlacePropertiesDialog->BeginContent();
			?>
			<table style="width: 100%; font-family: 'Segoe UI'; font-size: 8pt;">
				<tr>
					<td style="width: 200px;"><label for="txtPlaceName">Place name:</label></td>
					<td><input type="text" id="txtPlaceName" name="place_name" /></td>
				</tr>
			</table>
			<?php
			$PlacePropertiesDialog->BeginButtons();
			?>
			<a id="PlacePropertiesDialog_cmdCreate" class="Button" href="#">Create</a>
			<a id="PlacePropertiesDialog_cmdCancel" class="Button" href="#">Cancel</a>
			<?php
			$PlacePropertiesDialog->EndButtons();
			$PlacePropertiesDialog->EndContent();
			?>
			<script type="text/javascript">
				PlacePropertiesDialog.Buttons =
				{
					"cmdCreate":
					{
						"NativeObject": document.getElementById("PlacePropertiesDialog_cmdCreate")
					},
					"cmdCancel":
					{
						"NativeObject": document.getElementById("PlacePropertiesDialog_cmdCancel")
					}
				};
				
				PlacePropertiesDialog.Buttons.cmdCreate.NativeObject.addEventListener("click", function(e)
				{
					alert("Creating a Place!");
				});
				PlacePropertiesDialog.Buttons.cmdCancel.NativeObject.addEventListener("click", function(e)
				{
					PlacePropertiesDialog.Hide();
				});
			</script>
			<?php
			
			$ResourceTransferDialog = new Window("ResourceTransferDialog", "Transfer Resources");
			$ResourceTransferDialog->Visible = false;
			$ResourceTransferDialog->BeginContent();
			?>
			<table style="width: 100%;">
				<tr>
					<td style="width: 200px;"><label for="txtResourceTransferSender">From user (or blank):</label></td>
					<td>
					<?php
						$txtResourceTransferSender = new SearchTextBox("txtResourceTransferSender");
						$txtResourceTransferSender->SearchScopes[] = "Users";
						$txtResourceTransferSender->Render();
					?>
					</td>
				</tr>
				<tr>
					<td><label for="txtResourceTransferSender">To user:</label></td>
					<td>
					<?php
						$txtResourceTransferReceiver = new SearchTextBox("txtResourceTransferReceiver");
						$txtResourceTransferReceiver->SearchScopes[] = "Users";
						$txtResourceTransferReceiver->Render();
					?>
					</td>
				</tr>
			</table>
			<?php
			$ResourceTransferDialog->BeginButtons();
			?>
			<a class="Button" href="#">Transfer</a>
			<a class="Button" href="#" onclick="ResourceTransferDialog.Hide();">Cancel</a>
			<?php
			$ResourceTransferDialog->EndButtons();
			$ResourceTransferDialog->EndContent();
		}
		protected function AfterContent()
		{
		}
	}
?>