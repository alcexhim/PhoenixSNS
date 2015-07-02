<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	class Language
	{
		public $ID;
		public $Name;
		public $Title;
		
		public static function Get()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Languages";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Language::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetCurrent()
		{
			$user = User::GetCurrent();
			if ($user != null)
			{
				return $user->Language;
			}
			return Language::GetByID(1);
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return Language::GetByID($idOrName);
			return Language::GetByName($idOrName);
		}
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Languages WHERE language_ID = " . $id;
			
			$result = $MySQL->query($query);
			if ($result->num_rows < 1) return null;
			
			$values = $result->fetch_assoc();
			return Language::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Languages WHERE language_Name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			if ($result->num_rows < 1) return null;
			
			$values = $result->fetch_assoc();
			return Language::GetByAssoc($values);
		}
		public static function GetByAssoc($values)
		{
			$language = new Language();
			$language->ID = $values["language_ID"];
			$language->Name = $values["language_Name"];
			$language->Title = $values["language_Title"];
			return $language;
		}
		
		public function SetDefaultForUser($user = null)
		{
			if ($user == null) $user = User::GetCurrent();
			
			global $MySQL;
			$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Users SET user_LanguageID = " . $this->ID . " WHERE user_ID = " . $user->ID;
			$result = $MySQL->query($query);
		}
	}
	class LanguageString
	{
		public $Language;
		public $Name;
		public $Value;
		
		public static function Get($language = null, $max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings";
			if ($language != null)
			{
				$query .= " WHERE languagestring_LanguageID = " . $language->ID;
			}
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			
			$query .= " ORDER BY languagestring_LanguageID ASC, languagestring_StringName ASC";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = LanguageString::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetUnassigned($language = null, $max = null)
		{
			global $MySQL;
			$query = "SELECT DISTINCT languagestring_StringName FROM " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings";
			if ($language != null)
			{
				$query .= " WHERE NOT languagestring_StringName IN (SELECT languagestring_StringName FROM " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings WHERE languagestring_LanguageID = " . $language->ID . ")";
			}
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			
			$query .= " ORDER BY " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings.languagestring_StringName ASC";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = LanguageString::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByAssoc($values)
		{
			$string = new LanguageString();
			$string->Language = Language::GetByID($values["languagestring_LanguageID"]);
			$string->Name = $values["languagestring_StringName"];
			$string->Value = $values["languagestring_StringValue"];
			return $string;
		}
		public static function GetByName($stringName, $language = null)
		{
			if ($language == null) $language = Language::GetCurrent();
			if ($language == null)
			{
				return "@TRANSLATE['" . $stringName . "']@";
			}
			
			global $MySQL;
			$query = "SELECT languagestring_StringValue FROM " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings WHERE languagestring_StringName = '" . $MySQL->real_escape_string($stringName) . "' AND languagestring_LanguageID  = " . $language->ID;
			$result = $MySQL->query($query);
			if ($result === false) return "@TRANSLATE['" . $stringName . "']@";
			
			$count = $result->num_rows;
			if ($count == 0) return "@TRANSLATE['" . $stringName . "']@";
			
			$values = $result->fetch_array();
			return $values[0];
		}
		
		public function Update()
		{
			if ($this->Language == null) return false;
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings WHERE language_id = " . $this->Language->ID . " AND languagestring_StringName = '" . $MySQL->real_escape_string($this->Name) . "'";
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			$values = $result->fetch_array();
			$count = $values[0];
			
			if ($count == 0)
			{
				$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings (languagestring_ID, languagestring_StringName, languagestring_StringValue) VALUES (" . $this->Language->ID . ", '" . $MySQL->real_escape_string($this->Name) . "', '" . $MySQL->real_escape_string($this->Value) . "')";
			}
			else
			{
				$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings SET languagestring_StringValue = '" . $MySQL->real_escape_string($this->Value) . "' WHERE languagestring_LanguageID = " . $this->Language->ID . " AND languagestring_StringName = '" . $MySQL->real_escape_string($this->Name) . "'";
			}
			
			$result = $MySQL->query($query);
			return ($result !== false);
		}
		
		public function Delete()
		{
			if ($this->Language == null) return false;
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings WHERE languagestring_LanguageID = " . $this->Language->ID . " AND languagestring_StringName = '" . $MySQL->real_escape_string($this->Name) . "'";
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			$values = $result->fetch_array();
			$count = $values[0];
			
			if ($count > 0)
			{
				$query = "DELETE FROM " . System::GetConfigurationValue("Database.TablePrefix") . "LanguageStrings WHERE languagestring_LanguageID = " . $this->Language->ID . " AND languagestring_StringName = '" . $MySQL->real_escape_string($this->Name) . "'";
				
				$result = $MySQL->query($query);
				return ($result !== false);
			}
			return false;
		}
	}
?>