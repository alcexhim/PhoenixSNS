<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	class Content
	{
		public $Language;
		public $Path;
		public $Title;
		public $Content;
		public $DateCreated;
		
		public static function Create($path, $title, $content)
		{
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "contents (content_path, content_title, content_content, content_creator_id, content_creator_timestamp) VALUES (" .
				"'" . $MySQL->real_escape_string($path) . "', " .
				"'" . $MySQL->real_escape_string($title) . "', " .
				"'" . $MySQL->real_escape_string($content) . "', " .
				User::GetCurrent()->ID . ", " .
				"NOW()" .
				")";
			
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public static function GetByAssoc($values)
		{
			if ($values == null) return null;
			$content = new Content();
			$content->Language = Language::GetByID($values["content_language_id"]);
			$content->Path = $values["content_path"];
			$content->Title = $values["content_title"];
			$content->Content = $values["content_content"];
			$content->Creator = User::GetByID($values["content_creator_id"]);
			$content->DateCreated = $values["content_creator_timestamp"];
			return $content;
		}
		public static function GetByPath($path)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "contents WHERE content_path = '" . $MySQL->real_escape_string($path) . "' AND content_language_id = " . Language::GetCurrent()->ID;
			$result = $MySQL->query($query);
			if ($result->num_rows < 1) return null;
			
			$values = $result->fetch_assoc();
			return Content::GetByAssoc($values);
		}
		public static function Enumerate($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "contents";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Content::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function Update($path, $title, $content)
		{
			global $MySQL;
			$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "contents SET content_path = '" . $MySQL->real_escape_string($path) . "', content_title = '" . $MySQL->real_escape_string($title) . "', content_content = '" . $MySQL->real_escape_string($content) . "' WHERE content_path = '" . $MySQL->real_escape_string($this->Path) . "' AND content_language_id = " . $this->Language->ID;
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function Delete()
		{
			global $MySQL;
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "contents WHERE content_path = '" . $MySQL->real_escape_string($this->Path) . "' AND content_language_id = " . $this->Language->ID;
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
	}
?>