<?php
	namespace PhoenixSNS\Modules\World\Objects;
	use WebFX\System;
	
	class PlaceClippingRegion
	{
		public $ID;
		public $Place;
		public $Comments;
		
		public static function GetByAssoc($values)
		{
			$item = new PlaceClippingRegion();
			$item->ID = $values["region_ID"];
			$item->Place = Place::GetByID($values["region_PlaceID"]);
			$item->Comments = $values["region_Comments"];
			return $item;
		}
		
		public function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PlaceClippingRegions WHERE region_ID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return PlaceClippingRegion::GetByAssoc($values);
		}
		
		public function GetByPlace($place)
		{
			if (get_class($place) != "PhoenixSNS\\Modules\\World\\Objects\\Place") return array();
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PlaceClippingRegions WHERE region_PlaceID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = PlaceClippingRegion::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function GetPoints()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PlaceClippingRegionPoints WHERE regionpoint_RegionID = " . $this->ID;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = PlaceClippingRegionPoint::GetByAssoc($values);
			}
			return $retval;
		}
	}
?>