<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	/**
	 * Represents a type by which to group related tenants.
	 * @author Michael Becker
	 */
	class TenantType
	{
		/**
		 * The unique, incremental ID number of this TenantType
		 * @var int
		 */
		public $ID;
		/**
		 * The title of this TenantType
		 * @var string
		 */
		public $Title;
		/**
		 * A short description for this TenantType
		 * @var string
		 */
		public $Description;
		/**
		 * Counts the number of TenantTypes available on this server.
		 * @return int The number of TenantTypes available on this server.
		 */
		public static function Count()
		{
			global $MySQL;
			$query = "SELECT COUNT(tenanttype_ID) FROM " . System::$Configuration["Database.TablePrefix"] . "TenantTypes";
			$result = $MySQL->query($query);
			if ($result === false) return 0;
			$values = $result->fetch_array();
			return $values[0];
		}
		/**
		 * Creates a new TenantType object based on the given values from the database.
		 * @param array $values
		 * @return TenantType based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new TenantType();
			$item->ID = $values["tenanttype_ID"];
			$item->Title = $values["tenanttype_Title"];
			$item->Description = $values["tenanttype_Description"];
			return $item;
		}
		/**
		 * Retrieves all TenantTypes
		 * @param int $max The maximum number of TenantTypes to return
		 * @return TenantType[] array of TenantTypes
		 */
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantTypes";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantType::GetByAssoc($values);
			}
			return $retval;
		}
		/**
		 * Retrieves a single TenantType with the given ID.
		 * @param int $id The ID of the TenantType to return
		 * @return NULL|\TenantType The TenantType with the given ID, or NULL if no TenantType with the given ID was found
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantTypes WHERE tenanttype_ID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantType::GetByAssoc($values);
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
			$json .= "\"Title\":\"" . \JH\Utilities::JavaScriptDecode($this->Title, "\"") . "\",";
			$json .= "\"Description\":\"" . \JH\Utilities::JavaScriptDecode($this->Description, "\"") . "\"";
			$json .= "}";
			return $json;
		}
	}
?>