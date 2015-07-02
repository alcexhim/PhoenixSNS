<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	class Group
	{
		public $ID;
		public $Name;
		public $Title;
		public $Description;
		public $Creator;
		public $DateCreated;
		
		public function ToJSON()
		{
			$json = "{";
			$json .= "\"ID\": " . $this->ID . ", ";
			$json .= "\"Name\": \"" . \JH\Utilities::JavaScriptEncode($this->Name) . "\", ";
			$json .= "\"Title\": \"" . \JH\Utilities::JavaScriptEncode($this->Title) . "\", ";
			$json .= "\"Description\": \"" . \JH\Utilities::JavaScriptEncode($this->Description) . "\", ";
			if ($this->Creator == null)
			{
				$json .= "\"CreationUser\": null, ";
			}
			else
			{
				$json .= "\"CreationUser\": " . $this->Creator->ToJSON() . ", ";
			}
			$json .= "\"CreationDate\": \"" . $this->DateCreated->ToJSON() . "\"";
			$json .= "}";
			return $json;
		}
		
		public static function Create($name, $title, $description = null, $creator = null)
		{
			if ($creator == null) $creator = User::GetCurrent();
			if (Group::ValidateName($name) != null) return false;
			
			if ($description != null) $description = HTMLPurifier::instance()->purify($description);
			
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "Groups (group_Name, group_Title, group_Description, group_CreationUserID, group_CreationTimestamp) VALUES (" .
			"'" . $MySQL->real_escape_string($name) . "', " .
			"'" . $MySQL->real_escape_string($title) . "', " .
			($description == null ? "NULL" : ("'" . $MySQL->real_escape_string($description) . "'")) . ", " .
			$creator->ID . ", " .
			"NOW()" .
			");";
			
			$MySQL->query($query);
			
			if ($MySQL->errno != 0) return false;
			
			$id = $MySQL->insert_id;
			
			if ($MySQL->errno != 0) return false;
			
			$group = Group::GetByID($id);
			$group->AddMember($creator);
			
			return ($MySQL->errno == 0);
		}
		public static function Count()
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "Groups";
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if ($values == false) return 0;
			return $values[0];
		}
		
		public static function ValidateName($name)
		{
			$result = null;
			if (!ctype_alnum(str_replace(array('-', '_'), '', $name))) $result .= "Group name must consist of only alphanumeric characters (0-9, A-Z, a-z), dash (-), or underscore (_).";
			return $result;
		}
		
		public static function GetByAssoc($values)
		{
			if ($values == null) return null;
			
			$group = new Group();
			$group->ID = $values["group_ID"];
			$group->Name = $values["group_Name"];
			$group->Title = $values["group_Title"];
			$group->Description = $values["group_Description"];
			$group->Creator = User::GetByID($values["group_CreationUserID"]);
			$group->DateCreated = DateTime::FromDatabase($values["group_CreationTimestamp"]);
			return $group;
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Groups ORDER BY group_Title";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Group::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return Group::GetByID($idOrName);
			return Group::GetByName($idOrName);
		}
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Groups WHERE group_ID = " . $id . " ORDER BY group_Title";
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			$retval = Group::GetByAssoc($values);
			return $retval;
		}
		public static function GetByName($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Groups WHERE group_Name = '" . $MySQL->real_escape_string($name) . "' ORDER BY group_Title";
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			$retval = Group::GetByAssoc($values);
			return $retval;
		}
		public static function GetByUser($user = null, $max = null)
		{
			if ($user == null) return array();
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Groups, " . System::$Configuration["Database.TablePrefix"] . "GroupMembers WHERE " . System::$Configuration["Database.TablePrefix"] . "Groups.group_ID = " . System::$Configuration["Database.TablePrefix"] . "GroupMembers.groupmember_GroupID AND " . System::$Configuration["Database.TablePrefix"] . "GroupMembers.groupmember_UserID = " . $user->ID . " ORDER BY group_Title";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Group::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function Delete()
		{
			$CurrentUser = User::GetCurrent();
			if ($CurrentUser->ID != $this->Creator->ID) return false;
			
			global $MySQL;
			
			$queries = array
			(
				("DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "GroupTopics WHERE group_ID = " . $this->ID),
				("DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "GroupMembers WHERE group_ID = " . $this->ID),
				("DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "Groups WHERE group_ID = " . $this->ID)
			);
			
			foreach ($queries as $query)
			{
				$result = $MySQL->query($query);
				if (!$result) return false;
			}
			return true;
		}
		
		public function GetMembers($max = null)
		{
			global $MySQL;
			$query = "SELECT " . System::$Configuration["Database.TablePrefix"] . "Users.user_ID, " . System::$Configuration["Database.TablePrefix"] . "Users.user_DisplayName, " . System::$Configuration["Database.TablePrefix"] . "GroupMembers.groupmember_UserID FROM " . System::$Configuration["Database.TablePrefix"] . "GroupMembers, " . System::$Configuration["Database.TablePrefix"] . "Users WHERE " . System::$Configuration["Database.TablePrefix"] . "GroupMembers.groupmember_GroupID = " . $this->ID . " AND " . System::$Configuration["Database.TablePrefix"] . "Users.user_ID = " . System::$Configuration["Database.TablePrefix"] . "GroupMembers.groupmember_UserID ORDER BY " . System::$Configuration["Database.TablePrefix"] . "Users.user_DisplayName";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = User::GetByID($values["user_ID"]);
			}
			return $retval;
		}
		public function CountMembers()
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "GroupMembers WHERE group_ID = " . $this->ID;
			$result = $MySQL->query($query);
			$retval = $result->fetch_array();
			if (!is_numeric($retval[0])) return 0;
			
			return $retval[0];
		}
		
		public function GetTopics($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "GroupTopics WHERE grouptopic_GroupID = " . $this->ID . ($max == null ? "" : (" LIMIT " . $max));
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = GroupTopic::GetByAssoc($values);
			}
			return $retval;
		}
		public function CountTopics()
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "group_topics WHERE group_ID = " . $this->ID;
			$result = $MySQL->query($query);
			$retval = $result->fetch_array();
			return $retval[0];
		}
		
		public function AddMember($member)
		{
			if ($member == null) return false;
			if ($this->HasMember($member)) return true;
			
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "group_members (group_ID, member_id) VALUES (" . $this->ID . ", " . $member->ID . ")";
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function RemoveMember($member)
		{
			if ($member == null) return false;
			if (!$this->HasMember($member)) return true;
			
			global $MySQL;
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "group_members WHERE group_ID = " . $this->ID . " AND member_id = " . $member->ID;
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function HasMember($member)
		{
			if ($member == null) return false;
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "group_members WHERE group_ID = " . $this->ID . " AND member_id = " . $member->ID;
			$result = $MySQL->query($query);
			$retval = $result->fetch_array();
			return ($retval[0] > 0);
		}
		
		public function HasPermission($user, $permission_id)
		{
			if ($user == null) return false;
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "group_permission_mappings WHERE group_ID = " . $this->ID . " AND permission_id = " . $permission_id . " AND (member_id = " . $user->ID . " OR member_id = 0)";
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if (!is_numeric($values[0])) return false;
			return ($values[0] > 0);
		}
	}
	class GroupTopic
	{
		public $ID;
		public $Name;
		public $Title;
		public $Description;
		public $Creator;
		
		public static function ValidateName($name)
		{
			$result = null;
			if (!ctype_alnum(str_replace(array('-', '_'), '', $name))) $result .= "Topic name must consist of only alphanumeric characters (0-9, A-Z, a-z), dash (-), or underscore(_).";
			return $result;
		}
		
		public static function Create($parentGroup, $name, $title, $description = null, $creator = null)
		{
			if ($parentGroup == null) return false;
			if ($creator == null) $creator = User::GetCurrent();
			if (GroupTopic::ValidateName($name) != null) return false;
			
			$title = HTMLPurifier::instance()->purify($title);
			$description = HTMLPurifier::instance()->purify($description);
			
			global $MySQL;
			$MySQL->query("INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "group_topics (group_ID, topic_name, topic_title, topic_description, topic_creator_id) VALUES (" .
			$parentGroup->ID . ", " .
			"'" . $MySQL->real_escape_string($name) . "', " .
			"'" . $MySQL->real_escape_string($title) . "', " .
			($description == null ? "NULL" : ("'" . $MySQL->real_escape_string($description) . "'")) . ", " .
			$creator->ID .
			");");
			
			return ($MySQL->errno == 0);
		}
		
		public static function GetByAssoc($values)
		{
			$topic = new GroupTopic();
			$topic->ID = $values["grouptopic_ID"];
			$topic->Name = $values["grouptopic_Name"];
			$topic->Title = $values["grouptopic_Title"];
			$topic->Description = $values["grouptopic_Description"];
			$topic->CreationUser = User::GetByID($values["grouptopic_CreationUserID"]);
			$topic->CreationTimestamp = User::GetByID($values["grouptopic_CreationTimestamp"]);
			return $topic;
		}
		public static function GetByIDOrName($group, $idOrName)
		{
			if (is_numeric($idOrName)) return GroupTopic::GetByID($group, $idOrName);
			return GroupTopic::GetByName($group, $idOrName);
		}
		public static function GetByID($group, $id, $max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "GroupTopics WHERE grouptopic_GroupID = " . $group->ID . " AND grouptopic_ID = " . $id . ($max == null ? "" : (" LIMIT " . $max));
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return GroupTopic::GetByAssoc($values);
		}
		public static function GetByName($group, $name, $max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "GroupTopics WHERE grouptopic_GroupID = " . $group->ID . " AND grouptopic_Name = '" . $MySQL->real_escape_string($name) . "'" . ($max == null ? "" : (" LIMIT " . $max));
			
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return GroupTopic::GetByAssoc($values);
		}
		
		public function AddComment($title, $content, $author = null, $reply_comment_id = null)
		{
			if ($author == null) $author = User::GetCurrent();
			
			$title = HTMLPurifier::instance()->purify($title);
			$content = HTMLPurifier::instance()->purify($content);
			
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "GroupTopicComments (grouptopiccomment_TopicID, grouptopiccomment_CreationUserID, grouptopiccomment_Title, grouptopiccomment_Content, grouptopiccomment_ReplyCommentID, grouptopiccomment_CreationTimestamp) VALUES (" .
			$topic->ID . ", " .
			$author->ID . ", " .
			"'" . $MySQL->real_escape_string($title) . "', " .
			"'" . $MySQL->real_escape_string($content) . "', " .
			($reply_to == null ? "NULL" : $reply_comment_id) . ", " .
			"NOW()" .
			");";
			
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function RemoveComment($comment)
		{
			if ($comment == null) return false;
			
			global $MySQL;
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "GroupTopicComments WHERE grouptopiccomment_ID = " . $comment->ID;
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function GetComments($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "group_topic_comments WHERE topic_id = " . $this->ID . " ORDER BY comment_timestamp_created DESC";
			if (is_numeric($max)) $query .= (" LIMIT " . $max);
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = GroupTopicComment::GetByAssoc($values);
			}
			return $retval;
		}
		public function CountComments()
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "group_topic_comments WHERE topic_id = " . $this->ID;
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			return $values[0];
		}
	}
	class GroupTopicComment extends Comment
	{
		public static function GetByAssoc($values)
		{
			$comment = new GroupTopicComment();
			$comment->ID = $values["comment_id"];
			$comment->ParentComment = GroupTopicComment::GetByID($values["comment_parent_id"]);
			$comment->Author = User::GetByID($values["author_id"]);
			$comment->Title = $values["comment_title"];
			$comment->Content = $values["comment_content"];
			$comment->TimestampCreated = $values["comment_timestamp_created"];
			return $comment;
		}
		public static function GetByID($id)
		{
			if ($id == null) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "group_topic_comments WHERE comment_id = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return GroupTopicComment::GetByAssoc($values);
		}
		protected function GetCommentTableName()
		{
			return "group_topic_comments";
		}
	}
?>