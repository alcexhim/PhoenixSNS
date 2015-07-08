<?php
	namespace PhoenixSNS\WebControls;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	class ResourceReference extends WebControl
	{
		public function __construct()
		{
			parent::__construct();
			$this->TagName = "span";
		}
		
		public $Type;
		public $Value;
		
		protected function RenderBeginTag()
		{
			$this->Attributes[] = new WebControlAttribute("title", $this->Value . " " . $this->Type);
			$this->ClassList[] = "ResourceReference";
			$this->Content = $this->Value;
			parent::RenderBeginTag();
		}
	}
?>