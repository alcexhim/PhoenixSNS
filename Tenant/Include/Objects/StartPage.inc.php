<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	class StartPage
	{
		public $ID;
		public $Title;
		public $URL;
		
		public static function GetByAssoc($values)
		{
			$item = new StartPage();
			$item->ID = $values["startpage_ID"];
			$item->Title = $values["startpage_Title"];
			$item->URL = $values["startpage_URL"];
			return $item;
		}
		
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "StartPages WHERE startpage_ID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count < 1) return null;
			
			$values = $result->fetch_assoc();
			return StartPage::GetByAssoc($values);
		}
		
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "StartPages";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = StartPage::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function ToJSON()
		{
			$json = "{";
			$json .= "\"ID\":" . $this->ID . ",";
			$json .= "\"Title\":\"" . \JH\Utilities::JavaScriptDecode($this->Title) . "\",";
			$json .= "\"URL\":\"" . \JH\Utilities::JavaScriptDecode($this->URL) . "\"";
			$json .= "}";
			return $json;
		}
		
	}
?>