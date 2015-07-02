<?php
	\Enum::Create("ShoutoutMessageVisibility", "Hidden", "Friends", "Network", "Everyone", "Blacklist", "Whitelist");
	
	class ShoutoutMessage
	{
		public $ID;
		public $Sender;
		public $Receiver;
		public $Content;
		public $Timestamp;
		public $Visibility;
		
		public function GetPraises()
		{
			$query = "SELECT * FROM phpmmo_members, phpmmo_shoutout_message_likes WHERE phpmmo_shoutout_message_likes.message_id = " . $this->ID . " AND phpmmo_members.member_id = phpmmo_shoutout_message_likes.member_id";
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = User::GetByAssoc($values);
			}
			return $retval;
		}
		public function GetComments($max = null)
		{
			$query = "SELECT * FROM phpmmo_shoutout_message_comments WHERE phpmmo_shoutout_message_comments.message_id = " . $this->ID . " ORDER BY comment_timestamp DESC";
			if ($max != null) $query .= (" LIMIT " . $max);
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = mmo_shoutout_message_comment_get_by_assoc($values);
			}
			return $retval;
		}
		
		public function Like($user = null)
		{
			if ($user == null) $user = User::GetCurrent();
			$query = "SELECT COUNT(*) FROM phpmmo_shoutout_message_likes WHERE message_id = " . $this->ID . " AND member_id = " . $user->ID;
			$result = mysql_query($query);
			$values = mysql_fetch_array($result);
			if (is_numeric($values[0]) && ($values[0] > 0))
			{
				// user already liked this message, delete the like
				$query = "DELETE FROM phpmmo_shoutout_message_likes WHERE message_id = " . $this->ID . " AND member_id = " . $user->ID;
				$result = mysql_query($query);
				return (mysql_errno() == 0);
			}
			else
			{
				// user has not already liked this message, so like it
				$query = "INSERT INTO phpmmo_shoutout_message_likes (message_id, member_id) VALUES (" . $this->ID . ", " . $user->ID . ");";
				$result = mysql_query($query);
				return (mysql_errno() == 0);
			}
		}
		
		public static function GetByAssoc($values)
		{
			$message = new ShoutoutMessage();
			$message->ID = $values["message_id"];
			$message->Sender = User::GetByID($values["message_sender_id"]);
			$message->Receiver = User::GetByID($values["message_receiver_id"]);
			$message->Content = strrepl($values["message_content"]);
			$message->Timestamp = $values["message_timestamp"];
			$message->Visibility = ShoutoutMessageVisibility::FromIndex($values["message_visibility"]);
			
			$CurrentUser = User::GetCurrent();
			if ($CurrentUser->ID != $message->Sender->ID)
			{
				switch ($message->Visibility)
				{
					case ShoutoutMessageVisibility::Hidden:
					{
						return null;
					}
					case ShoutoutMessageVisibility::Friends:
					{
						// only friends of the person posting it can see the post
						if ($CurrentUser == null) return null;
						if (!$CurrentUser->HasFriend($message->Sender)) return null;
						break;
					}
					case ShoutoutMessageVisibility::Network:
					{
						// everyone on Psychatica can see the post (i.e., must be logged in)
						if ($CurrentUser == null) return null;
						break;
					}
					case ShoutoutMessageVisibility::Everyone:
					{
						// entire Internet can see the post
						break;
					}
					case ShoutoutMessageVisibility::Blacklist:
					{
						// friends only
						return null;
					}
					case ShoutoutMessageVisibility::Whitelist:
					{
						// friends only
						return null;
					}
				}
			}
			return $message;
		}
		public static function GetByUser($user = null, $max = null)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return array();
			
			$query = "SELECT * FROM phpmmo_shoutout_messages WHERE message_receiver_id = " . $user->ID;
			$query .= " ORDER BY message_timestamp DESC";
			
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$item = ShoutoutMessage::GetByAssoc($values);
				if ($item == null) continue;
				$retval[] = $item;
			}
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return array();
			$query = "SELECT * FROM phpmmo_shoutout_messages WHERE message_id = " . $id;
			$result = mysql_query($query);
			$values = mysql_fetch_assoc($result);
			return ShoutoutMessage::GetByAssoc($values);
		}
		
		public static function Create($sender, $receiver, $content)
		{
			$content = HTMLPurifier::instance()->purify($content);
			
			$query = "INSERT INTO phpmmo_shoutout_messages (message_sender_id, message_receiver_id, message_content, message_timestamp) VALUES (" .
			$sender->ID . ", " .
			$receiver->ID . ", " . 
			"'" . mysql_real_escape_string($content) . "', " .
			"NOW()" .
			");";
			
			$result = mysql_query($query);
			$success = (mysql_errno() == 0);
			if ($success)
			{
				// notify the user that we sent them a shoutout
				Notification::Create($receiver, "I wrote you a Shoutout message!", "\"" . $content . "\"", $sender);
			}
			return $success;
		}
		
		public function ToJSON()
		{
			$json = "{";
			$json .= "\"ID\": " . $this->ID . ",";
			$json .= "\"Sender\": " . $this->Sender->ToJSON() . ",";
			$json .= "\"Receiver\": " . $this->Receiver->ToJSON() . ",";
			$json .= "\"Content\": \"" . JH\Utilities::JavaScriptEncode($this->Content) . "\",";
			$json .= "\"Timestamp\": \"" . $this->Timestamp . "\",";
			
			$visibility = ShoutoutMessageVisibility::ToIndex($this->Visibility);
			/*
			switch ($this->Visibility)
			{
				case ShoutoutMessageVisibility::Hidden:
				{
					$visibility = "ShoutoutMessageVisibility.Hidden";
					break;
				}
				case ShoutoutMessageVisibility::Friends:
				{
					$visibility = "ShoutoutMessageVisibility.Friends";
					break;
				}
				case ShoutoutMessageVisibility::Network:
				{
					$visibility = "ShoutoutMessageVisibility.Network";
					break;
				}
				case ShoutoutMessageVisibility::Everyone:
				{
					$visibility = "ShoutoutMessageVisibility.Everyone";
					break;
				}
				case ShoutoutMessageVisibility::Blacklist:
				{
					$visibility = "ShoutoutMessageVisibility.Blacklist";
					break;
				}
				case ShoutoutMessageVisibility::Whitelist:
				{
					$visibility = "ShoutoutMessageVisibility.Whitelist";
					break;
				}
			}
			*/
			
			$json .= "\"Visibility\": " . $visibility . "";
			$json .= "}";
			return $json;
		}
	}
	
	// Comments
	function mmo_shoutout_message_comment_get_by_assoc($values)
	{
		$comment = new Comment();
		$comment->ID = $values["comment_id"];
		$comment->Author = mmo_get_user_by_id($values["author_id"]);
		$comment->ReplyFrom = mmo_group_topic_get_comment_by_id($values["reply_comment_id"]);
		$comment->Title = $values["comment_title"];
		$comment->Content = $values["comment_content"];
		$comment->Timestamp = $values["comment_timestamp"];
		return $comment;
	}
	
?>