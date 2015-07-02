<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use WebFX\Controls\TabContainer;
	use WebFX\Controls\TabPage;
	
	use WebFX\ModulePage;
	
	use PhoenixSNS\TenantManager\MasterPages\NavigationButton;
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	
	use PhoenixSNS\Objects\Organization;
	
	class OrganizationWebPage extends WebPage
	{
		
	}
	class OrganizationBrowsePage extends OrganizationWebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "Organization Management";
			
			$this->HeaderButtons[] = new NavigationButton("~/organizations/modify", "Create Organization", "fa-plus-circle", "return nav('/organizations/modify');", "Default");
		}
		
		protected function RenderContent()
		{
			$items = Organization::Get();
		?>
		<table class="ListView" style="width: 100%;">
			<tr>
				<th>Title</th>
			</tr>
			<?php
				foreach ($items as $item)
				{
					?>
					<tr>
						<td><a href="<?php echo(System::ExpandRelativePath("~/organizations/modify/" . $item->ID)); ?>"><?php echo($item->Title); ?></a></td>
					</tr>
					<?php
				}
			?>
		</table>
		<?php
		}
	}
	class OrganizationModifyPage extends OrganizationWebPage
	{
		public $CurrentObject;
		
		protected function Initialize()
		{
			$this->Title = "Modify Organization";
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
				<table style="width: 100%;">
					<tr>
						<td style="width: 100px;">Title: </td>
						<td><input name="organization_Title" type="text" value="<?php echo($this->CurrentObject->Title); ?>" /></td>
					</tr>
				</table>
				<?php
			});
			$tbs->TabPages[] = new TabPage("tabUsers", "Users", null, null, null, function()
			{
			});
			$tbs->CurrentTab = $tbs->TabPages[0];
			$tbs->Render();
			?>
				<div class="Buttons">
					<input class="Button Default" type="submit" value="Save Changes" />
					<a class="Button" href="<?php echo(System::ExpandRelativePath("~/organizations")); ?>">Discard Changes</a>
				</div>
			</form>
			<?php
		}
	}
	
	System::$Modules[] = new \WebFX\Module("net.phoenixsns.TenantManager.Organization", array
	(
		new ModulePage("organizations", array
		(
			new ModulePage("", function($page, $path)
			{
				$page = new OrganizationBrowsePage();
				$page->Render();
				return true;
			}),
			new ModulePage("modify", function($page, $path)
			{
				$item = Organization::GetByID($path[0]);
				if ($item == null)
				{
					$item = new Organization();
				}
				
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$item->Title = $_POST["organization_Title"];
					if (!$item->Update())
					{
						global $MySQL;
						echo($MySQL->errno . ": " . $MySQL->error);
						return true;
					}
					
					System::Redirect("~/organizations");
				}
				else
				{
					$page = new OrganizationModifyPage();
					$page->CurrentObject = $item;
					$page->Render();
				}
				return true;
			})
		))
	));
?>