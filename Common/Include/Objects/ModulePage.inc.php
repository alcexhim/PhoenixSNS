<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	/**
	 * Represents a Web page associated with a specific Module.
	 * @author Michael Becker
	 */
	class ModulePage
	{
		/**
		 * The unique, incremental ID number of this Module
		 * @var int
		 */
		public $ID;
		
		/**
		 * The parent Module that owns this ModulePage
		 * @var Module
		 */
		public $Module;
		
		/**
		 * The ModulePage that is the parent of this ModulePage
		 * @var ModulePage
		 */
		public $ParentPage;
		
		/**
		 * The URL of this ModulePage
		 * @var string
		 */
		public $URL;
		
		/**
		 * The HTML content of this ModulePage
		 * @var string
		 */
		public $Content;
		
		/**
		 * Creates a new ModulePage object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\ModulePage based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new ModulePage();
			$item->ID = $values["modulepage_ID"];
			$item->Module = Module::GetByID($values["modulepage_ModuleID"]);
			$item->ParentPage = ModulePage::GetByID($values["modulepage_ParentPageID"]);
			$item->URL = $values["modulepage_URL"];
			$item->Content = $values["modulepage_Content"];
			return $item;
		}
		/**
		 * Retrieves all ModulePages on the server
		 * @param int $max The maximum number of ModulePages to retrieve
		 * @return \PhoenixSNS\Objects\ModulePage[] array of ModulePages
		 */
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "ModulePages";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = ModulePage::GetByAssoc($values);
			}
			return $retval;
		}
		
		/**
		 * Retrieves a single ModulePage with the given ID
		 * @param int $id The ID of the ModulePage to return
		 * @return NULL|\PhoenixSNS\Objects\ModulePage The ModulePage with the given ID, or NULL if no ModulePage with the requested ID was found.
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Modules WHERE module_ID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return ModulePage::GetByAssoc($values);
		}
		
		/**
		 * Gets the JSON representation of this object for use in AJAX calls.
		 * @return string The JSON representation of this object.
		 */
		public function ToJSON()
		{
			$json = "";
			$json .= "{";
			$json .= "\"ID\":" . $this->ID . ",";
			if ($this->Module == null)
			{
				$json .= "\"Module\":null,";
			}
			else
			{
				$json .= "\"Module\":" . $this->Module->ToJSON() . ",";
			}
			if ($this->ParentPage == null)
			{
				$json .= "\"ParentPage\":null,";
			}
			else
			{
				$json .= "\"ParentPage\":" . $this->ParentPage->ToJSON() . ",";
			}
			$json .= "\"URL\":\"" . \JH\Utilities::JavaScriptDecode($this->URL, "\"") . "\",";
			$json .= "\"Content\":\"" . \JH\Utilities::JavaScriptDecode($this->Content, "\"") . "\"";
			$json .= "}";
			return $json;
		}
	}
?>