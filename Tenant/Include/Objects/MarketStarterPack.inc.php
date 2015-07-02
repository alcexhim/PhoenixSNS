<?php
	namespace PhoenixSNS\Objects;
	
	class MarketStarterPack
	{
		public $ID;
		public $Title;
		public $CreationUser;
		public $CreationTimestamp;
	
		public static function GetByAssoc($values)
		{
			$pack = new MarketStarterPack();
			$pack->ID = $values["starterpack_ID"];
			$pack->Title = $values["starterpack_Title"];
			$pack->CreationUserID = User::GetByID($values["starterpack_CreationUserID"]);
			$pack->CreationTimestamp = $values["starterpack_CreationTimestamp"];
			return $pack;
		}
		
		public static function Get()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketStarterPacks";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = MarketStarterPack::GetByAssoc($values);
			}
			return $retval;
		}
	
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketStarterPacks WHERE starterpack_ID = " . $id;
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return MarketStarterPack::GetByAssoc($values);
		}
	
		public function GetItems()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketStarterPackItems WHERE " . System::$Configuration["Database.TablePrefix"] . "MarketStarterPackItems.starterpackitem_StarterPackID = " . $this->ID;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$item = Item::GetByID($values["starterpackitem_ItemID"]);
				if ($item == null) continue;
				$retval[] = $item;
			}
			return $retval;
		}
		
		public function ApplyToUser($user)
		{
			if ($user == null) return false;
			$items = $this->GetItems();
			foreach ($items as $item)
			{
				if (!$item->GiveToUser($user)) return false;
				if (!$item->Equip($user)) return false;
			}
			return true;
		}
	}
?>