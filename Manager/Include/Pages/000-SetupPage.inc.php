<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	use PhoenixSNS\Objects\Tenant;
	
	class SetupPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->RenderHeader = false;
			$this->RenderSidebar = false;
		}
		protected function BeforeContent()
		{
		?>
		<div class="SetupContainer">
			<div class="Billboard">
				<img src="<?php echo(System::ExpandRelativePath("~/Images/Billboard.png")); ?>" />
			</div>
			<div class="Branding">
				<span class="ProductName">PhoenixSNS</span> Initial Configuration
			</div>
			<div class="Credentials">
		<?php
		}
		protected function AfterContent()
		{
		?>
			</div>
		</div>
		<?php
		}
	}
?>