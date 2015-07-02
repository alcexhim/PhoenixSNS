<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;

	/**
	 * @deprecated Please use SingleInstance or MultipleInstance data type for enumeration properties.
	 * @author Michael Becker
	 */
	class TenantEnumerationChoice
	{
		public $ID;
		public $Enumeration;
		public $Name;
		public $Description;
		public $Value;
		
		public function __construct($name, $value)
		{
			$this->Name = $name;
			$this->Value = $value;
		}
	}
	
	/**
	 * @deprecated Please use SingleInstance or MultipleInstance data type for enumeration properties.
	 * @author Michael Becker
	 */
	class TenantEnumeration
	{
		public $ID;
		public $Tenant;
		public $Module;
		public $Name;
		public $Description;
		public $Choices;
		
		public function __construct($name, $description = null, $choices = null)
		{
			if ($choices == null) $choices = array();
			
			$this->Name = $name;
			$this->Description = $description;
			$this->Choices = $choices;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new TenantEnumeration();
			$item->ID = $values["enum_ID"];
			$item->Tenant = Tenant::GetByID($values["enum_TenantID"]);
			$item->Module = Module::GetByID($values["enum_ModuleID"]);
			$item->Name = $values["enum_Name"];
			$item->Description = $values["enum_Description"];
			return $item;
		}
		
		public static function Get($max = null)
		{
			$tenant = Tenant::GetCurrent();
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantEnumerations WHERE enum_TenantID = " . $tenant->ID;
			if (is_numeric($max))
			{
				$query .= " LIMIT " . $max;
			}
			
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantEnumeration::GetByAssoc($values);
			}
			
			return $retval;
		}
		
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$tenant = Tenant::GetCurrent();
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantEnumerations WHERE enum_ID = " . $id . " AND enum_TenantID = " . $tenant->ID;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantEnumeration::GetByAssoc($values);
		}
		
		public function Update()
		{
			global $MySQL;
			
			if ($this->ID == null)
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantEnumerations (enum_TenantID, enum_ModuleID, enum_ParentEnumerationID, enum_Name, enum_Description) VALUES (";
				$query .= ($this->Tenant == null ? "NULL" : $this->Tenant->ID) . ", ";
				$query .= ($this->Module == null ? "NULL" : $this->Module->ID) . ", ";
				$query .= "'" . $MySQL->real_escape_string($this->Name) . "', ";
				$query .= "'" . $MySQL->real_escape_string($this->Description) . "'";
				$query .= ")";
			}
			else
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "TenantEnumerations SET ";
				$query .= "enum_TenantID = " . ($this->Tenant == null ? "NULL" : $this->Tenant->ID) . ", ";
				$query .= "enum_ModuleID = " . ($this->Module == null ? "NULL" : $this->Module->ID) . ", ";
				$query .= "enum_Name = '" . $MySQL->real_escape_string($this->Name) . "', ";
				$query .= "enum_Description = '" . $MySQL->real_escape_string($this->Description) . "'";
				$query .= " WHERE enum_ID = " . $this->ID;
			}
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "TenantEnumerationChoices WHERE choice_EnumerationID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			foreach ($this->Choices as $choice)
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantEnumerationChoices (choice_EnumerationID, choice_Name, choice_Description) VALUES (";
				$query .= $this->ID . ", ";
				$query .= "'" . $MySQL->real_escape_string($choice->Name) . "', ";
				$query .= "'" . $MySQL->real_escape_string($choice->Description) . "'";
				
				$result = $MySQL->query($query);
				if ($result === false) return false;
			}
			
			if ($this->ID == null)
			{
				$this->ID = $MySQL->insert_id;
			}
			
			return true;
		}
	}
?>