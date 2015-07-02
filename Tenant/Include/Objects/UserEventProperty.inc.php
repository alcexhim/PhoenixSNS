<?php
	class UserEventProperty
	{
		public $User;
		public $Event;
		public $Name;
		public $Value;
		public $Exists;
		
		public static function GetByAssoc($values)
		{
			$property = new UserEventProperty();
			$property->User = mmo_get_user_by_id($values["member_id"]);
			$property->Event = mmo_get_event_by_id($values["event_id"]);
			$property->Name = $values["property_name"];
			$property->Value = $values["property_value"];
			$property->Exists = true;
			return $property;
		}
		public static function Contains($user, $event, $propertyName)
		{
			$query = "SELECT COUNT(*) FROM phpmmo_member_event_properties WHERE member_id = " . $user->ID . " AND event_id = " . $event->ID . " AND property_name = '" . mysql_real_escape_string($propertyName) . "'";
			$result = mysql_query($query);
			$values = mysql_fetch_array($result);
			return ($values[0] > 0);
		}
		
		public function Save()
		{
			if ($this->Exists)
			{
				$query = "UPDATE phpmmo_member_event_properties SET property_value = '" . mysql_real_escape_string($this->Value) . "' WHERE member_id = " . $this->User->ID . " AND event_id = " . $this->Event->ID . " AND property_name = '" . mysql_real_escape_string($this->Name) . "'";
			}
			else
			{
				$query = "INSERT INTO phpmmo_member_event_properties (property_value, member_id, event_id, property_name) VALUES ('" . mysql_real_escape_string($this->Value) . "', " . $this->User->ID . ", " . $this->Event->ID . ", '" . mysql_real_escape_string($this->Name) . "')";
			}
			$result = mysql_query($query);
			return (mysql_errno() == 0);
		}
	}
?>