<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\TabContainer;
	use WebFX\Controls\TabPage;
	
	use WebFX\ModulePage;
	
	use PhoenixSNS\TenantManager\MasterPages\NavigationButton;
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	
	use PhoenixSNS\Objects\DataCenter;
	
	class DataCenterWebPage extends WebPage
	{
		
	}
	class DataCenterMainPage extends DataCenterWebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "Data Center Management";
			
			$this->HeaderButtons[] = new NavigationButton("~/data-centers/modify", "Create Data Center", "fa-plus-circle", "return nav('/data-centers/modify');", "Default");
		}
		
		protected function RenderContent()
		{
			$datacenters = DataCenter::Get();
			
			$lv = new ListView();
			$lv->Columns = array
			(
				new ListViewColumn("chDataCenter", "Data Center"),
				new ListViewColumn("chDescription", "Description"),
				new ListViewColumn("chHostName", "Host name")
			);
			
			foreach ($datacenters as $datacenter)
			{
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("chDataCenter", "<a href=\"" . System::ExpandRelativePath("~/data-centers/modify/" . $datacenter->ID) . "\">" . $datacenter->Title . "</a>", $datacenter->Title),
					new ListViewItemColumn("chDescription", $datacenter->Description),
					new ListViewItemColumn("chHostName", "<a href=\"http://" . $datacenter->HostName . "\">" . $datacenter->HostName . "</a>")
				));
			}
			
			$lv->Render();
		}
	}
	class DataCenterManagementPage extends DataCenterWebPage
	{
		public $CurrentObject;
		
		protected function Initialize()
		{
			$this->Title = "Manage Data Center";
			$this->Subtitle = $this->CurrentObject->Title;
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
				<div class="FormView" style="width: 100%;">
					<div class="Field">
						<label for="txtTitle" style="width: 100px;">Title:</label>
						<input id="txtTitle" name="datacenter_Title" type="text" value="<?php echo($this->CurrentObject->Title); ?>" />
					</div>
					<div class="Field">
						<label for="txtDescription" style="vertical-align: top;">Description:</label>
						<textarea id="txtDescription" name="datacenter_Description" style="width: 100%;" rows="5"><?php echo($this->CurrentObject->Description); ?></textarea>
					</div>
					<div class="Field">
						<label for="txtHostName">Hostname:</label>
						<input id="txtHostName" name="datacenter_HostName" type="text" value="<?php echo($this->CurrentObject->HostName); ?>" />
					</div>
				</div>
				<?php
			});
			$tbs->SelectedTab = $tbs->TabPages[0];
			$tbs->Render();
			?>
				<div class="Buttons">
					<input type="submit" class="Button Default" value="Save Changes" />
					<a class="Button" href="<?php echo(System::ExpandRelativePath("~/data-centers")); ?>">Discard Changes</a>
				</div>
			</form>
			<?php
		}
	}
	
	System::$Modules[] = new \WebFX\Module("net.phoenixsns.TenantManager.DataCenter", array
	(
		new ModulePage("data-centers", array
		(
			new ModulePage("", function($page, $path)
			{
				$page = new DataCenterMainPage();
				$page->Render();
				return true;
			}),
			new ModulePage("create", function($page, $path)
			{
				$datacenter = new DataCenter();
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$datacenter->Title = $_POST["datacenter_Title"];
					$datacenter->Description = $_POST["datacenter_Description"];
					$datacenter->HostName = $_POST["datacenter_HostName"];
					$datacenter->Update();
					
					System::Redirect("~/data-centers");
				}
				else
				{
					$page = new DataCenterManagementPage();
					$page->CurrentObject = null;
					$page->Render();
				}
				return true;
			}),
			new ModulePage("modify", function($page, $path)
			{
				$datacenter = DataCenter::GetByID($path[0]);
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$datacenter->Title = $_POST["datacenter_Title"];
					$datacenter->Description = $_POST["datacenter_Description"];
					$datacenter->HostName = $_POST["datacenter_HostName"];
					$datacenter->Update();
					
					// if ($_GET["close"] == "1")
					// System::Redirect("~/data-centers/modify/" . $path[0]);
					System::Redirect("~/data-centers");
				}
				else
				{
					$page = new DataCenterManagementPage();
					$page->CurrentObject = $datacenter;
					$page->Render();
				}
				return true;
			})
		))
	));
?>