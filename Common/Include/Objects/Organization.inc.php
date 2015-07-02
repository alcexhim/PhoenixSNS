<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	class Organization
	{
		public $ID;
		public $Title;
		
		public static function Count()
		{
			global $MySQL;
			$query = "SELECT COUNT(organization_ID) FROM " . System::$Configuration["Database.TablePrefix"] . "Organizations";
			$result = $MySQL->query($query);
			if ($result === false) return 0;
			$values = $result->fetch_array();
			return $values[0];
		}
		public static function GetByAssoc($values)
		{
			$item = new Organization();
			$item->ID = $values["organization_ID"];
			$item->Title = $values["organization_Title"];
			return $item;
		}
		
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM ". System::$Configuration["Database.TablePrefix"] . "Organizations";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			
			$retval = array();
			if ($result === false) return $retval;
			
			for ($i = 0; $i < $result->num_rows; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Organization::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM ". System::$Configuration["Database.TablePrefix"] . "Organizations WHERE organization_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$values = $result->fetch_assoc();
			return Organization::GetByAssoc($values);
		}
		
		public static function Create($title)
		{
			$item = new Organization();
			$item->Title = $title;
			
			if ($item->Update())
			{
				return $item;
			}
			return null;
		}
		
		public function Update()
		{
			global $MySQL;
			if ($this->ID != null)
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "Organizations SET ";
				$query .= "organization_Title = '" . $MySQL->real_escape_string($this->Title) . "'";
				$query .= " WHERE organization_ID = " . $this->ID;
			}
			else
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "Organizations (organization_Title, organization_TimestampCreated) VALUES (";
				$query .= "'" . $MySQL->real_escape_string($this->Title) . "', ";
				$query .= "NOW()";
				$query .= ")";
			}
			
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			if ($this->ID == null)
			{
				$this->ID = $MySQL->insert_id;
			}
			return true;
		}
		
	}
?>