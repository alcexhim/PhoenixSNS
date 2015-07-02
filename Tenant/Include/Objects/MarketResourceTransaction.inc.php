<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;

	class MarketResourceTransactionResource
	{
		public $Transaction;
		public $ResourceType;
		public $Amount;
		
		public static function Negate($resource)
		{
			$resource->Amount = !$resource->Amount;
			return $resource;
		}
		public static function NegateAll($resources)
		{
			foreach ($resources as $resource)
			{
				$resource->Amount = !$resource->Amount;
			}
			return $resources;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new MarketResourceTransactionResource();
			$item->Transaction = MarketResourceTransaction::GetByID($values["transactionresource_TransactionID"]);
			$item->ResourceType = MarketResourceType::GetByID($values["transactionresource_ResourceTypeID"]);
			$item->Amount = $values["transactionresource_Amount"];
			return $item;
		}
		
		public function __construct($resourceType = null, $amount = 0)
		{
			$this->Transaction = null;
			$this->ResourceType = $resourceType;
			$this->Amount = $amount;
		}
	}
	class MarketResourceTransaction
	{
		public $ID;
		public $SendingUser;
		public $ReceivingUser;
		
		public $Comments;
		
		public $CreationTimestamp;
		public $CreationUser;
		
		public static function GetInitialResources()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketInitialResources";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = new MarketResourceTransactionResource(MarketResourceType::GetByID($values["initialresource_ResourceTypeID"]), $values["initialresource_Amount"]);
			}
			return $retval;
		}
		
		public static function Create($resources, $receiver = null, $sender = null, $comments = null, $creationUser = null)
		{
			$CurrentUser = User::GetCurrent();
			if ($creationUser == null) $creationUser = $CurrentUser;
			
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactions (transaction_SendingUserID, transaction_ReceivingUserID, transaction_Comments, transaction_CreationTimestamp, transaction_CreationUserID) VALUES (" .
				($sender == null ? "NULL" : $sender->ID) . ", " .
				($receiver == null ? "NULL" : $receiver->ID) . ", " .
				"'" . $MySQL->real_escape_string($comments) . "', " .
				"NOW()" . ", " .
				($creationUser == null ? "NULL" : $creationUser->ID) .
			")";
			
			$MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			$transactionID = $MySQL->insert_id;
			
			foreach ($resources as $resource)
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactionResources (transactionresource_TransactionID, transactionresource_ResourceTypeID, transactionresource_Amount) VALUES (" .
					$transactionID . ", " .
					$resource->ResourceType->ID . ", " .
					$resource->Amount .
				")";
				
				$MySQL->query($query);
				if ($MySQL->errno != 0) return false;
			}
		}
		
		public static function GetByAssoc($values)
		{
			$item = new MarketResourceTransaction();
			$item->SendingUser = User::GetByID($values["transaction_SendingUserID"]);
			$item->ReceivingUser = User::GetByID($values["transaction_ReceivingUserID"]);
			$item->Comments = $values["transaction_Comments"];
			$item->CreationTimestamp = $values["transaction_CreationTimestamp"];
			$item->CreationUser = User::GetByID($values["transaction_CreationUserID"]);
			return $item;
		}
		
		public static function GetByUser($user, $type = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactions WHERE transaction_ReceivingUserID = " . $user->ID;
			if ($type != null) $query .= " AND transaction_ResourceTypeID = " . $type->ID;
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = MarketResourceTransaction::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetAmountByUser($user, $type)
		{
			global $MySQL;
			$query = "SELECT SUM(" . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactionResources.transactionresource_Amount) FROM " . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactions, " . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactionResources WHERE " . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactionResources.transactionresource_TransactionID = " . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactions.transaction_ID AND " . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactions.transaction_ReceivingUserID = " . $user->ID . " AND " . System::$Configuration["Database.TablePrefix"] . "UserResourceTransactionResources.transactionresource_ResourceTypeID = " . $type->ID;
			
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			$value = $values[0];
			if (!is_numeric($value)) return 0;
			return $value;
		}
	}
?>