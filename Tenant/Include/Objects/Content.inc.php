<?php
	namespace PhoenixSNS\Objects;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	use PDO;
		
	class Content
	{
		public $Language;
		public $Path;
		public $Title;
		public $Content;
		public $DateCreated;
		
		public static function Create($path, $title, $content)
		{
			$pdo = DataSystem::GetPDO();
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Contents (content_Path, content_Title, content_Content, content_CreationUserID, content_CreationTimestamp) VALUES (" .
				":content_Path, :content_Title, :content_Content, :content_CreationUserID, NOW())";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":content_Path" => $path,
				":content_Title" => $title,
				":content_Content" => $content,
				":content_CreationUserID" => User::GetCurrent()->ID
			));
			return ($result !== false);
		}
		public static function GetByAssoc($values)
		{
			if ($values == null) return null;
			$content = new Content();
			$content->Language = Language::GetByID($values["content_language_id"]);
			$content->Path = $values["content_path"];
			$content->Title = $values["content_title"];
			$content->Content = $values["content_content"];
			$content->Creator = User::GetByID($values["content_creator_id"]);
			$content->DateCreated = $values["content_creator_timestamp"];
			return $content;
		}
		public static function GetByPath($path)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Contents WHERE content_Path = :content_Path AND content_LanguageID = :content_LanguageID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":content_Path" => $path,
				":content_LanguageID" => Language::GetCurrent()->ID
			));
			if ($statement->rowCount() < 1) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Content::GetByAssoc($values);
		}
		public static function Enumerate($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Contents";
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			$count = $statement->rowCount();
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = Content::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function Update()
		{
			$pdo = DataSystem::GetPDO();
			$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Contents SET content_Path = :content_Path, content_Title = :content_Title, content_Content = :content_Content WHERE content_Path = :content_Path AND content_LanguageID = :content_LanguageID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":content_Path" => $this->Path,
				":content_Title" => $this->Title,
				":content_Content" => $this->Content,
				":content_LanguageID" => $this->Language->ID
			));
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function Delete()
		{
			$pdo = DataSystem::GetPDO();
			$query = "DELETE FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Contents WHERE content_Path = :content_Path AND content_LanguageID = :content_LanguageID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":content_Path" => $this->Path,
				":content_LanguageID" => $this->Language->ID
			));
			return ($result !== false);
		}
	}
?>