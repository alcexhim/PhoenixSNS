<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	use PhoenixSNS\Objects\Tenant;
	
	class ErrorPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "Error";
			$this->Subtitle = "Something went wrong";
			
			$this->RenderHeader = false;
			$this->RenderSidebar = false;
			
			$this->ReturnButtonURL = "~/";
			$this->ReturnButtonText = "Return to Main Page";
		}
		
		public $Message;
		
		public $ReturnButtonURL;
		public $ReturnButtonText;
		
		protected function RenderContent()
		{
		?>
		<p><?php echo($this->Message); ?></p>
		<p style="text-align: right;">
			<a href="<?php echo(System::ExpandRelativePath($this->ReturnButtonURL)); ?>"><?php echo($this->ReturnButtonText); ?></a>
		</p>
		<?php
		}
	}
?>