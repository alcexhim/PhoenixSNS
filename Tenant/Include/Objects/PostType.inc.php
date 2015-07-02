<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	class PostType
	{
		public $ID;
		public $Title;
		public $CssClass;
		
		public static function GetByAssoc($values)
		{
			$item = new DashboardPostType();
			$item->ID = $values["posttype_ID"];
			$item->Title = $values["posttype_Title"];
			$item->CssClass = $values["posttype_CssClass"];
			return $item;
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PostTypes";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$retval = array();
			$result = $MySQL->query($query);
			if ($result === false) return $retval;
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = PostType::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PostTypes WHERE posttype_ID = " . $id;
			$retval = array();
			$result = $MySQL->query($query);
			if ($result === false) return $retval;
			$count = $result->num_rows;
			if ($count < 1) return null;
			
			$values = $result->fetch_assoc();
			return PostType::GetByAssoc($values);
		}
	}
?>