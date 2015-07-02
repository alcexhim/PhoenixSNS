<?php
	class Message
	{
		public $ID;
		public $Sender;
		public $Timestamp;
		public $Title;
		public $Content;
		public $Status;
		
		public static function Create($sender, $receivers, $title, $content)
		{
			if (count($receivers) == 0) return;
			if ($sender == null) $sender = User::GetCurrent();
			
			$query = "INSERT INTO phpmmo_messages (message_sender, message_title, message_content, message_timestamp) VALUES (" .
				$sender->ID . ", " .
				"'" . mysql_real_escape_string($title) . "', " .
				"'" . mysql_real_escape_string($content) . "', " .
				"NOW()" .
			");";
			
			$result = mysql_query($query);
			if (mysql_errno() != 0) return;
			
			$query = "SELECT LAST_INSERT_ID();";
			$result = mysql_query($query);
			if (mysql_errno() != 0) return;
			
			$values = mysql_fetch_array($result);
			$last_insert_id = $values[0];
			
			foreach ($receivers as $receiver)
			{
				if ($receiver == null) continue;
				
				$query = "INSERT INTO phpmmo_message_receivers (message_id, receiver_id, message_status) VALUES (" . $last_insert_id . ", " . $receiver->ID . ", 0);";
				$result = mysql_query($query);
				if (mysql_errno() != 0) return;
			}
		}
		
		public static function Get($max = null)
		{
			$query = "SELECT * FROM phpmmo_messages ORDER BY message_timestamp DESC";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$message = Message::GetByAssoc($values);
				if ($message != null) $retval[] = $message;
			}
			return $retval;
		}
		public static function GetByID($message_id)
		{
			if ($message_id == null || !is_numeric($message_id)) return null;
			$query = "SELECT * FROM phpmmo_messages WHERE message_id = " . $message_id;
			$result = mysql_query($query);
			$values = mysql_fetch_assoc($result);
			$message = Message::GetByAssoc($values);
			return $message;
		}
		public static function GetByAssoc($values)
		{
			$message = new Message();
			$message->ID = $values["message_id"];
			$message->Sender = User::GetByID($values["message_sender"]);
			$message->Title = $values["message_title"];
			$message->Content = strrepl($values["message_content"]);
			$message->Timestamp = $values["message_timestamp"];
			$message->Status = $values["message_status"];
			
			if (!$message->IsAuthenticated()) return null;
			
			return $message;
		}
		
		public static function GetByReceiver($receiver = null, $show_only_unread = false)
		{
			$query = "SELECT * FROM phpmmo_messages, phpmmo_message_receivers";
			if ($receiver == null)
			{
				$receiver = User::GetCurrent();
			}
			if ($receiver != null)
			{
				$query .= " WHERE phpmmo_messages.message_id = phpmmo_message_receivers.message_id AND phpmmo_message_receivers.receiver_id = " . $receiver->ID;
			}
			if ($show_only_unread)
			{
				$query .= " AND phpmmo_message_receivers.message_status = 0";
			}
			$query .= " ORDER BY message_timestamp DESC";
			
			$result = mysql_query($query);
			
			$count = mysql_num_rows($result);
			
			$messages = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$message = Message::GetByAssoc($values);
				if ($message == null) continue;
				
				$messages[] = $message;
			}
			return $messages;
		}
		public static function GetBySender($sender = null, $show_only_unread = false)
		{
			$query = "SELECT * FROM phpmmo_messages, phpmmo_message_receivers";
			if ($sender == null)
			{
				$sender = User::GetCurrent();
			}
			if ($sender != null)
			{
				$query .= " WHERE phpmmo_messages.message_id = phpmmo_message_receivers.message_id AND message_sender = " . $sender->ID;
			}
			if ($show_only_unread)
			{
				$query .= " AND phpmmo_message_receivers.message_status = 0";
			}
			$query .= " ORDER BY message_timestamp DESC";
			
			$result = mysql_query($query);
			
			$count = mysql_num_rows($result);
			
			$messages = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$message = Message::GetByAssoc($values);
				$messages[] = $message;
			}
			return $messages;
		}
			
		public static function CountByReceiver($receiver = null, $show_only_unread = false)
		{
			$query = "SELECT COUNT(*) FROM phpmmo_messages, phpmmo_message_receivers WHERE phpmmo_messages.message_id = phpmmo_message_receivers.message_id";
			if ($receiver == null)
			{
				$receiver = User::GetCurrent();
			}
			if ($receiver != null)
			{
				$query .= " AND phpmmo_message_receivers.receiver_id = " . $receiver->ID;
			}
			if ($show_only_unread)
			{
				$query .= " AND phpmmo_message_receivers.message_status = 0";
			}
			$result = mysql_query($query);
			$values = mysql_fetch_array($result);
			return $values[0];
		}
	
		public function SetStatus($status)
		{
			if ($status == null || !is_numeric($status)) return null;
			
			$query = "UPDATE phpmmo_message_receivers SET message_status = " . $status . " WHERE message_id = " . $this->ID;
			$result = mysql_query($query);
			return (mysql_errno() == 0);
		}
		
		public function GetReceivers()
		{
			$retval = array();
			$query = "SELECT * FROM phpmmo_members, phpmmo_message_receivers WHERE phpmmo_members.member_id = phpmmo_message_receivers.receiver_id AND phpmmo_message_receivers.message_id = " . $this->ID;
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = User::GetByAssoc($values);
			}
			return $retval;
		}
		public function IsAuthenticated()
		{
			$rcvrs = $this->GetReceivers();
			foreach ($rcvrs as $rcvr)
			{
				if ($rcvr->IsAuthenticated) return true;
			}
			return false;
		}
	}
?>