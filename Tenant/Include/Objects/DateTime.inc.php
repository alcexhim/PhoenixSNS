<?php
	namespace PhoenixSNS\Objects;
	class DateTime
	{
		private $nativeObject;
		private function __construct($nativeObject)
		{
			$this->nativeObject = $nativeObject;
		}
		
		public static function FromDatabase($value)
		{
			return new DateTime(new \DateTime($value));
		}
		
		public function ToString()
		{
		}
		public function ToJSON()
		{
			$this->nativeObject->format("Y-m-dTH:i:sZ");
		}
	}
?>