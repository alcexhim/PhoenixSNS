<?php
	class EventType
	{
		public $ID;
		public $Title;
		public $Color;
		
		private static function GetByAssoc($values)
		{
			$eventType = new EventType();
			$eventType->ID = $values["event_type_id"];
			$eventType->Title = $values["event_type_title"];
			$eventType->Color = $values["event_type_color"];
			return $eventType;
		}
		public static function GetByID($id)
		{
			$query = "SELECT * FROM phpmmo_event_types WHERE event_type_id = " . $id;
			$result = mysql_query($query);
			$values = mysql_fetch_assoc($result);
			return EventType::GetByAssoc($values);
		}
	}
?>