<?php
	namespace PhoenixSNS\Objects;

	use Phast\System;
	use Phast\HorizontalAlignment;
	
	use Phast\Data\DataSystem;
	use PDO;
		
	class AvatarRenderer
	{
		public $Name;
		public $Base;
		public $View;
		
		public $Left;
		public $Top;
		public $ZoomFactor;
		public $HorizontalAlignment;
		
		public function __construct($name)
		{
			$this->Name = $name;
			$this->View = 1;
			$this->Base = AvatarBase::GetByID(2);
			$this->HorizontalAlignment = HorizontalAlignment::Center;
		}
		
		public static function GetByUser($user)
		{
			$ar = new AvatarRenderer("UserAvatar");
			return $ar;
		}
		
		public function Render()
		{
			$slices = $this->Base->GetSlices();
			echo("<div class=\"Avatar Avatar" . $this->Base->ID . "\" style=\"");
			if ($this->Left != null) echo("left: " . $this->Left . "px; ");
			if ($this->Top != null) echo("top: " . $this->Top . "px; ");
			if ($this->ZoomFactor != null)
			{
				echo("transform: scale(" . $this->ZoomFactor . "); ");
				echo("-webkit-transform: scale(" . $this->ZoomFactor . "); ");
				echo("-o-transform: scale(" . $this->ZoomFactor . "); ");
				echo("-ms-transform: scale(" . $this->ZoomFactor . "); ");
				echo("-moz-transform: scale(" . $this->ZoomFactor . "); ");
			}
			if ($this->HorizontalAlignment != null)
			{
				switch ($this->HorizontalAlignment)
				{
					case HorizontalAlignment::Left:
					{
						echo("text-align: left; ");
						break;
					}
					case HorizontalAlignment::Center:
					{
						echo("text-align: center; ");
						break;
					}
					case HorizontalAlignment::Right:
					{
						echo("text-align: right; ");
						break;
					}
					default:
					{
						echo("text-align: " . $this->HorizontalAlignment . "; ");
						break;
					}
				}
			}
			echo("\">");
			
			echo("<div class=\"chatbubble\" id=\"Avatar_" . $this->Name . "_chatbubble\">&nbsp;</div>");
			echo("<div class=\"error\" id=\"Avatar_" . $this->Name . "_error\" title=\"Please enable JavaScript to see Avatars\" style=\"background-image: url('" . System::ExpandRelativePath("~/images/avatar/bases/" . $this->Base->ID . "/" . $this->View . "/loading/error.png") . "');\" /></div>");
			echo("<div class=\"loading\" id=\"Avatar_" . $this->Name . "_loading\" style=\"background-image: url('" . System::ExpandRelativePath("~/images/avatar/bases/" . $this->Base->ID . "/" . $this->View . "/loading/outline.png") . "');\">");
			echo("<img class=\"fill\" id=\"Avatar_" . $this->Name . "_loading_fill\" src=\"" . System::ExpandRelativePath("~/images/avatar/bases/" . $this->Base->ID . "/" . $this->View . "/loading/fill.png") . "\" />");
			echo("</div>");
			echo("<div class=\"body\" id=\"Avatar_" . $this->Name . "_body\">");
			foreach ($slices as $slice)
			{
				$this->RenderRecursive($slice);
			}
			echo("</div>");
			
			echo("</div>");
			echo("<script type=\"text/javascript\">var " . $this->Name . " = new Avatar(\"" . $this->Name . "\", AvatarBase.GetByID(" . $this->Base->ID . "));</script>");
		}
		
		private function RenderRecursive($slice)
		{
			echo("<div class=\"" . $slice->Name . "\" id=\"Avatar_" . $this->Name . "_body_Slices_" . $slice->Name . "\">");
			$slices = $slice->GetSlices();
			if (count($slices) == 0)
			{
				echo("&nbsp;");
			}
			else
			{
				foreach ($slices as $slice1)
				{
					$this->RenderRecursive($slice1);
				}
			}
			echo("</div>");
		}
	}
	class AvatarBase
	{
		public $ID;
		public $Name;
		public $Creator;
		public $Width;
		public $Height;
		
		public static function GetByAssoc($values)
		{
			$retval = new AvatarBase();
			$retval->ID = $values["base_id"];
			$retval->Name = $values["base_name"];
			$retval->Width = $values["base_width"];
			$retval->Height = $values["base_height"];
			$retval->Creator = User::GetByID($values["base_creator_id"]);
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "AvatarBases WHERE base_ID = :base_ID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":base_id" => $id
			));
			$count = $statement->rowCount();
			if ($count < 1) return null;
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return AvatarBase::GetByAssoc($values);
		}
		public static function Get($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "AvatarBases";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			$count = $statement->rowCount();
			
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch(PDO::FETCH_ASSOC);
				$retval[] = AvatarBase::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function GetSlices($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "AvatarBaseSlices";
			$query .= " WHERE slice_BaseID = :slice_BaseID AND slice_ParentSliceID IS NULL";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":slice_BaseID" => $this->ID
			));
			$count = $statement->rowCount();
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch(PDO::FETCH_ASSOC);
				$retval[] = AvatarBaseSlice::GetByAssoc($values);
			}
			return $retval;
		}
	}
	class AvatarBaseSlice
	{
		public $Base;
		
		public $ID;
		public $Name;
		public $Title;
		public $Description;
		public $Left;
		public $Top;
		public $Width;
		public $Height;
		public $OriginLeft;
		public $OriginTop;
		
		public static function GetByAssoc($values)
		{
			$retval = new AvatarBaseSlice();
			$retval->Base = AvatarBase::GetByID($values["base_id"]);
			$retval->ID = $values["slice_id"];
			$retval->Name = $values["slice_name"];
			$retval->Title = $values["slice_title"];
			$retval->Description = $values["slice_description"];
			$retval->Left = $values["slice_left"];
			$retval->Top = $values["slice_top"];
			$retval->Width = $values["slice_width"];
			$retval->Height = $values["slice_height"];
			$retval->OriginLeft = $values["slice_origin_left"];
			$retval->OriginTop = $values["slice_origin_top"];
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "AvatarBaseSlices";
			$query .= " WHERE slice_ID = :slice_ID";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":slice_ID" => $id
			));
			$values = $result->fetch(PDO::FETCH_ASSOC);
			if ($result->num_rows < 1) return null;
			return AvatarBaseSlice::GetByAssoc($values);
		}
		
		public function GetSlices($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "AvatarBaseSlices";
			$query .= " WHERE slice_BaseID = :slice_BaseID AND slice_ParentSliceID = :slice_ParentSliceID";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$statement = $pdo->prepare($query);
			$result = $pdo->execute(array
			(
				":slice_BaseID" => $this->Base->ID,
				":slice_ParentSliceID" => $this->ID
			));
			$count = $statement->rowCount();
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch(PDO::FETCH_ASSOC);
				$retval[] = AvatarBaseSlice::GetByAssoc($values);
			}
			return $retval;
		}
	}
?>