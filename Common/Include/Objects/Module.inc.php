<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	/**
	 * Represents a collection of objects available to all tenants that can be enabled or disabled on each specific tenant.
	 * @author Michael Becker
	 */
	class Module
	{
		/**
		 * The unique, incremental ID number of this Module
		 * @var int
		 */
		public $ID;
		
		/**
		 * The title of this Module
		 * @var string
		 */
		public $Title;
		
		/**
		 * A short description of this Module
		 * @var string
		 */
		public $Description;
		
		/**
		 * True if this Module is enabled; false if this Module is disabled
		 * @var boolean
		 */
		public $Enabled;
		
		/**
		 * Counts all of the available Modules.
		 * @return int The number of Modules available on this server.
		 */
		public static function Count()
		{
			global $MySQL;
			$query = "SELECT COUNT(module_ID) FROM " . System::$Configuration["Database.TablePrefix"] . "Modules";
			$result = $MySQL->query($query);
			if ($result === false) return 0;
			$values = $result->fetch_array();
			return $values[0];
		}
		
		/**
		 * Creates a new Module object based on the given values from the database.
		 * @param array $values
		 * @return Module based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new Module();
			$item->ID = $values["module_ID"];
			$item->Title = $values["module_Title"];
			$item->Description = $values["module_Description"];
			$item->Enabled = $values["module_Enabled"];
			return $item;
		}
		
		/**
		 * Retrieves all Modules, optionally associated with the given Tenant.
		 * @param int $max The maximum number of Tenants to return
		 * @param Tenant $tenant The tenant whose modules to return (or null to return all modules)
		 * @return Module[] array of Modules
		 */
		public static function Get($max = null, $tenant = null)
		{
			global $MySQL;
			
			$query = "SELECT module_ID, module_Title, module_Description";
			if ($tenant != null)
			{
				$query .= ", (tenantmodule_ModuleID = module_ID) AS module_Enabled";
			}
			else
			{
				$query .= ", 1 AS module_Enabled";
			}
			$query .= " FROM " . System::$Configuration["Database.TablePrefix"] . "Modules";
			if ($tenant != null)
			{
				$query .= ", " . System::$Configuration["Database.TablePrefix"] . "TenantModules";
				$query .= "  WHERE tenantmodule_ModuleID = module_ID AND tenantmodule_TenantID = " . $tenant->ID;
			}
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Module::GetByAssoc($values);
			}
			return $retval;
		}
		
		/**
		 * Retrieves a single Module with the given ID.
		 * @param int $id The ID of the Module to return
		 * @param boolean $forAllTenants True if the Module should be a global module; false if it should be a tenant-specific module
		 * @return NULL|\Module The Module with the given ID, or NULL if no Module with the given ID was found
		 */
		public static function GetByID($id, $forAllTenants = false)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT module_ID, module_Title, module_Description FROM " . System::$Configuration["Database.TablePrefix"] . "Modules WHERE module_ID = " . $id;
			if (!$forAllTenants)
			{
				$query = "SELECT module_ID, module_Title, module_Description FROM " . System::$Configuration["Database.TablePrefix"] . "Modules, " . System::$Configuration["Database.TablePrefix"] . "TenantModules WHERE tenantmodule_ModuleID = module_ID AND tenantmodule_TenantID = " . $CurrentTenant->ID . " AND module_ID = " . $id;
			}
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return Module::GetByAssoc($values);
		}
		
		/**
		 * Updates the server with the information in this object.
		 * @return boolean True if the update succeeded; false if an error occurred.
		 */
		public function Update()
		{
			global $MySQL;
			
			if (is_numeric($this->ID))
			{
				// id is set, so update
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "Modules SET ";
				$query .= "module_Title = '" . $MySQL->real_escape_string($this->Title) . "', ";
				$query .= "module_Description = '" . $MySQL->real_escape_string($this->Description) . "'";
				$query .= " WHERE module_ID = " . $this->ID;
			}
			else
			{
				// id is not set, so insert
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "Modules (module_Title, module_Description) VALUES (";
				$query .= "'" . $MySQL->real_escape_string($this->Title) . "', ";
				$query .= "'" . $MySQL->real_escape_string($this->Description) . "'";
				$query .= ")";
			}
			
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			if (!is_numeric($this->ID))
			{
				// id is not set, so set it
				$this->ID = $MySQL->insert_id;
			}
			return true;
		}
		
		/**
		 * Gets the main menu items associated with this Module.
		 * @return array The main menu items associated with this Module.
		 */
		public function GetMainMenuItems()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "ModuleMenuItems WHERE menuitem_ModuleID = " . $this->ID;
			$retval = array();
			$result = $MySQL->query($query);
			
			if ($result === false) return $retval;
			$count = $result->num_rows;
			if ($count == 0) return $retval;
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = ModuleMenuItem::GetByAssoc($values);
			}
			return $retval;
		}
		
		/**
		 * Gets the ModulePages associated with this Module.
		 * @return \PhoenixSNS\Objects\ModulePage[] an array of ModulePages associated with this Module
		 */
		public function GetPages()
		{
			global $MySQL;
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "ModulePages WHERE modulepage_ModuleID = " . $this->ID;
			$retval = array();
			$result = $MySQL->query($query);
			
			if ($result === false) return $retval;
			$count = $result->num_rows;
			if ($count == 0) return $retval;
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = ModulePage::GetByAssoc($values);
			}
			return $retval;
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