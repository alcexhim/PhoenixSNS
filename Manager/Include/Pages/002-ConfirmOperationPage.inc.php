<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	
	// use PhoenixSNS\Objects\DataCenter;
	use PhoenixSNS\Objects\PaymentPlan;
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantStatus;
	use PhoenixSNS\Objects\TenantType;
	
	class ConfirmOperationPage extends WebPage
	{
		public $Message;
		
		public $ReturnButtonURL;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "Confirm Operation";
			
			$this->ReturnButtonURL = "~/";
		}
		
		protected function RenderContent()
		{
			?>
			<p><?php echo($this->Message); ?></p>
			<form method="POST">
				<input type="hidden" name="Confirm" value="1" />
				<input class="Button Default" type="submit" value="Continue" />
				<a class="Button" href="<?php echo(System::ExpandRelativePath($this->ReturnButtonURL)); ?>">Cancel</a>
			</form>
			<?php
		}
	}
?>