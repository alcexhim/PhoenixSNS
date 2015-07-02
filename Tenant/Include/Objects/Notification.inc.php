<?php
	class Notification
	{
		public $ID;
		public $Sender;
		public $Receiver;
		public $Title;
		public $Content;
		public $Timestamp;
		
		public static function GetByAssoc($values)
		{
			$notif = new Notification();
			$notif->ID = $values["notification_id"];
			$notif->Sender = User::GetByID($values["sender_id"]);
			$notif->Receiver = User::GetByID($values["receiver_id"]);
			$notif->Title = $values["notification_title"];
			$notif->Content = $values["notification_content"];
			$notif->Timestamp = $values["notification_timestamp"];
			return $notif;
		}
		public static function Create($receiver, $title, $content = null, $sender = null)
		{
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "member_notifications (sender_id, receiver_id, notification_title, notification_content, notification_timestamp) VALUES (" . ($sender == null ? "null" : $sender->ID) . ", " . $receiver->ID . ", " .
			"'" . mysql_real_escape_string($title) . "', " .
			($content == null ? "null" : ("'" . mysql_real_escape_string($content) . "'")) . ", " .
			"NOW())";
			$result = mysql_query($query);
			return ($result !== false);
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "member_notifications WHERE notification_id = " . $id;
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			if ($count == 0) return null;
			
			$values = mysql_fetch_assoc($result);
			return Notification::GetByAssoc($values);
		}
		public static function GetByReceiver($receiver, $max = null)
		{
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "member_notifications WHERE receiver_id = " . $receiver->ID;
			$query .= " ORDER BY notification_timestamp DESC";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_array($result);
				$retval[] = Notification::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function Remove()
		{
			$user = User::GetCurrent();
			if ($user == null) return false;
			
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "member_notifications WHERE receiver_id = " . $user->ID . " AND notification_id = " . $this->ID;
			$result = mysql_query($query);
			return ($result !== false);
		}
	}
?>