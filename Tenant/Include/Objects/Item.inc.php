<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	class Item
	{
		public $ID;
		public $Name;
		public $Title;
		public $Category;
		public $Description;
		public $ZIndex;
		public $Images;
		
		public function ToJSON()
		{
			$json = "{";
			$json .= "\"ID\": " . $this->ID . ", ";
			$json .= "\"Name\": \"" . \JH\Utilities::JavaScriptEncode($this->Name) . "\", ";
			$json .= "\"Title\": \"" . \JH\Utilities::JavaScriptEncode($this->Title) . "\", ";
			$json .= "\"Category\": " . $this->Category->ToJSON() . ", ";
			$json .= "\"Description\": \"" . \JH\Utilities::JavaScriptEncode($this->Description) . "\", ";
			$json .= "}";
			return $json;
		}
		
		public function CountPurchased($byUser = null)
		{
			global $MySQL;
			$query = "SELECT COUNT(item_id) FROM " . System::$Configuration["Database.TablePrefix"] . "UserInventoryItems WHERE inventoryitem_ItemID = " . $this->ID;
			if ($byUser != null)
			{
				$query .= " AND inventoryitem_UserID = " . $byUser->ID;
			}
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			
			if (is_numeric($values[0])) return $values[0];
			return 0;
		}
		
		public function GetMarketEntry()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketItems WHERE marketitem_ItemID = " . $this->ID;
			$result = $MySQL->query($query);
			if (!$result) return null;
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			$retval = ItemMarketEntry::GetByAssoc($values);
			return $retval;
		}
		
		public function AddImage($image_mimetype, $image_left = 0, $image_top = 0, $image_width = null, $image_height = null, $image_zindex = null)
		{
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "ItemImages (itemimage_ItemID, itemimage_MimeType, itemimage_Left, itemimage_Top, itemimage_Width, itemimage_Height, itemimage_ZIndex) VALUES (" .
			$this->ID . ", " .
			"'" . $MySQL->real_escape_string($image_mimetype) . "', " .
			$image_left . ", " .
			$image_top . ", " .
			($image_width == null ? 'NULL' : $image_width) . ", " .
			($image_height == null ? 'NULL' : $image_height) . ", " .
			($image_zindex == null ? 'NULL' : $image_zindex) .
			")";
			
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return null;
			
			$query = "SELECT LAST_INSERT_ID()";
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			$id = $values[0];
			
			$image = new ItemImage();
			$image->ID = $id;
			$image->FileNameExtension = $image_mimetype;
			$image->Left = $image_left;
			$image->Top = $image_top;
			$image->Width = $image_width;
			$image->Height = $image_height;
			$image->ZIndex = $image_zindex;
			return $image;
		}
		
		public static function Create($item_name, $item_title, $item_description, $item_category = null)
		{
			global $MySQL;
			$CurrentUser = User::GetCurrent();
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "Items (item_Name, item_Title, item_Description, item_CategoryID, item_CreationUserID, item_CreationTimestamp) VALUES (" .
			"'" . $MySQL->real_escape_string($item_name) . "', " .
			"'" . $MySQL->real_escape_string($item_title) . "', " .
			"'" . $MySQL->real_escape_string($item_description) . "', " .
			($item_category == null ? "NULL" : $item_category->ID) . ", " .
			$CurrentUser->ID . ", " .
			"NOW()" .
			")";
			
			$result = $MySQL->query($query);
			
			if ($MySQL->errno != 0) return null;
			
			$query = "SELECT LAST_INSERT_ID()";
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			$id = $values[0];
			
			return Item::GetByID($id);
		}
		
		public static function GetByAssoc($values)
		{
			global $MySQL;
			$item = new Item();
			$item->ID = $values["item_ID"];
			$item->Name = $values["item_Name"];
			$item->Title = $values["item_Title"];
			$item->Description = $values["item_Description"];
			$item->Category = ItemCategory::GetByID($values["item_CategoryID"]);
			$item->Images = array();
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "ItemImages WHERE itemimage_ItemID = " . $item->ID;
			$query .= " ORDER BY itemimage_ZIndex";
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values1 = $result->fetch_assoc();
				
				$image = new ItemImage();
				$image->ID = $values1["itemimage_ID"];
				$image->Slice = AvatarBaseSlice::GetByID($values1["itemimage_SliceID"]);
				$image->MimeType = $values1["itemimage_MimeType"];
				$image->Left = $values1["itemimage_Left"];
				$image->Top = $values1["itemimage_Top"];
				$image->Width = $values1["itemimage_Width"];
				$image->Height = $values1["itemimage_Height"];
				$image->ZIndex = $values1["itemimage_ZIndex"];
				$item->Images[] = $image;
			}
			
			return $item;
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Items";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Item::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByID($id)
		{
			if ($id == null || !is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Items WHERE item_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return Item::GetByAssoc($values);
			}
			return null;
		}
		public static function GetByName($name)
		{
			if ($name == null) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Items WHERE item_Name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			if ($result->num_rows > 0)
			{	
				$values = $result->fetch_assoc();
				return Item::GetByAssoc($values);
			}
			return null;
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return Item::GetByID($idOrName);
			return Item::GetByName($idOrName);
		}
		public static function GetByUser($user = null, $max = null)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return array();
			
			global $MySQL;
			$items = array();
			$query = "SELECT " . System::$Configuration["Database.TablePrefix"] . "Items.* FROM " . System::$Configuration["Database.TablePrefix"] . "Items, " . System::$Configuration["Database.TablePrefix"] . "UserInventoryItems WHERE " . System::$Configuration["Database.TablePrefix"] . "UserInventoryItems.inventoryitem_ID = " . System::$Configuration["Database.TablePrefix"] . "Items.item_ID AND " . System::$Configuration["Database.TablePrefix"] . "UserInventoryItems.UserID = " . $user->ID;
			$query .= " ORDER BY " . System::$Configuration["Database.TablePrefix"] . "Items.item_ZIndex ASC";
			
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$items[] = Item::GetByAssoc($values);
			}
			return $items;
		}
		public static function GetEquippedByUser($user)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Items, " . System::$Configuration["Database.TablePrefix"] . "UserEquippedItems WHERE " . System::$Configuration["Database.TablePrefix"] . "UserEquippedItems.equippeditem_UserID = " . $user->ID . " AND " . System::$Configuration["Database.TablePrefix"] . "UserEquippedItems.equippeditem_ItemID = " . System::$Configuration["Database.TablePrefix"] . "Items.item_ID";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Item::GetByAssoc($values);
			}
			return $retval;
		}
		public static function CountEquippedByUser($user, $item = null)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return false;
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "UserEquippedItems WHERE " . System::$Configuration["Database.TablePrefix"] . "UserEquippedItems.equippeditem_UserID = " . $user->ID;
			if ($item != null)
			{
				$query .= " AND " . System::$Configuration["Database.TablePrefix"] . "UserEquippedItems.equippeditem_ItemID = " . $item->ID;
			}
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if (is_numeric($values[0])) return $values[0];
			return 0;
		}
		
		public function IsEquipped($user = null)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return false;
			return (Item::CountEquippedByUser($user, $this) != 0);
		}
		public function Equip($user = null)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return false;
			
			if (Item::IsEquipped($user)) return true;
			if (!$user->HasInventoryItem($this)) return false;
			
			// if user has any items with the same category equipped, unequip them
			$equippedItems = Item::GetEquippedByUser($user);
			foreach ($equippedItems as $equippedItem)
			{
				if ($equippedItem->Category->ID == $this->Category->ID)
				{
					$equippedItem->Unequip();
				}
			}
			
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "UserEquippedItems (equippeditem_UserID, equippeditem_ItemID) VALUES (" . $user->ID . ", " . $this->ID . ");";
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		public function Unequip($user = null)
		{
			if ($user == null) $user = User::GetCurrent();
			if ($user == null) return false;
			if (!$user->HasInventoryItem($this)) return false;
			
			global $MySQL;
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "UserEquippedItems WHERE equippeditem_UserID = " . $user->ID . " AND equippeditem_ItemID = " . $this->ID;
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		
		public function GiveToUser($user)
		{
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "UserInventoryItems (inventoryitem_UserID, inventoryitem_ItemID) VALUES (" . $user->ID . ", " . $this->ID . ");";
			$result = $MySQL->query($query);
			return ($MySQL->errno == 0);
		}
		
		public static function CountByUser($user)
		{
			global $MySQL;
			if ($user == null) return 0;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "UserInventoryItems WHERE inventoryitem_UserID = " . $user->ID;
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			return $values[0];
		}
	}
	class ItemResource
	{
		public $Amount;
		public $ResourceType;
	}
	class ItemCategory
	{
		public $ID;
		public $Name;
		public $Title;
		
		public function ToJSON()
		{
			$json = "{";
			$json .= "\"ID\": " . $this->ID . ",";
			$json .= "\"Name\": \"" . \JH\Utilities::JavaScriptEncode($this->Name) . "\",";
			$json .= "\"Title\": \"" . \JH\Utilities::JavaScriptEncode($this->Title) . "\"";
			$json .= "}";
			return $json;
		}
		
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "ItemCategories";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = ItemCategory::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByAssoc($values)
		{
			$category = new ItemCategory();
			$category->ID = $values["category_ID"];
			$category->Name = $values["category_Name"];
			$category->Title = $values["category_Title"];
			return $category;
		}
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "ItemCategories WHERE category_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return ItemCategory::GetByAssoc($values);
			}
			return null;
		}
	}
	class ItemImage
	{
		public $ID;
		public $Slice;
		public $FileNameExtension;
		public $Top;
		public $Left;
		public $Width;
		public $Height;
		public $ZIndex;
	}
	class ItemMarketEntry
	{
		public $Item;
		
		public $BeginTimestamp;
		public $EndTimestamp;
		
		public function GetRequiredResources()
		{
			if ($this->Item == null) return array();
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketItemResources WHERE marketitemresource_ItemID = " . $this->Item->ID;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if (!$result) return array();
			
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = MarketResourceReference::GetByAssoc($values);
			}
			return $retval;
		}
		
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketItems WHERE (marketitem_BeginTimestamp IS NOT NULL AND marketitem_EndTimestamp <= NOW()) AND (marketitem_EndTimestamp IS NULL OR marketitem_EndTimestamp >= NOW()) ORDER BY marketitem_BeginTimestamp ASC";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = ItemMarketEntry::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByAssoc($values)
		{
			$entry = new ItemMarketEntry();
			$entry->Item = Item::GetByID($values["marketitem_ItemID"]);
			$entry->BeginTimestamp = $values["marketitem_BeginTimestamp"];
			$entry->EndTimestamp = $values["marketitem_EndTimestamp"];
			return $entry;
		}
	}
?>