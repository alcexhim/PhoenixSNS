<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	class Task
	{
		public $ID;
		public $Title;
		public $NavigateURL;
		
		public static function GetByAssoc($values)
		{
			$item = new Task();
			$item->ID = $values["task_ID"];
			$item->Title = $values["task_Title"];
			$item->NavigateURL = $values["task_URL"];
			return $item;
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Tasks";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Task::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function ToJSON()
		{
			echo("{");
			echo("\"ID\":" . $this->ID . ",");
			echo("\"Title\":\"" . $this->Title . "\",");
			echo("\"NavigateURL\":\"" . $this->NavigateURL . "\"");
			echo("}");
		}
	}
?>