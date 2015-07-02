<?php
	namespace PhoenixSNS\MasterPages;
	
	use WebFX\System;
	
	class MessagePage extends WebPage
	{
		public $Message;
		
		public $ReturnButtonURL;
		public $ReturnButtonText;
		
		public function Initialize()
		{
			parent::Initialize();
			if ($this->Title == null) $this->Title = "Information";
			if ($this->ReturnButtonURL == null) $this->ReturnButtonURL = "~/";
			if ($this->ReturnButtonText == null) $this->ReturnButtonText = "Return to " . System::$Configuration["Application.Name"];
		}
		
		protected function BeforeRenderMessage()
		{
		}
		protected function AfterRenderMessage()
		{
		}
		
		protected function RenderContent()
		{
?>
<div class="Panel">
	<h3 class="PanelTitle"><?php echo($this->Title); ?></h3>
	<div class="PanelContent">
	<?php
		$this->BeforeRenderMessage();
	?>
		<p><?php echo($this->Message); ?></p>
	<?php
		$this->AfterRenderMessage();
	?>
		<p style="text-align: center;"><a href="<?php echo(System::ExpandRelativePath($this->ReturnButtonURL)); ?>"><?php echo($this->ReturnButtonText); ?></a></p>
	</div>
</div>
<?php
		}
	}
?>