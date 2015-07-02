<?php
	namespace PhoenixSNS\Objects;
	
	class Journal
	{
		public $ID;
		public $Creator;
		public $CreationDate;
		public $Name;
		public $Title;
		public $Description;
		
		public static function ValidateName($name)
		{
			$result = null;
			if (!ctype_alnum(str_replace(array('-', '_'), '', $name))) $result .= "Journal name must consist of only alphanumeric characters (0-9, A-Z, a-z), dash (-), or underscore(_).";
			return $result;
		}
		
		public static function GetByAssoc($values)
		{
			if ($values == null) return null;
			
			$journal = new Journal();
			$journal->ID = $values["journal_id"];
			$journal->Creator = User::GetByID($values["journal_creator_id"]);
			$journal->CreationDate = $values["journal_creation_date"];
			$journal->Name = $values["journal_name"];
			$journal->Title = $values["journal_title"];
			$journal->Description = $values["journal_description"];
			return $journal;
		}
		
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journals WHERE journal_id = " . $id;
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return Journal::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journals WHERE journal_name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return Journal::GetByAssoc($values);
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return Journal::GetByID($idOrName);
			return Journal::GetByName($idOrName);
		}
		
		public static function GetByUser($user)
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journals WHERE journal_creator_id = " . $user->ID;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Journal::GetByAssoc($values);
			}
			return $retval;
		}
		
		public static function Create($journal_name, $journal_title, $journal_description, $creator = null)
		{
			if ($creator == null) $creator = User::GetCurrent();
			if (!Journal::ValidateName($journal_name)) return false;
			$journal_title = HTMLPurifier::instance()->purify($journal_title);
			$journal_description = HTMLPurifier::instance()->purify($journal_description);
			
			global $MySQL;
			$query = "INSERT INTO phpmmo_journals (journal_creator_id, journal_name, journal_title, journal_description, journal_timestamp) VALUES (" . $creator->ID . ", " .
			"'" . $MySQL->real_escape_string($journal_name) . "', " .
			"'" . $MySQL->real_escape_string($journal_title) . "', " .
			"'" . $MySQL->real_escape_string($journal_description) . "', " .
			"NOW()" .
			");";
			
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		
		public function GetURL()
		{
			return System::ExpandRelativePath("~/community/members/" . $this->Creator->ShortName . "/journals/" . $this->Name);
		}
		
		public function GetEntries($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journal_entries WHERE journal_id = " . $this->ID . " ORDER BY journal_id, entry_timestamp_modified DESC";
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = JournalEntry::GetByAssoc($values);
			}
			return $retval;
		}
		public function Modify($journal_name, $journal_title, $journal_description)
		{
			if (!Journal::ValidateName($journal_name)) return false;
			$journal_title = HTMLPurifier::instance()->purify($journal_title);
			$journal_description = HTMLPurifier::instance()->purify($journal_description);
			
			global $MySQL;
			$query = "UPDATE phpmmo_journals SET " .
			"journal_name = '" . $MySQL->real_escape_string($journal_name) . "', " .
			"journal_title = '" . $MySQL->real_escape_string($journal_title) . "', " .
			"journal_description = '" . $MySQL->real_escape_string($journal_description) . "'" .
			" WHERE journal_id = " . $this->ID;
			
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function Remove()
		{
			global $MySQL;
			$query = "DELETE FROM phpmmo_journals WHERE journal_id = " . $this->ID;
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		
		public function CreateEntry($entry_name, $entry_title, $entry_content)
		{
			if (JournalEntry::ValidateName($entry_name) != null) return false;
			$entry_title = HTMLPurifier::instance()->purify($entry_title);
			$entry_content = HTMLPurifier::instance()->purify($entry_content);
			
			global $MySQL;
			$query = "INSERT INTO phpmmo_journal_entries (journal_id, entry_name, entry_title, entry_content, entry_timestamp_created, entry_timestamp_modified) VALUES (" . $this->ID . ", " .
			"'" . $MySQL->real_escape_string($entry_name) . "', " .
			"'" . $MySQL->real_escape_string($entry_title) . "', " .
			"'" . $MySQL->real_escape_string($entry_content) . "', " .
			"NOW(), NOW()" .
			")";
			
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		
	}
	class JournalEntry
	{
		public $ID;
		public $Name;
		public $Title;
		public $Content;
		public $Journal;
		public $TimestampCreated;
		public $TimestampModified;
		
		public static function ValidateName($name)
		{
			$result = null;
			if (!ctype_alnum(str_replace(array('-', '_'), '', $name))) $result .= "Entry name must consist of only alphanumeric characters (0-9, A-Z, a-z), dash (-), or underscore(_).";
			return $result;
		}
		
		public static function GetByAssoc($values)
		{
			$entry = new JournalEntry();
			$entry->ID = $values["entry_id"];
			$entry->Name = $values["entry_name"];
			$entry->Title = $values["entry_title"];
			$entry->Content = $values["entry_content"];
			$entry->Journal = Journal::GetByID($values["journal_id"]);
			$entry->TimestampCreated = $values["entry_timestamp_created"];
			$entry->TimestampModified = $values["entry_timestamp_modified"];
			return $entry;
		}
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journal_entries WHERE entry_id = " . $id;
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return JournalEntry::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journal_entries WHERE entry_name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return JournalEntry::GetByAssoc($values);
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return JournalEntry::GetByID($idOrName);
			return JournalEntry::GetByName($idOrName);
		}
		
		public function CountImpressions($member = null)
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journal_entry_impressions WHERE entry_id = " . $this->ID;
			if ($member != null)
			{
				// specific member's impressions
				$query .= " AND member_id = " . $member->ID;
			}
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = 0;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval += $values["impression_count"];
			}
			return $retval;
		}
		public function GetImpressions($member = null)
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journal_entry_impressions WHERE entry_id = " . $this->ID;
			if ($member != null)
			{
				// specific member's impressions
				$query .= " AND member_id = " . $member->ID;
			}
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = JournalImpression::GetByAssoc($values);
			}
			return $retval;
		}
		public function UpdateImpressions($member = null)
		{
			if ($member == null) $member = User::GetCurrent();
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journal_entry_impressions WHERE member_id = " . $member->ID;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count > 0)
			{
				$query = "UPDATE phpmmo_journal_entry_impressions SET impression_count = impression_count + 1 WHERE entry_id = " . $this->ID . " AND member_id = " . $member->ID;
				$result = $MySQL->query($query);
			}
			else
			{
				$query = "INSERT INTO phpmmo_journal_entry_impressions (entry_id, member_id, impression_count) VALUES (" . $this->ID . ", " . $member->ID . ", 1);";
				$result = $MySQL->query($query);
			}
			return ($MySQL->errno == 0);
		}
		
		public function Modify($entry_name, $entry_title, $entry_content)
		{
			if (!JournalEntry::ValidateName($entry_name)) return false;
			$entry_title = HTMLPurifier::instance()->purify($entry_title);
			$entry_content = HTMLPurifier::instance()->purify($entry_content);
			
			global $MySQL;
			$query = "UPDATE phpmmo_journal_entries SET " .
			"entry_name = '" . $MySQL->real_escape_string($entry_name) . "', " .
			"entry_title = '" . $MySQL->real_escape_string($entry_title) . "', " .
			"entry_content = '" . $MySQL->real_escape_string($entry_content) . "', " .
			"entry_timestamp_modified = NOW() WHERE entry_id = " . $this->ID;
			
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function Remove()
		{
			global $MySQL;
			$query = "DELETE FROM phpmmo_journal_entries WHERE entry_id = " . $this->ID;
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		
		public function AddComment($comment_title, $comment_content, $comment_parent = null, $comment_author = null)
		{
			// check to see if author exists
			if ($comment_author == null) $comment_author = User::GetCurrent();
			if ($comment_author == null) return false;
			
			// HTMLPurify the parameters
			$comment_title = HTMLPurifier::instance()->purify($comment_title);
			$comment_content = HTMLPurifier::instance()->purify($comment_content);
			
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "journal_entry_comments (journal_entry_id, author_id, comment_parent_id, comment_title, comment_content, comment_timestamp_created) VALUES (" . $this->ID . ", " .
			$comment_author->ID . ", " .
			($comment_parent == null ? "NULL" : $comment_parent->ID) . ", " .
			"'" . $MySQL->real_escape_string($comment_title) . "', " .
			"'" . $MySQL->real_escape_string($comment_content) . "', " .
			"NOW()" .
			")";
			
			$result = $MySQL->query($query);
			$success = ($MySQL->errno == 0);
			if ($success)
			{
				// notify the user that we commented on their journal
				Notification::Create($this->Journal->Creator, "I commented on <a href=\"" . $this->Journal->GetURL() . "/entries/" . $this->Name . "\">" . $this->Title . "</a>!", "\"" . $comment_content . "\"", User::GetCurrent());
			}
			return $success;
		}
		public function GetComments($max = null)
		{
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "journal_entry_comments WHERE journal_entry_id = " . $this->ID;
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			global $MySQL;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = JournalEntryComment::GetByAssoc($values);
			}
			return $retval;
		}
	}
	class JournalEntryComment extends Comment
	{
		public $ParentJournalEntry;
		
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM phpmmo_journal_entry_comments WHERE comment_id = " . $id . " LIMIT 1";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return JournalEntryComment::GetByAssoc($values);
		}
		public static function GetByAssoc($values)
		{
			$comment = new JournalEntryComment();
			$comment->ID = $values["comment_id"];
			$comment->ParentComment = JournalEntryComment::GetByID($values["comment_parent_id"]);
			$comment->Author = User::GetByID($values["author_id"]);
			$comment->ParentJournalEntry = JournalEntry::GetByID($values["journal_entry_id"]);
			$comment->Title = $values["comment_title"];
			$comment->Content = $values["comment_content"];
			$comment->TimestampCreated = $values["comment_timestamp_created"];
			return $comment;
		}
		
		protected function GetCommentTableName()
		{
			return "journal_entry_comments";
		}
	}
	class JournalImpression
	{
		public $User;
		public $Count;
		
		public static function GetByAssoc($values)
		{
			$impression = new JournalImpression();
			$impression->User = User::GetByID($values["member_id"]);
			$impression->Count = $values["impression_count"];
			return $impression;
		}
	}
?>