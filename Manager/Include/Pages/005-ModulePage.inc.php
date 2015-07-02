<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	use WebFX\ModulePage;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\TabContainer;
	use WebFX\Controls\TabPage;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	use PhoenixSNS\TenantManager\MasterPages\NavigationButton;
	
	use PhoenixSNS\Objects\Module;
	
	class ModuleMainPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->HeaderButtons[] = new NavigationButton("~/modules/modify", "Create Module", "fa-plus-circle", "// return nav('/modules/modify');", "Default");
		}
		
		protected function RenderContent()
		{
			$modules = Module::Get();
		?>
		<p>There are <?php echo(count($modules)); ?> modules in total.  Click a module name to configure that module.</p>
		<?php
			$lv = new ListView();
			$lv->Columns = array
			(
				new ListViewColumn("chTitle", "Module"),
				new ListViewColumn("chDescription", "Description")
			);
			foreach ($modules as $module)
			{
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("chTitle", "<a href=\"" . System::ExpandRelativePath("~/modules/modify/" . $module->ID) . "\">" . $module->Title . "</a>"),
					new ListViewItemColumn("chDescription", $module->Description)
				));
			}
			$lv->Render();
		}
	}
	class ModuleManagementPage extends WebPage
	{
		public $Module;
		
		protected function Initialize()
		{
			$this->Title = "Manage Module";
			$this->Subtitle = $this->Module->Title;
		}
		
		protected function RenderContent()
		{
		?>
		<form method="POST">
		<?php
			$tc = new TabContainer();
			$tc->TabPages[] = new TabPage("tabGeneralInformation", "General Information", null, null, null, function()
			{
				?>
				<div class="FormView">
					<div class="Field">
						<label for="txtModuleTitle">Title:</label>
						<input id="txtModuleTitle" name="module_Title" type="text" value="<?php echo($this->Module->Title); ?>" />
					</div>
					<div class="Field">
						<label for="txtModuleDescription" style="vertical-align: top;">Description: </label>
						<textarea id="txtModuleDescription" name="module_Description" style="width: 100%;" rows="5"><?php echo($this->Module->Description); ?></textarea>
					</div>
				</div>
				<?php
			});
			$tc->TabPages[] = new TabPage("tabApplicationMenuItems", "Application Menu Items", null, null, null, function()
			{
				$lv = new ListView();
				$lv->Columns = array
				(
					new ListViewColumn("chTitle", "Title"),
					new ListViewColumn("chDescription", "Description"),
					new ListViewColumn("chTarget", "Target")
				);
				
				$menuitems = $this->Module->GetMainMenuItems();
				foreach ($menuitems as $menuitem)
				{
					$lv->Items[] = new ListViewItem(array
					(
						new ListViewItemColumn("chTitle", $menuitem->Title),
						new ListViewItemColumn("chDescription", $menuitem->Description),
						new ListViewItemColumn("chTarget", $menuitem->TargetURL)
					));
				}
				
				$lv->Render();
			});
			$tc->TabPages[] = new TabPage("tabModulePages", "Module Pages", null, null, null, function()
			{
				$lv = new ListView();
				$lv->Columns = array
				(
					new ListViewColumn("chURL", "URL")
				);
			
				$pages = $this->Module->GetPages();
				foreach ($pages as $modulePage)
				{
					$lv->Items[] = new ListViewItem(array
					(
						new ListViewItemColumn("chURL", "<a href=\"" . System::ExpandRelativePath("~/modules/modify/" . $this->Module->ID . "/pages/" . $modulePage->ID) . "\">" . $modulePage->URL . "</a>", $modulePage->URL)
					));
				}
				
				$lv->Render();
			});
			
			$tc->Render();
			
			?>
			<div class="Buttons" style="text-align: right;">
				<input class="Button Default" type="submit" value="Save Changes" />
				<a class="Button" href="<?php echo(System::ExpandRelativePath("~/modules")); ?>">Discard Changes</a>
			</div>
		</form>
		<?php
		}
	}
	
	System::$Modules[] = new \WebFX\Module("net.phoenixsns.TenantManager.Module", array
	(
		new ModulePage("modules", array
		(
			new ModulePage("", function($page, $path)
			{
				$page = new ModuleMainPage();
				$page->Render();
				return true;
			}),
			new ModulePage("modify", function($page, $path)
			{
				$module = \PhoenixSNS\Objects\Module::GetByID($path[0], true);
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$module->Title = $_POST["module_Title"];
					$module->Description = $_POST["module_Description"];
					$module->Update();
					
					System::Redirect("~/modules/modify/" . $path[0]);
				}
				else
				{
					$page = new ModuleManagementPage();
					$page->Module = $module;
					$page->Render();
				}
				return true;
			})
		))
	));
?>