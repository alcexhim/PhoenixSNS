<?php
	namespace PhoenixSNS\Objects;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	use PDO;
	
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
			$pdo = DataSystem::GetPDO();
			$themes = array();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Themes";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$themes[] = Theme::GetByAssoc($values);
			}
			return $themes;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Themes WHERE theme_ID = :theme_ID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":theme_ID" => $id
			));
			if ($result->num_rows == 0) return null;
			
			$values = $result->fetch(PDO::FETCH_ASSOC);
			return Theme::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Themes WHERE theme_Name = :theme_Name";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":theme_Name" => $name
			));
			
			if ($statement->rowCount() > 0)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
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