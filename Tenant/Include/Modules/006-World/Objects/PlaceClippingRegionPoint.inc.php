<?php
	namespace PhoenixSNS\Modules\World\Objects;
	
	class PlaceClippingRegionPoint
	{
		public $Region;
		public $Left;
		public $Top;
		
		public static function GetByAssoc($values)
		{
			$item = new PlaceClippingRegionPoint();
			$item->Region = PlaceClippingRegion::GetByID($values["regionpoint_RegionID"]);
			$item->Left = $values["regionpoint_Left"];
			$item->Top = $values["regionpoint_Top"];
			return $item;
		}
	}
?>