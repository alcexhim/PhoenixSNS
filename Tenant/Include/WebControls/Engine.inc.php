<?php
	namespace PhoenixSNS\WebControls;
	
	use Phast\WebControl;
	
	class Engine extends WebControl
	{
		/**
		 * The ID of the Place to display in the Engine.
		 * @var int
		 */
		public $PlaceID;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "div";
			$this->ClassList[] = "PhoenixEngine";
			
			$this->PlaceID = null;
		}
		
		protected function RenderBeginTag()
		{
			
			
			parent::RenderBeginTag();
		}
	}
?>