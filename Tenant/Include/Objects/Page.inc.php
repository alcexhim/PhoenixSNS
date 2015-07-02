<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	class Page
	{
		public $ID;
		public $Name;
		public $Title;
		public $Creator;
		public $Description;
		public $TimestampCreated;
		
		public function ToJSON()
		{
			$json = "{";
			$json .= "\"ID\":" . $this->ID . ",";
			$json .= "\"Name\":\"" . \JH\Utilities::JavaScriptEncode($this->Name,"\"") . "\",";
			$json .= "\"Title\":\"" . \JH\Utilities::JavaScriptEncode($this->Title,"\"") . "\",";
			$json .= "\"Creator\":" . $this->Creator->ToJSON() . ",";
			$json .= "\"Description\":\"" . \JH\Utilities::JavaScriptEncode($this->Description,"\"") . "\",";
			$json .= "\"TimestampCreated\":\"" . $this->TimestampCreated . "\"";
			$json .= "}";
			return $json;
		}
	
		public static function ValidateName($name)
		{
			$result = null;
			if (!ctype_alnum(str_replace(array('-', '_'), '', $name))) $result .= "Group name must consist of only alphanumeric characters (0-9, A-Z, a-z), dash (-), or underscore (_).";
			return $result;
		}
		public static function Create($name, $title, $description, $creator = null)
		{
			if (Page::ValidateName($name) != null) return false;
			
			global $MySQL;
			
			if ($creator == null) $creator = User::GetCurrent();
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "Pages (page_Name, page_Title, page_Description, page_CreationUserID, page_CreationTimestamp) VALUES (" .
				"'" . $MySQL->real_escape_string($name) . "', " .
				"'" . $MySQL->real_escape_string($title) . "', " .
				"'" . $MySQL->real_escape_string($description) . "', " .
				$creator->ID . ", " .
				"NOW()" .
				")";
			
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public static function Count()
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "Pages";
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if (is_numeric($values[0])) return $values[0];
			return 0;
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Pages";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Page::GetByAssoc($values);
			}
			return $retval;
		}
		
		public static function GetByAssoc($values)
		{
			if ($values == null) return null;
			$page = new Page();
			$page->ID = $values["page_ID"];
			$page->Name = $values["page_Name"];
			$page->Title = $values["page_Title"];
			$page->Description = $values["page_Description"];
			$page->Creator = User::GetByID($values["page_CreationUserID"]);
			$page->TimestampCreated = $values["page_CreationTimestamp"];
			return $page;
		}
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Pages WHERE page_ID = " . $id;
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return Page::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Pages WHERE page_Name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return Page::GetByAssoc($values);
		}
		public function AddMember($user)
		{
			if ($user == null) return false;
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "PageMembers (pagemember_PageID, pagemember_UserID) VALUES (" . $this->ID . ", " . $user->ID . ")";
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function RemoveMember($user)
		{
			if ($user == null) return false;
			global $MySQL;
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "PageMembers WHERE pagemember_PageID = " . $this->ID . " AND pagemember_UserID = " . $user->ID;
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function HasMember($user)
		{
			if ($user == null) return false;
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "PageMembers WHERE pagemember_PageID = " . $this->ID . " AND pagemember_UserID = " . $user->ID;
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if (is_numeric($values[0])) return ($values[0] > 0);
			return false;
		}
		public function CountMembers()
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "PageMembers WHERE pagemember_PageID = " . $this->ID;
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if (is_numeric($values[0])) return $values[0];
			return 0;
		}
		public function GetMembers($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Users, " . System::$Configuration["Database.TablePrefix"] . "PageMembers WHERE " . System::$Configuration["Database.TablePrefix"] . "PageMembers.pagemember_PageID = " . $this->ID . " AND " . System::$Configuration["Database.TablePrefix"] . "PageMembers.pagemember_UserID = " . System::$Configuration["Database.TablePrefix"] . "Users.user_ID";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = User::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function GetActions()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PageActions WHERE pageaction_PageID = " . $this->ID;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				if ($values == null) continue;
				
				$action = new PageAction();
				$action->Title = $values["pageaction_Title"];
				$action->URL = $values["pageaction_URL"];
				$retval[] = $action;
			}
			return $retval;
		}
	}
	class PageAction
	{
		public $Title;
		public $URL;
		
	}
?>