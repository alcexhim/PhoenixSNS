<?php
	namespace PhoenixSNS\Modules\World\Objects;
	
	\Enum::Create("PhoenixSNS\\Modules\\World\\Objects\\PlaceHotspotTargetType", "URL", "Script", "Place", "Unknown");
	
	class PlaceHotspot
	{
		public $ID;
		public $Title;
		
		public $Left;
		public $Top;
		public $Width;
		public $Height;
		
		// Target can be a script, a URL, or a Place ID depending on TargetType.
		public $TargetPlace;
		public $TargetScript;
		public $TargetURL;
		
		public $TargetType;
		
		public static function GetByAssoc($values)
		{
			$item = new PlaceHotspot();
			$item->ID = $values["hotspot_ID"];
			$item->Title = $values["hotspot_Title"];
			$item->Left = $values["hotspot_Left"];
			$item->Top = $values["hotspot_Top"];
			$item->Width = $values["hotspot_Width"];
			$item->Height = $values["hotspot_Height"];
			$item->TargetPlace = Place::GetByID($values["hotspot_TargetPlaceID"]);
			$item->TargetScript = $values["hotspot_TargetScript"];
			$item->TargetURL = $values["hotspot_TargetURL"];
			switch ($values["hotspot_TargetTypeID"])
			{
				case 1:
				{
					$item->TargetType = PlaceHotspotTargetType::URL;
					break;
				}
				case 2:
				{
					$item->TargetType = PlaceHotspotTargetType::Script;
					break;
				}
				case 3:
				{
					$item->TargetType = PlaceHotspotTargetType::Place;
					break;
				}
				default:
				{
					$item->TargetType = PlaceHotspotTargetType::Unknown;
					break;
				}
			}
			return $item;
		}
		
		public function ToJSON()
		{
			$json = "{";
			$json .= "\"Left\": " . $this->Left . ",";
			$json .= "\"Top\": " . $this->Top . ",";
			$json .= "\"Width\": " . $this->Width . ",";
			$json .= "\"Height\": " . $this->Height . ",";
			$json .= "\"Target\": " . \JH\Utilities::JavaScriptEncode($this->Target, "\"") . ",";
			$json .= "\"TargetType\": ";
			switch ($this->TargetType)
			{
				case PlaceHotspotTargetType::URL:
				{
					$json .= "PlaceHotspotTargetType.URL";
					break;
				}
				case PlaceHotspotTargetType::Script:
				{
					$json .= "PlaceHotspotTargetType.Script";
					break;
				}
				case PlaceHotspotTargetType::Place:
				{
					$json .= "PlaceHotspotTargetType.Place";
					break;
				}
				default:
				{
					$json .= "PlaceHotspotTargetType.Unknown";
					break;
				}
			}
			$json .= "}";
			return $json;
		}
	}
?>