<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	class SecurityPermission
	{
		public $ID;
		public $Name;
		public $Title;
		
		public static function GetByAssoc($values)
		{
			$item = new SecurityPermission();
			$item->ID = $values["permission_ID"];
			$item->Name = $values["permission_Name"];
			$item->Title = $values["permission_Title"];
			return $item;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "SecurityPermissions WHERE permission_ID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count < 1) return null;
			
			$values = $result->fetch_assoc();
			$item = SecurityPermission::GetByAssoc($values);
			return $item;
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "SecurityPermissions";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$item = SecurityPermission::GetByAssoc($values);
				if ($item == null) continue;
				$retval[] = $item;
			}
			return $retval;
		}
		public static function Initialize()
		{
			$permissions = SecurityPermission::Get();
			$t = "";
			$t .= "namespace PhoenixSNS\\Objects;\n";
			$t .= "class SecurityPermissions\n";
			$t .= "{\n";
			foreach ($permissions as $permission)
			{
				$t .= "\tpublic static $" . $permission->Name . ";\n";
			}
			$t .= "}\n\n";
			foreach ($permissions as $permission)
			{
				$t .= "SecurityPermissions::$" . $permission->Name . " = SecurityPermission::GetByID(" . $permission->ID . ");\n";
			}
			eval($t);
		}
	}
	
?>