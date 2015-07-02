<?php
	class Forum
	{
		public $ID;
		public $Name;
		public $Title;
		public $Description;
	
		public $CreationUser;
		public $CreationTimestamp;
		
		public static function ValidateName($name)
		{
			$result = null;
			if (!ctype_alnum(str_replace(array('-', '_'), '', $name))) $result .= "URL must consist of only alphanumeric characters (0-9, A-Z, a-z), dash (-), or underscore (_).";
			return $result;
		}
		
		public static function Create($name, $title, $description, $user = null)
		{
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "forums (forum_name, forum_title, forum_description, forum_creation_member_id, forum_creation_timestamp) VALUES (" .
				"'" . mysql_real_escape_string($name) . "', " .
				"'" . mysql_real_escape_string($title) . "', " .
				"'" . mysql_real_escape_string($description) . "', " .
				$user->ID . ", " .
				"NOW()" .
			")";
			$result = mysql_query($query);
			if (!$result) return null;
			
			$id = mysql_insert_id();
			return Forum::GetByID($id);
		}
		
		public function Delete()
		{
			$CurrentUser = User::GetCurrent();
			if ($CurrentUser->ID != $this->CreationUser->ID) return false;
			
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "forums WHERE forum_id = " . $this->ID;
			$result = mysql_query($query);
			if (!$result) return false;
			
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "forum_topics WHERE forum_id = " . $this->ID;
			$result = mysql_query($query);
			if (!$result) return false;
			
			return true;
		}
		
		public function GetTopics($max = null)
		{
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "forum_topics WHERE forum_id = " . $this->ID . ($max == null ? "" : (" LIMIT " . $max));
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = ForumTopic::GetByAssoc($values);
			}
			return $retval;
		}
		public function CountTopics()
		{
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "forum_topics WHERE forum_id = " . $this->ID;
			$result = mysql_query($query);
			$retval = mysql_fetch_array($result);
			if (is_numeric($retval[0])) return $retval[0];
			return 0;
		}
		
		public static function Count($max = null)
		{
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "forums";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			$result = mysql_query($query);
			$values = mysql_fetch_array($result);
			if (is_numeric($values[0])) return $values[0];
			return 0;
		}
		public static function GetByAssoc($values)
		{
			$forum = new Forum();
			$forum->ID = $values["forum_id"];
			$forum->Name = $values["forum_name"];
			$forum->Title = $values["forum_title"];
			$forum->Description = $values["forum_description"];
			$forum->CreationUser = User::GetByID($values["forum_creation_member_id"]);
			$forum->CreationTimestamp = $values["forum_creation_timestamp"];
			return $forum;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "forums WHERE forum_id = " . $id;
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			if ($count == 0) return null;
			
			$values = mysql_fetch_assoc($result);
			return Forum::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "forums WHERE forum_name = '" . mysql_real_escape_string($name) . "'";
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			if ($count == 0) return null;
			
			$values = mysql_fetch_assoc($result);
			return Forum::GetByAssoc($values);
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return Forum::GetByID($idOrName);
			return Forum::GetByName($idOrName);
		}
		public static function Get($max = null)
		{
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "forums";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			$result = mysql_query($query);
			$retval = array();
			if (!$result) return $retval;
			
			$count = mysql_num_rows($result);
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = Forum::GetByAssoc($values);
			}
			return $retval;
		}
	}
	class ForumTopic
	{
		public $ID;
		public $Name;
		public $Title;
		public $Description;
		
		public $CreationUser;
		public $CreationTimestamp;
		
		public function GetPosts($max = null)
		{
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "forum_topic_posts WHERE topic_id = " . $this->ID;
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = ForumTopicPost::GetByAssoc($values);
			}
			return $retval;
		}
	}
	
	class ForumTopicPost
	{
		public $ID;
		public $Name;
		public $Title;
		public $Content;
	}
?>