<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	class Theme
	{
		public $ID;
		public $Name;
		public $Title;
		
		public $CreationUser;
		public $CreationTimestamp;
		
		public static function GetByAssoc($values)
		{
			$theme = new Theme();
			$theme->ID = $values["theme_ID"];
			$theme->Name = $values["theme_Name"];
			$theme->Title = $values["theme_Title"];
			if ($values["theme_CreationUserID"] != null)
			{
				$theme->CreationUser = User::GetByID($values["theme_CreationUserID"]);
			}
			else
			{
				$theme->CreationUser = null;
			}
			return $theme;
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$themes = array();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Themes";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$themes[] = Theme::GetByAssoc($values);
			}
			return $themes;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Themes WHERE theme_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result->num_rows == 0) return null;
			
			$values = $result->fetch_assoc();
			return Theme::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Themes WHERE theme_Name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return Theme::GetByAssoc($values);
			}
			return null;
		}
		
		/// <summary>
		/// gets the active theme in use by the currently logged-in user
		/// </summary>
		public static function GetCurrent()
		{
			$user = User::GetCurrent();
			if ($user == null) return null;
			return $user->Theme;
		}
	}
?>