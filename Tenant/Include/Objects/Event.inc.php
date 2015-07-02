<?php
	class Event
	{
		public $ID;
		public $Name;
		public $Title;
		public $Description;
		public $Content;
		
		public $BeginDateTime;
		public $EndDateTime;
		public $BeginDate;
		public $EndDate;
		
		public $Type;
		
		private static function GetByAssoc($values)
		{
			$event = new Event();
			$event->ID = $values["event_id"];
			$event->Name = $values["event_name"];
			$event->Title = $values["event_title"];
			$event->Description = $values["event_description"];
			$event->Content = $values["event_content"];
			$event->BeginDateTime = $values["event_date_begin"];
			$event->EndDateTime = $values["event_date_end"];
			$event->BeginDate = $values["event_date_begin_date"];
			$event->EndDate = $values["event_date_end_date"];
			
			$event->Type = EventType::GetByID($values["event_type"]);
			return $event;
		}
		public static function GetByID($id)
		{
			if ($id == null) return null;
			global $MySQL;
			$query = "SELECT *, DATE(event_date_begin) AS event_date_begin_date, DATE(event_date_end) AS event_date_end_date FROM phpmmo_events WHERE event_id = " . $id;
			$result = $MySQL->query($query);
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return Event::GetByAssoc($values);
			}
			return null;
		}
		public static function GetByName($name)
		{
			if ($name == null) return null;
			
			global $MySQL;
			$query = "SELECT *, DATE(event_date_begin) AS event_date_begin_date, DATE(event_date_end) AS event_date_end_date FROM phpmmo_events WHERE event_name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return Event::GetByAssoc($values);
			}
			return null;
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return Event::GetByID($idOrName);
			return Event::GetByName($idOrName);
		}
		public static function Get($onlyActive = true)
		{
			global $MySQL;
			$query = "SELECT *, DATE(event_date_begin) AS event_date_begin_date, DATE(event_date_end) AS event_date_end_date FROM phpmmo_events";
			if ($onlyActive) $query .= " WHERE event_date_begin <= NOW() AND event_date_end >= NOW()";
			$query .= " ORDER BY event_date_begin DESC";
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$event = Event::GetByAssoc($values);
				$retval[] = $event;
			}
			// return the list of events
			return $retval;
		}
		
		public function GetRestrictions()
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_restrictions, phpmmo_event_restrictions WHERE phpmmo_event_restrictions.event_id = " . $this->ID . " AND phpmmo_restrictions.restriction_id = phpmmo_event_restrictions.restriction_id";
			$result = $MySQL->query($query);
			if (!$result) return array();
			
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				
				$restriction = new Restriction();
				$restriction->ID = $values["restriction_id"];
				$restriction->Title = $values["restriction_title"];
				$retval[] = $restriction;
			}
			return $retval;
		}
		public function GetChances()
		{
			return array();
			
			$query = "SELECT * FROM phpmmo_restrictions, phpmmo_event_restrictions WHERE phpmmo_event_restrictions.event_id = " . $event->ID . " AND phpmmo_restrictions.restriction_id = phpmmo_event_restrictions.restriction_id";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				
				$restriction = new Restriction();
				$restriction->ID = $values["restriction_id"];
				$restriction->Title = $values["restriction_title"];
				$retval[] = $restriction;
			}
			return $retval;
		}
		public function GetRelatedEvents()
		{
			return array();
			
			global $MySQL;
			$query = "SELECT * FROM phpmmo_restrictions, phpmmo_event_restrictions WHERE phpmmo_event_restrictions.event_id = " . $event->ID . " AND phpmmo_restrictions.restriction_id = phpmmo_event_restrictions.restriction_id";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				
				$restriction = new Restriction();
				$restriction->ID = $values["restriction_id"];
				$restriction->Title = $values["restriction_title"];
				$retval[] = $restriction;
			}
			return $retval;
		}
		
		public function CountProperties($propertyName, $user = null)
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM phpmmo_member_event_properties WHERE event_id = " . $this->ID . " AND property_name = '" . $MySQL->real_escape_string($propertyName) . "'";
			if ($user != null) $query .= " AND member_id = " . $user->ID;
			
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			return ($values[0] > 0);
		}
		public function GetProperty($propertyName, $defaultValue = null, $user = null)
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM phpmmo_member_event_properties WHERE event_id = " . $event->ID . " AND property_name = '" . $MySQL->real_escape_string($propertyName) . "'";
			if ($user != null) $query .= " AND member_id = " . $user->ID;
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count > 0)
			{
				$values = $result->fetch_assoc();
				$property = UserEventProperty::GetByAssoc($values);
				return $property;
			}
			else
			{
				$property = new UserEventProperty();
				$property->User = $user;
				$property->Event = $event;
				$property->Name = $propertyName;
				$property->Value = $defaultValue;
				$property->Exists = false;
				return $property;
			}
		}
	}
?>