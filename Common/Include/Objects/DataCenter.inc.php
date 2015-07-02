<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	/**
	 * Provides a collection of DataCenters
	 * @author Michael Becker
	 */
	class DataCenterCollection
	{
		/**
		 * The items stored in this collection
		 * @var DataCenter[]
		 */
		public $Items;
		public function __construct()
		{
			$this->Items = array();
		}
		
		public function Add($item)
		{
			$this->Items[] = $item;
		}
		public function Contains($item)
		{
			foreach ($this->Items as $itm)
			{
				if ($itm->ID == $item->ID) return true;
			}
			return false;
		}
		public function Get($item)
		{
			foreach ($this->Items as $itm)
			{
				if ($itm->ID == $item->ID) return $item;
			}
			return null;
		}
	}
	/**
	 * Represents a server on which multiple Tenants can be hosted.
	 * @author Michael Becker
	 */
	class DataCenter
	{
		/**
		 * The unique, incremental ID number of this DataType.
		 * @var int
		 */
		public $ID;
		/**
		 * The title of this DataCenter
		 * @var string
		 */
		public $Title;
		/**
		 * A short description of this DataCenter
		 * @var string
		 */
		public $Description;
		/**
		 * The host name associated with this DataCenter
		 * @var string
		 */
		public $HostName;

		/**
		 * Creates a new DataCenter with the given details
		 * @param string $title The title of the DataCenter to create
		 * @param string $hostname The host name or IP address of the DataCenter
		 * @param string $description A short description of the DataCenter
		 * @return \PhoenixSNS\Objects\DataCenter|NULL The newly-created DataCenter object with the given details, or NULL if the creation operation failed.
		 */
		public static function Create($title, $hostname, $description = null)
		{
			$item = new DataCenter();
			$item->Title = $title;
			$item->HostName = $hostname;
			$item->Description = $description;
			if ($item->Update())
			{
				return $item;
			}
			return null;
		}
		/**
		 * Counts all of the available DataCenters.
		 * @return int The number of DataCenters available on this server.
		 */
		public static function Count()
		{
			global $MySQL;
			$query = "SELECT COUNT(datacenter_ID) FROM " . System::$Configuration["Database.TablePrefix"] . "DataCenters";
			$result = $MySQL->query($query);
			if ($result === false) return 0;
			$values = $result->fetch_array();
			return $values[0];
		}
		/**
		 * Creates a new DataCenter object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\DataCenter based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new DataCenter();
			$item->ID = $values["datacenter_ID"];
			$item->Title = $values["datacenter_Title"];
			$item->Description = $values["datacenter_Description"];
			$item->HostName = $values["datacenter_HostName"];
			return $item;
		}
		/**
		 * Retrieves all DataCenters
		 * @param int $max The maximum number of DataCenters to return
		 * @return \PhoenixSNS\Objects\DataCenter[]
		 */
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "DataCenters";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = DataCenter::GetByAssoc($values);
			}
			return $retval;
		}

		/**
		 * Retrieves a single DataCenter with the given ID.
		 * @param int $id The ID of the DataCenter to return
		 * @return NULL|\PhoenixSNS\Objects\DataCenter The DataCenter with the given ID, or null if no DataCenter with the given ID was found or the given ID was invalid
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "DataCenters WHERE datacenter_ID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return DataCenter::GetByAssoc($values);
		}

		/**
		 * Updates the server with the information in this object.
		 * @return boolean True if the update succeeded; false if an error occurred.
		 */
		public function Update()
		{
			global $MySQL;
			if ($this->ID != null)
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "DataCenters SET ";
				$query .= "datacenter_Title = '" . $MySQL->real_escape_string($this->Title) . "', ";
				$query .= "datacenter_Description = '" . $MySQL->real_escape_string($this->Description) . "', ";
				$query .= "datacenter_HostName = '" . $MySQL->real_escape_string($this->HostName) . "'";
				$query .= " WHERE datacenter_ID = " . $this->ID;
			}
			else
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "DataCenters (datacenter_Title, datacenter_Description, datacenter_HostName) VALUES (";
				$query .= "'" . $MySQL->real_escape_string($this->Title) . "', ";
				$query .= "'" . $MySQL->real_escape_string($this->Description) . "', ";
				$query .= "'" . $MySQL->real_escape_string($this->HostName) . "'";
				$query .= ")";
			}
			$result = $MySQL->query($query);
			if ($this->ID == null)
			{
				$this->ID = $MySQL->insert_id;
			}
			return ($MySQL->errno == 0);
		}
		
		/**
		 * Deletes this DataCenter from the server.
		 * @return boolean True if the delete operation succeeded; false if the delete operation failed.
		 */
		public function Delete()
		{
			global $MySQL;
			if ($this->ID == null) return false;
			
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "DataCenters WHERE datacenter_ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			return true;
		}

		/**
		 * Gets the JSON representation of this object for use in AJAX calls.
		 * @return string The JSON representation of this object.
		 */
		public function ToJSON()
		{
			$json = "";
			$json .= "{";
			$json .= "\"ID\":" . $this->ID . ",";
			$json .= "\"Title\":\"" . \JH\Utilities::JavaScriptEncode($this->Title, "\"") . "\",";
			$json .= "\"Description\":\"" . \JH\Utilities::JavaScriptEncode($this->Description, "\"") . "\",";
			$json .= "\"HostName\":\"" . \JH\Utilities::JavaScriptEncode($this->HostName, "\"") . "\"";
			$json .= "}";
			return $json;
		}
	}
?>