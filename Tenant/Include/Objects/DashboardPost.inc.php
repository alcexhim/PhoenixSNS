<?php
	namespace PhoenixSNS\Objects;
	use Phast\System;
	use Phast\Data\DataSystem;
	use PDO;
		
	class DashboardPost
	{
		public $ID;
		public $TargetUser;
		public $Title;
		public $Content;
		public $Type;
		public $CreationUser;
		public $CreationTimestamp;
		
		public $AllowLike;
		public $AllowComment;
		public $AllowShare;
		
		public static function GetByAssoc($values)
		{
			$item = new DashboardPost();
			$item->ID = $values["post_ID"];
			$item->TargetUser = User::GetByID($values["post_TargetUserID"]);
			$item->Title = $values["post_Title"];
			$item->Content = $values["post_Content"];
			$item->Type = PostType::GetByID($values["post_PostTypeID"]);
			$item->CreationUser = User::GetByID($values["post_CreationUserID"]);
			$item->CreationTimestamp = $values["post_CreationTimestamp"];
			
			$item->AllowLike = ($values["post_AllowLike"] == 1);
			$item->AllowComment = ($values["post_AllowComment"] == 1);
			$item->AllowShare = ($values["post_AllowShare"] == 1);
			return $item;
		}
		public static function Get($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPosts";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$query .= " ORDER BY post_CreationTimestamp DESC";
			
			$retval = array();
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			if ($result === false) return $retval;
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = DashboardPost::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function GetActions($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostActions";
			$query .= " WHERE action_PostID = :action_PostID";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$statement = $pdo->prepare($query);
			
			$retval = array();
			$result = $statement->execute(array
			(
				":action_PostID" => $this->ID
			));
			if ($result === false) return $retval;
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = DashboardPostAction::GetByAssoc($values);
			}
			return $retval;
		}
		public function GetComments($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostComments";
			$query .= " WHERE comment_PostID = :comment_PostID";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$retval = array();
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":comment_PostID" => $this->ID
			));
			if ($result === false) return $retval;
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = DashboardPostComment::GetByAssoc($values);
			}
			return $retval;
		}
		public function GetShares($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostShares";
			$query .= " WHERE share_PostID = :share_PostID";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$retval = array();
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":share_PostID" => $this->ID
			));
			if ($result === false) return $retval;
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = DashboardPostShare::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function CountComments()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostComments";
			$query .= " WHERE comment_PostID = :comment_PostID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":comment_PostID" => $this->ID
			));
			$retval = 0;
			if ($result !== false)
			{
				$values = $statement->fetch(PDO::FETCH_NUM);
				$retval = $values[0];
			}
			return $retval;
		}
		public function CountShares()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostShares";
			$query .= " WHERE share_PostID = :share_PostID";
			$statement = $pdo->prepare($query);
			$retval = array();
			$result = $statement->execute(array
			(
				":share_PostID" => $this->ID
			));
			$retval = 0;
			if ($result !== false)
			{
				$values = $statement->fetch(PDO::FETCH_NUM);
				$retval = $values[0];
			}
			return $retval;
		}
		
		public function GetImpressions($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostImpressions";
			$query .= " WHERE impression_PostID = :impression_PostID";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$retval = array();
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":impression_PostID" => $this->ID
			));
			if ($result === false) return $retval;
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = DashboardPostImpression::GetByAssoc($values);
			}
			return $retval;
		}
	}
	class DashboardPostAction
	{
		public $ID;
		public $Post;
		public $Title;
		public $URL;
		public $Script;
		
		public static function GetByAssoc($values)
		{
			$item = new DashboardPostAction();
			$item->ID = $values["action_ID"];
			$item->Post = DashboardPost::GetByID($values["action_PostID"]);
			$item->Title = $values["action_Title"];
			$item->URL = $values["action_URL"];
			$item->Script = $values["action_Script"];
			return $item;
		}
		public static function Get($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostActions";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$retval = array();
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			if ($result === false) return $retval;
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = DashboardPostAction::GetByAssoc($values);
			}
			return $retval;
		}
	}
	class DashboardPostComment
	{
		public $ID;
		public $Post;
		public $ParentComment;
		public $Title;
		public $Content;
		public $CreationUser;
		public $CreationTimestamp;
		
		public static function GetByAssoc($values)
		{
			$item = new DashboardPostComment();
			$item->ID = $values["comment_ID"];
			$item->Post = DashboardPost::GetByID($values["comment_PostID"]);
			$item->ParentComment = DashboardPostComment::GetByID($values["comment_ParentCommentID"]);
			$item->Title = $values["comment_Title"];
			$item->Content = $values["comment_Content"];
			$item->CreationUser = User::GetByID($values["comment_CreationUserID"]);
			$item->CreationTimestamp = $values["comment_CreationTimestamp"];
			return $item;
		}
		public static function Get($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostComments";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$retval = array();
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			if ($result === false) return $retval;
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = DashboardPostComment::GetByAssoc($values);
			}
			return $retval;
		}
	}
	class DashboardPostShare
	{
		public $Post;
		public $TargetUser;
		public $CreationUser;
		public $CreationTimestamp;
		
		public static function GetByAssoc($values)
		{
			$item = new DashboardPostShare();
			$item->Post = DashboardPost::GetByID($values["share_PostID"]);
			$item->TargetUser = User::GetByID($values["share_TargetUserID"]);
			$item->CreationUser = User::GetByID($values["share_CreationUserID"]);
			$item->CreationTimestamp = $values["share_CreationTimestamp"];
			return $item;
		}
		public static function Get($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostShares";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$retval = array();
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			if ($result === false) return $retval;
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = DashboardPostShare::GetByAssoc($values);
			}
			return $retval;
		}
	}
	class DashboardPostImpression
	{
		public $Post;
		public $CreationUser;
		public $CreationTimestamp;
		
		public static function GetByAssoc($values)
		{
			$item = new DashboardPostImpression();
			$item->Post = DashboardPost::GetByID($values["share_PostID"]);
			$item->CreationUser = User::GetByID($values["share_CreationUserID"]);
			$item->CreationTimestamp = $values["share_CreationTimestamp"];
			return $item;
		}
		public static function Get($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DashboardPostImpressions";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$retval = array();
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			if ($result === false) return $retval;
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = DashboardPostImpression::GetByAssoc($values);
			}
			return $retval;
		}
	}
?>