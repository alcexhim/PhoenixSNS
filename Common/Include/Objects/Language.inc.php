<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	/**
	 * Provides localization functionality for PhoenixSNS
	 * @author Michael Becker
	 */
	class Language
	{
		/**
		 * The unique, incremental ID number of this Module
		 * @var int
		 */
		public $ID;
		/**
		 * The name of this Language
		 * @var string
		 */
		public $Name;
		
		/**
		 * Creates a new Language object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\Language based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new Language();
			$item->ID = $values["language_ID"];
			$item->Name = $values["language_Name"];
			return $item;
		}
		/**
		 * Retrieves all Languages
		 * @param string $max The maximum number of Languages to return
		 * @return \PhoenixSNS\Objects\Language[] array of all the Languages available on this server
		 */
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Languages";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = $MySQL->query($query);
			$retval = array();
			if ($result === false) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$item = Language::GetByAssoc($values);
				if ($item != null) $retval[] = $item;
			}
			return $retval;
		}
		/**
		 * Retrieves a single Language with the given ID
		 * @param int $id The ID of the Language to return
		 * @return NULL|\PhoenixSNS\Objects\Language The Language with the given ID, or NULL if no language with the given ID was found
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Languages WHERE language_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count < 1) return null;
			
			$values = $result->fetch_assoc();
			return Language::GetByAssoc($values);
		}
		/**
		 * Retrieves the language currently in use by the currently logged-in user.
		 * @return \PhoenixSNS\Objects\Language The language currently in use by the currently logged-in user.
		 */
		public static function GetCurrent()
		{
			return Language::GetByID(1);
		}
	}
?>