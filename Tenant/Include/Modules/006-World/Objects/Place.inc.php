<?php
	namespace PhoenixSNS\Modules\World\Objects;
	
	use WebFX\System;
	
	class Place
	{
		public $ID;
		public $Name;
		public $Title;
		public $Description;
		public $Enabled;
		public $Capacity;
		
		public $CreationTimestamp;
		public $CreationUser;
		
		public static function Get()
		{
			global $MySQL;
			// allocate space for the list of places
			$places = array();
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Places";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				
				$place = Place::GetByAssoc($values);
				
				// add the place to the list of places
				$places[] = $place;
			}
			
			// return the list of places
			return $places;
		}
		public static function GetByAssoc($values)
		{
			$place = new Place();
			$place->ID = $values["place_ID"];
			$place->Name = $values["place_Name"];
			$place->Title = $values["place_Title"];
			$place->Description = $values["place_Description"];
			$place->Enabled = ($values["place_Enabled"] == 1);
			
			$place->Capacity = $values["place_Capacity"];
			if (!is_numeric($place->Capacity)) $place->Capacity = 0;
			
			$place->CreationTimestamp = $values["place_CreationTimestamp"];
			$place->CreationUser = User::GetByID($values["place_CreationUserID"]);
			return $place;
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return Place::GetByID($idOrName);
			return Place::GetByName($idOrName);
		}
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Places WHERE place_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return Place::GetByAssoc($values);
			}
			return null;
		}
		public static function GetByName($name)
		{
			if ($name == null) return null;
			
			global $MySQL;
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Places WHERE place_Name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return Place::GetByAssoc($values);
			}
			return null;
		}
		
		public function GetURL()
		{
			$url = System::$Configuration["Application.BasePath"] . "/world/";
			$url .= $this->Name;
			return $url;
		}
		
		public function GetClippingRegions()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PlaceClippingRegions WHERE clippingregion_PlaceID = " . $this->ID;
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
		public function GetHotspots()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PlaceHotspots WHERE hotspot_PlaceID = " . $this->ID;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = PlaceHotspot::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function ToJSON()
		{
			$json = "{";
			$json .= "\"ID\": " . $this->ID . ",";
			$json .= "\"Name\": \"" . \JH\Utilities::JavaScriptEncode($this->Name, "\"") . "\",";
			$json .= "\"Title\": \"" . \JH\Utilities::JavaScriptEncode($this->Title, "\"") . "\",";
			$json .= "\"Description\": \"" . \JH\Utilities::JavaScriptEncode($this->Description, "\"") . "\",";
			$json .= "\"Enabled\": " . ($this->Enabled ? "true" : "false") . ",";
			$json .= "\"Capacity\": " . $this->Capacity;
			$json .= "}";
			return $json;
		}
	}
?>