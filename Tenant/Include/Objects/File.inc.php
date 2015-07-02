<?php
	class File
	{
		public $ID;
		public $Name;
		public $Description;
		public $Category;
		public $CreationUser;
		public $CreationTimestamp;
		public $DeletionUser;
		public $DeletionTimestamp;
		
		public $CheckoutUser;
		public $CheckoutTimestamp;
		
		public static function GetByAssoc($values)
		{
			$file = new File();
			$file->ID = $values["file_id"];
			$file->Name = $values["file_name"];
			$file->Description = $values["file_description"];
			$file->Category = FileCategory::GetByID($values["file_category_id"]);
			$file->CreationUser = User::GetByID($values["file_creation_member_id"]);
			$file->CreationTimestamp = $values["file_creation_timestamp"];
			$file->DeletionUser = User::GetByID($values["file_deletion_member_id"]);
			$file->DeletionTimestamp = $values["file_deletion_timestamp"];
			$file->CheckoutUser = User::GetByID($values["file_checkout_member_id"]);
			$file->CheckoutTimestamp = $values["file_checkout_timestamp"];
			return $file;
		}
		public static function GetByID($id)
		{
			if ($id == null || !is_numeric($id)) return null;
			
			$query = "SELECT * FROM phpmmo_storage_files WHERE file_id = " . $id;
			$result = mysql_query($query);
			if (mysql_num_rows($result) > 0)
			{
				$values = mysql_fetch_assoc($result);
				return File::GetByAssoc($values);
			}
			return null;
		}
		
		public function GetByUser($user = null, $category = null, $max = null, $includeDeleted = false)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return null;
			
			$query = "SELECT * FROM phpmmo_storage_files WHERE file_creation_member_id = " . $user->ID;
			if ($category != null) $query .= " AND file_category_id = " . $category->ID;
			if (!$includeDeleted) $query .= " AND file_deletion_member_id IS NULL AND file_deletion_timestamp IS NULL";
			
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_array($result);
				$retval[] = File::GetByAssoc($values);
			}
			return $retval;
		}
		public function CountByUser($user = null, $category = null, $includeDeleted = false)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return 0;
			
			$query = "SELECT COUNT(*) FROM phpmmo_storage_files WHERE file_creation_member_id = " . $user->ID;
			if ($category != null) $query .= " AND file_category_id = " . $category->ID;
			if (!$includeDeleted) $query .= " AND file_deletion_member_id IS NULL AND file_deletion_timestamp IS NULL";
			
			$result = mysql_query($query);
			$values = mysql_fetch_array($result);
			if (is_numeric($values[0])) return $values[0];
			return 0;
		}
		
		public function Delete($user = null)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return false;
			
			if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/files/" . $this->ID)) return false;
			unlink($_SERVER["DOCUMENT_ROOT"] . "/files/" . $this->ID);
			
			$query = "UPDATE phpmmo_storage_files SET file_deletion_member_id = " . $user->ID . ", file_deletion_timestamp = NOW() WHERE file_id = " . $this->ID;
			$result = mysql_query($query);
			if (mysql_errno() != 0) return false;
			return true;
		}
		
		/// <summary>
		/// Creates a file entry on the database. Note that this does not actually place the file on the server,
		/// it's just a convenience method for the database API. 
		/// </summary>
		public static function Create($name, $description = null, $category = null, $user = null)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return null;
			
			$query = "INSERT INTO phpmmo_storage_files (file_name, file_description, file_category_id, file_creation_member_id, file_creation_timestamp) VALUES (" .
				"'" . mysql_real_escape_string($name) . "', " .
				($description == null ? "NULL" : ("'" . mysql_real_escape_string($description) . "'")) . ", " .
				($category == null ? "NULL" : $category->ID) . ", " .
				$user->ID . ", " .
				"NOW()" .
			")";
			
			$result = mysql_query($query);
			if (mysql_errno() != 0) return null;
			
			$query = "SELECT LAST_INSERT_ID();";
			$result = mysql_query($query);
			$values = mysql_fetch_array($result);
			$id = $values[0];
			
			if (mysql_errno() != 0) return null;
			
			if (!is_numeric($id)) return null;
			
			$value = File::GetByID($id);
			if (mysql_errno() != 0) return null;
			
			return $value;
		}
		
		public function GetModifications()
		{
			$query = "SELECT * FROM phpmmo_storage_file_history WHERE file_id = " . $this->ID;
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = FileModification::GetByAssoc($values);
			}
			return $retval;
		}
		public function GetTags()
		{
			$query = "SELECT * FROM phpmmo_storage_tags, phpmmo_storage_file_tags WHERE phpmmo_storage_file_tags.file_id = " . $this->ID . " AND phpmmo_storage_tags.tag_id = phpmmo_storage_file_tags.tag_id";
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = FileTag::GetByAssoc($values);
			}
			return $retval;
		}
	}
	class FileCategory
	{
		public $ID;
		public $Name;
		public $Title;
		
		public static function Get($max = null)
		{
			$query = "SELECT * FROM phpmmo_storage_categories";
			if ($max != null && is_numeric($max))
			{
				$query .= " LIMIT " . $max;
			}
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = FileCategory::GetByAssoc($values);
			}
			return $retval;
		}
		
		public static function GetByAssoc($values)
		{
			$category = new FileCategory();
			$category->ID = $values["category_id"];
			$category->Name = $values["category_name"];
			$category->Title = $values["category_title"];
			return $category;
		}
		public static function GetByID($id)
		{
			if ($id == null || !is_numeric($id)) return null;
			
			$query = "SELECT * FROM phpmmo_storage_categories WHERE category_id = " . $id;
			$result = mysql_query($query);
			$values = mysql_fetch_assoc($result);
			return FileCategory::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			if ($name == null) return null;
			
			$query = "SELECT * FROM phpmmo_storage_categories WHERE category_name = '" . mysql_real_escape_string($name) . "'";
			$result = mysql_query($query);
			$values = mysql_fetch_assoc($result);
			return FileCategory::GetByAssoc($values);
		}
		
		
		public function GetFilesByUser($user)
		{
			$query = "SELECT * FROM phpmmo_storage_files WHERE file_category_id = " . $this->ID . " AND file_creation_member_id = " . $user->ID . " AND file_deletion_member_id IS NULL AND file_deletion_timestamp IS NULL";
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = File::GetByAssoc($values);
			}
			return $retval;
		}
	}
	class FileModification
	{
		public $User;
		public $CheckoutTimestamp;
		public $CheckinTimestamp;
		public $Description;
		
		public static function GetByAssoc($values)
		{
			$modification = new FileModification();
			$modification->User = User::GetByID($values["modification_member_id"]);
			$modification->CheckoutTimestamp = $values["modification_checkout_timestamp"];
			$modification->CheckinTimestamp = $values["modification_checkin_timestamp"];
			$modification->Description = $values["modification_description"];
		}
	}
	class FileTag
	{
		public $ID;
		public $Name;
		public $Title;
		public $CreationUser;
		public $CreationTimestamp;
		
		public static function GetByAssoc($values)
		{
			$tag = new FileTag();
			$tag->ID = $values["tag_id"];
			$tag->Name = $values["tag_name"];
			$tag->Title = $values["tag_title"];
			$tag->CreationUser = User::GetByID($values["tag_creation_member_id"]);
			$tag->CreationTimestamp = $values["tag_creation_timestamp"];
			return $tag;
		}
	}
?>