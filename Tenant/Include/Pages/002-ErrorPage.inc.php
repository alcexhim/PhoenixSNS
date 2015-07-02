<?php
	namespace PhoenixSNS\Pages;
	use PhoenixSNS\MasterPages\MessagePage;
	
	class ErrorPage extends MessagePage
	{
		public $ErrorCode;
		public $ErrorDescription;
		
		public function __construct()
		{
			parent::__construct();
			$this->MessageAreaClassName = "Error";
		}
		
		protected function AfterRenderMessage()
		{
			?>
			<p><?php
			if ($this->ErrorCode == "" && $this->ErrorDescription == "")
			{
			}
			else
			{
				echo("Error " . $this->ErrorCode . ": " . $this->ErrorDescription);
			}
			?></p>
			<?php
		}
	}
?>