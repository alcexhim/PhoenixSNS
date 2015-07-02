<?php
	namespace PhoenixSNS\Controls;
	use WebFX\WebControl;
	
	class Hovercard extends WebControl
	{
		public $ContentURL;
		public $RequestMethod;
		
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->ContentURL = null;
			$this->RequestMethod = "GET";
		}
		
		protected function BeforeContent()
		{
			echo("<div class=\"Hovercard\" id=\"Hovercard_" . $this->ID . "\" data-contenturl=\"" . $this->ContentURL . "\" data-requestmethod=\"" . $this->RequestMethod . "\">");
		}
		protected function AfterContent()
		{
			echo("</div>");
			echo("<script type=\"text/javascript\">var " . $this->ID . " = new Hovercard(\"" . $this->ID . "\");</script>");
		}
	}
?>