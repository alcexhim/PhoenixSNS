<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	use WebFX\Enumeration;
	
	/**
	 * Provides an enumeration for determining the status of a Tenant.
	 * @author Michael Becker
	 */
	abstract class TenantStatus extends Enumeration
	{
		/**
		 * The tenant is disabled.
		 * @var int
		 */
		const Disabled = 0;
		/**
		 * The tenant is enabled.
		 * @var int
		 */
		const Enabled = 1;
	}
	
	/**
	 * Represents a Tenant, a completely isolated set of objects, properties, and other elements that can reside
	 * alongside other Tenants.
	 * @author Michael Becker
	 */
	class Tenant
	{
		/**
		 * The unique, incremental ID number of this Tenant.
		 * @var int
		 */
		public $ID;
		/**
		 * The URL of this Tenant.
		 * @var string
		 */
		public $URL;
		/**
		 * A short description of this Tenant.
		 * @var string
		 */
		public $Description;
		/**
		 * The status of this Tenant (Disabled or Enabled)
		 * @var TenantStatus
		 */
		public $Status;
		/**
		 * The TenantType associated with this Tenant.
		 * @var TenantType
		 */
		public $Type;
		/**
		 * A DataCenterCollection holding the DataCenter(s) associated with this Tenant.
		 * @var DataCenterCollection
		 */
		public $DataCenters;
		/**
		 * The PaymentPlan associated with this Tenant.
		 * @var PaymentPlan
		 */
		public $PaymentPlan;
		/**
		 * The date and time upon which access to this Tenant is granted.
		 * @var unknown
		 */
		public $BeginTimestamp;
		/**
		 * The date and time upon which access to this Tenant is revoked.
		 * @var unknown
		 */
		public $EndTimestamp;
		
		/**
		 * Creates a new Tenant object and initializes the DataCenterCollection.
		 */
		public function __construct()
		{
			$this->DataCenters = new DataCenterCollection();
		}
		
		/**
		 * Determines whether this Tenant is disabled, either explicitly (via TenantStatus) or implicitly (via
		 * Begin/EndTimestamp). 
		 * @return boolean True if this Tenant should be treated as disabled; false otherwise.
		 */
		public function IsExpired()
		{
			if ($this->Status == TenantStatus::Disabled) return true;
			
			$date = date_create();
			if ($this->BeginTimestamp == null)
			{
				$dateBegin = null;
			}
			else
			{
				$dateBegin = date_create($this->BeginTimestamp);
			}
			if ($this->EndTimestamp == null)
			{
				$dateEnd = null;
			}
			else
			{
				$dateEnd = date_create($this->EndTimestamp);
			}
			
			return (!(($dateBegin == null || $dateBegin <= $date) && ($dateEnd == null || $dateEnd >= $date)));
		}
		/**
		 * Determines whether a Tenant with the specified URL exists on this server.
		 * @param string $url The URL to search for.
		 * @return boolean True if a Tenant with the specified URL exists and is considered enabled; false otherwise.
		 */
		public static function Exists($url)
		{
			global $MySQL;
			$query = "SELECT COUNT(tenant_ID) FROM " . System::$Configuration["Database.TablePrefix"] . "Tenants WHERE tenant_URL = '" . $MySQL->real_escape_string($url) . "'";
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$values = $result->fetch_array();
			return ($values[0] > 0);
		}
		/**
		 * Counts the Tenants that match the given criteria.
		 * @param boolean $includeActive True to include active tenants in the results; false to exclude active tenants.
		 * @param boolean $includeInactive True to include inactive tenants in the results; false to exclude inactive tenants.
		 * @return int The number of Tenants which match the given criteria.
		 */
		public static function Count($includeActive = true, $includeInactive = true)
		{
			$tenants = Tenant::Get();
			$count = 0;
			foreach ($tenants as $tenant)
			{
				if (($includeActive && !$tenant->IsExpired()) || ($tenant->IsExpired() && $includeInactive))
					$count++;
			}
			return $count;
		}
		/**
		 * Creates a Tenant on the server with the given parameters.
		 * @param string $url The URL of the Tenant to create.
		 * @param string $description A short description of the Tenant being created.
		 * @param TenantStatus $status The status of the Tenant.
		 * @param TenantType $type The TenantType associated with the Tenant.
		 * @param PaymentPlan $paymentPlan The PaymentPlan associated with the Tenant.
		 * @param mixed $beginTimestamp The date and time upon which the Tenant begins to be operational.
		 * @param mixed $endTimestamp The date and time upon which the Tenant ceases to be operational.
		 * @param array $dataCenters An array of DataCenters to associate with the Tenant.
		 * @return \PhoenixSNS\Objects\Tenant|NULL
		 */
		public static function Create($url, $description = null, $status = TenantStatus::Enabled, TenantType $type = null, PaymentPlan $paymentPlan = null, $beginTimestamp = null, $endTimestamp = null, array $dataCenters = null)
		{
			$item = new Tenant();
			$item->URL = $url;
			$item->Description = $description;
			$item->Status = $status;
			$item->Type = $type;
			$item->PaymentPlan = $paymentPlan;
			$item->BeginTimestamp = $beginTimestamp;
			$item->EndTimestamp = $endTimestamp;
			
			if ($dataCenters == null) $dataCenters = array();
			foreach ($dataCenters as $datacenter)
			{
				$item->DataCenters->Add($datacenter);
			}
			
			if ($item->Update())
			{
				return $item;
			}
			return null;
		}
		/**
		 * Creates a new Tenant object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\Tenant based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new Tenant();
			$item->ID = $values["tenant_ID"];
			$item->URL = $values["tenant_URL"];
			$item->Description = $values["tenant_Description"];
			switch ($values["tenant_Status"])
			{
				case 1:
				{
					$item->Status = TenantStatus::Enabled;
					break;
				}
				case 0:
				{
					$item->Status = TenantStatus::Disabled;
					break;
				}
			}
			$item->Type = TenantType::GetByID($values["tenant_TypeID"]);
			$item->PaymentPlan = PaymentPlan::GetByID($values["tenant_PaymentPlanID"]);
			$item->BeginTimestamp = $values["tenant_BeginTimestamp"];
			$item->EndTimestamp = $values["tenant_EndTimestamp"];
			
			
			// get the data centers associated with this tenant
			global $MySQL;
			$query = "SELECT " . System::$Configuration["Database.TablePrefix"] . "DataCenters.* FROM " . System::$Configuration["Database.TablePrefix"] . "DataCenters, " . System::$Configuration["Database.TablePrefix"] . "TenantDataCenters WHERE " . System::$Configuration["Database.TablePrefix"] . "TenantDataCenters.tdc_TenantID = " . $item->ID . " AND " . System::$Configuration["Database.TablePrefix"] . "TenantDataCenters.tdc_DataCenterID = " . System::$Configuration["Database.TablePrefix"] . "DataCenters.datacenter_ID";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = DataCenter::GetByAssoc($values);
			}
			$item->DataCenters->Items = $retval;
			
			return $item;
		}
		/**
		 * Retrieves all Tenants
		 * @param int $max The maximum number of Tenants to return
		 * @return \PhoenixSNS\Objects\Tenant[]
		 */
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Tenants";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Tenant::GetByAssoc($values);
			}
			return $retval;
		}

		/**
		 * Retrieves a single Tenant with the given ID.
		 * @param int $id The ID of the Tenant to return
		 * @return NULL|\PhoenixSNS\Objects\Tenant The Tenant with the given ID, or null if no Tenant with the given ID was found or the given ID was invalid
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Tenants WHERE tenant_ID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return Tenant::GetByAssoc($values);
		}
		
		/**
		 * Retrieves a single Tenant with the given URL.
		 * @param string $url The URL of the Tenant to return
		 * @return NULL|\PhoenixSNS\Objects\Tenant The Tenant with the given URL, or null if no Tenant with the given URL was found
		 */
		public static function GetByURL($url)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Tenants WHERE tenant_URL = '" . $MySQL->real_escape_string($url) . "'";
			$result = $MySQL->query($query);
			if ($result === false)
			{
				echo("<html><head><title>Initialization Failure</title></head><body><h1>Initialization Failure</h1><p>A fatal error occurred when attempting to initialize the PhoenixSNS runtime.  Please make sure PhoenixSNS has been installed correctly on the server.</p><p>The PhoenixSNS runtime cannot be loaded (1001). Please contact the Web site administrator to inform them of this problem.</p><hr /><h3>System information</h3><table><tr><td>Tenant:</td><td>" . $url . "</td></tr><tr><td>Server: </td><td>" . $_SERVER["HTTP_HOST"] . "</td></tr></table></body></html>");
				die();
				return null;
			}
			
			$count = $result->num_rows;
			if ($count == 0)
			{
				echo("<html><head><title>Initialization Failure</title></head><body><h1>Initialization Failure</h1><p>A fatal error occurred when attempting to initialize the PhoenixSNS runtime.  Please make sure PhoenixSNS has been installed correctly on the server.</p><p>The PhoenixSNS runtime cannot find the requested tenant (1002). Please contact the Web site administrator to inform them of this problem.</p><hr /><h3>System information</h3><table><tr><td>Tenant:</td><td>" . $url . "</td></tr><tr><td>Server: </td><td>" . $_SERVER["HTTP_HOST"] . "</td></tr></table></body></html>");
				die();
				return null;
			}
			
			$values = $result->fetch_assoc();
			return Tenant::GetByAssoc($values);
		}
		
		/**
		 * Gets the currently active Tenant, if possible.
		 * @return NULL|\PhoenixSNS\Objects\Tenant The currently active Tenant, or null if no Tenant is currently active.
		 */
		public static function GetCurrent()
		{
			if (System::$TenantName == "") return null;
			return Tenant::GetByURL(System::$TenantName);
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
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "Tenants SET ";
				$query .= "tenant_URL = '" . $MySQL->real_escape_string($this->URL) . "', ";
				$query .= "tenant_Description = '" . $MySQL->real_escape_string($this->Description) . "', ";
				$query .= "tenant_Status = " . ($this->Status == TenantStatus::Enabled ? "1" : "0") . ", ";
				$query .= "tenant_TypeID = " . ($this->Type != null ? $this->Type->ID : "NULL") . ", ";
				$query .= "tenant_PaymentPlanID = " . ($this->PaymentPlan != null ? $this->PaymentPlan->ID : "NULL") . ", ";
				$query .= "tenant_BeginTimestamp = " . ($this->BeginTimestamp != null ? ("'" . $this->BeginTimestamp . "'") : "NULL") . ", ";
				$query .= "tenant_EndTimestamp = " . ($this->EndTimestamp != null ? ("'" . $this->EndTimestamp . "'") : "NULL");
				$query .= " WHERE tenant_ID = " . $this->ID;
			}
			else
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "Tenants (tenant_URL, tenant_Description, tenant_Status, tenant_TypeID, tenant_PaymentPlanID, tenant_BeginTimestamp, tenant_EndTimestamp) VALUES (";
				$query .= "'" . $MySQL->real_escape_string($this->URL) . "', ";
				$query .= "'" . $MySQL->real_escape_string($this->Description) . "', ";
				$query .= ($this->Status == TenantStatus::Enabled ? "1" : "0") . ", ";
				$query .= ($this->Type != null ? $this->Type->ID : "NULL") . ", ";
				$query .= ($this->PaymentPlan != null ? $this->PaymentPlan->ID : "NULL") . ", ";
				$query .= ($this->BeginTimestamp != null ? ("'" . $this->BeginTimestamp . "'") : "NULL") . ", ";
				$query .= ($this->EndTimestamp != null ? ("'" . $this->EndTimestamp . "'") : "NULL");
				$query .= ")";
			}
			
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			if ($this->ID == null)
			{
				$this->ID = $MySQL->insert_id;
			}
			
			// clearing the data centers
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "TenantDataCenters WHERE tdc_TenantID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			// inserting the data centers
			foreach ($this->DataCenters->Items as $item)
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantDataCenters (tdc_TenantID, tdc_DataCenterID) VALUES (";
				$query .= $this->ID . ", ";
				$query .= $item->ID;
				$query .= ")";
			
				$result = $MySQL->query($query);
				if ($MySQL->errno != 0) return false;
			}
			
			return true;
		}

		/**
		 * Deletes this Tenant from the server.
		 * @return boolean True if the delete operation succeeded; false if the delete operation failed.
		 */
		public function Delete()
		{
			global $MySQL;
			if ($this->ID == null) return false;
			
			// Relationships should prevent associated tenant data from being deleted automatically. We need to delete it manually.
			$properties = $this->GetProperties();
			foreach ($properties as $property)
			{
				if (!$this->DeletePropertyValue($property)) return false;
				if (!$property->Delete()) return false;
			}
			
			$objects = $this->GetObjects();
			foreach ($objects as $object)
			{
				if (!$object->Delete()) return false;
			}
			
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "Tenants WHERE tenant_ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			return true;
		}
		
		/**
		 * Copies all objects (and, optionally, data) from this tenant to the specified tenant. 
		 * @param Tenant $tenant The tenant to which to copy the data.
		 * @param boolean $includeData True if data (tenant property values and object instances) should be copied; false otherwise.
		 */
		public function CopyTo(Tenant $tenant, $includeData = true)
		{
			$properties = $this->GetProperties();
			foreach ($properties as $property)
			{
				$tenant->CreateProperty($property);
				$tenant->SetPropertyValue($property, $this->GetPropertyValue($property));
			}
			
			$objects = $this->GetObjects();
				
			foreach ($objects as $object)
			{
				$instances = null;
				if ($includeData)
				{
					$instances = $object->GetInstances();
				}
				
				$tenant->CreateObject($object->Name, $object->GetTitles(), $object->GetDescriptions(), $object->GetProperties(), $object->ParentObject, $instances);
			}
		}
		
		/**
		 * Determines if an ObjectFX object with the specified name exists on this tenant.
		 * @param string $name The name of the object to search for.
		 * @return boolean True if an ObjectFX object with the specified name exists on this tenant; false otherwise.
		 */
		public function HasObject($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjects WHERE (object_TenantID IS NULL OR object_TenantID = " . $this->ID . ") AND object_Name = '" . $MySQL->real_escape_string($name) . "'";
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			return ($count != 0);
		}
		
		/**
		 * Gets all ObjectFX objects which reside in this tenant.
		 * @return TenantObject[] objects which reside in this tenant
		 */
		public function GetObjects()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjects WHERE (object_TenantID IS NULL OR object_TenantID = " . $this->ID . ")";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$object = TenantObject::GetByAssoc($values);
				$retval[] = $object;
			}
			return $retval;
		}
		
		/**
		 * Retrieves a single TenantObject with the specified name from this Tenant.
		 * @param string $name The name of the TenantObject to search for
		 * @return NULL|TenantObject The TenantObject with the specified name, or NULL if no TenantObject with the specified name was found on this Tenant.
		 */
		public function GetObject($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjects WHERE (object_TenantID IS NULL OR object_TenantID = " . $this->ID . ") AND object_Name = '" . $MySQL->real_escape_string($name) . "'";
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0)
			{
				PhoenixSNS::Log("No object with the specified name was found.", array
				(
					"Tenant" => $this->URL,
					"Object" => $name
				));
				return null;
			}
			$values = $result->fetch_assoc();
			$object = TenantObject::GetByAssoc($values);
			return $object;
		}
		/**
		 * Creates a TenantObject with the specified criteria on this Tenant.
		 * @param string $name The name of the TenantObject to create.
		 * @param string $titles
		 * @param string $descriptions
		 * @param TenantInstanceProperty[] $properties Instance properties to create on this TenantObject.
		 * @param TenantObject $parentObject The TenantObject which owns this TenantObject
		 * @param string $instances
		 * @param TenantObjectInstanceProperty $defaultProperty The TenantObjectInstanceProperty used for inline-rendering an instance of this property
		 * @return NULL|\PhoenixSNS\Objects\TenantObject The newly-created TenantObject, or NULL if the creation operation failed.
		 */
		public function CreateObject($name, $titles = null, $descriptions = null, array $properties = null, TenantObject $parentObject = null, array $instances = null, TenantObjectInstanceProperty $defaultProperty = null)
		{
			global $MySQL;
			if ($titles == null) $titles = array($name);
			if ($descriptions == null) $descriptions = array();
			if ($properties == null) $properties = array();
			if ($instances == null) $instances = array();
			
			// do not create the object if the object with the same name already exists
			if ($this->HasObject($name))
			{
				$bt = debug_backtrace();
				trigger_error("Object '" . $name . "' already exists on tenant '" . $this->URL . "' in " . $bt[0]["file"] . "::" . $bt[0]["function"] . " on line " . $bt[0]["line"] . "; ", E_USER_WARNING);
				return $this->GetObject($name);
			}
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjects (object_TenantID, object_ModuleID, object_ParentObjectID, object_Name, object_DefaultPropertyID) VALUES (";
			$query .= $this->ID . ", ";
			$query .= "NULL" . ", ";
			$query .= ($parentObject == null ? "NULL" : $parentObject->ID) . ", ";
			$query .= "'" . $MySQL->real_escape_string($name) . "', ";
			if ($defaultProperty != null && is_numeric($defaultProperty->ID))
			{
				$query .= $defaultProperty->ID;
			}
			else
			{
				$query .= "NULL";
			}
			$query .= ")";
			
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			
			$id = $MySQL->insert_id;
			$object = TenantObject::GetByID($id);
			
			$object->SetTitles($titles);
			$object->SetDescriptions($descriptions);
			
			foreach ($properties as $property)
			{
				$object->CreateInstanceProperty($property);
			}
			
			if (is_array($instances))
			{
				foreach ($instances as $instance)
				{
					// handle as an array of property/value pairs
					$object->CreateInstance($instance);
				}
			}
			
			return $object;
		}
		/**
		 * Creates an enumeration on this Tenant
		 * @deprecated Please create a SingleInstance or MultipleInstance property instead.
		 * @param unknown $name
		 * @param string $description
		 * @param string $choices
		 * @return \PhoenixSNS\Objects\TenantEnumeration
		 */
		public function CreateEnumeration($name, $description = null, $choices = null)
		{
			global $MySQL;
			if ($choices == null) $choices = array();
			
			$item = new TenantEnumeration($name, $description, $choices);
			$item->Tenant = $this;
			$item->Choices = $choices;
			$item->Update();
			
			return $item;
		}
		
		/**
		 * Gets all the TenantProperties associated with this Tenant.
		 * @return TenantProperty[] properties associated with this Tenant
		 */
		public function GetProperties()
		{
			global $MySQL;
			
			$retval = array();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantProperties WHERE (property_TenantID = " . $this->ID . " OR property_TenantID IS NULL)";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantProperty::GetByAssoc($values);
			}
			return $retval;
		}
		/**
		 * Creates the specified property on this Tenant.
		 * @param TenantProperty $property the TenantProperty to create on this Tenant
		 */
		public function CreateProperty($property)
		{
			return TenantProperty::Create($property, $this);
		}
		/**
		 * Gets the TenantProperty with the specified name on this Tenant
		 * @param string $propertyName The name of the TenantProperty to search for.
		 * @return NULL|TenantProperty The TenantProperty with the specified name, or NULL if no TenantProperty with the specified name was found on this Tenant.
		 */
		public function GetProperty($propertyName)
		{
			global $MySQL;
			
			$retval = array();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantProperties WHERE (property_TenantID = " . $this->ID . " OR property_TenantID IS NULL) AND property_Name = '" . $MySQL->real_escape_string($propertyName) . "'";
			$result = $MySQL->query($query);
			if ($result === false)
			{
				PhoenixSNS::Log("Database error when trying to obtain a reference to a property on the tenant.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query
				));
				return null;
			}
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantProperty::GetByAssoc($values);
		}
		/**
		 * Deletes the property value(s) associated with the specified TenantProperty.
		 * @param TenantProperty $property
		 * @return boolean True if the operation completed successfully; false if the operation failed.
		 */
		public function DeletePropertyValue($property)
		{
			global $MySQL;
			
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "TenantPropertyValues WHERE propval_PropertyID = " . $property->ID;
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			return true;
		}
		
		private $mvarJournalID = null;
		/**
		 * Begins an audit journal with the specified description.
		 * @param string $description A brief description detailing why an audit journal is being recorded. 
		 * @return boolean True if the operation completed successfully; false if the operation failed.
		 */
		public function BeginJournal($description)
		{
			global $MySQL;
			
			$userID = "NULL";
			$user = User::GetCurernt();
			if ($user != null) $userID = $user->ID;
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantJournals (journal_TenantID, journal_Description, journal_CreationTimestamp, journal_CreationUserID) VALUES (" . $this->ID . ", '" . $MySQL->real_escape_string($description) . "', NOW(), " . $userID . ")";
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$this->mvarJournalID = $MySQL->insert_id;
			return true;
		}
		/**
		 * Ends the currently-recording audit journal, if an audit journal is currently being recorded.
		 */
		public function EndJournal()
		{
			$this->mvarJournalID = null;
		}
		/**
		 * Gets the value associated with the specified TenantProperty.
		 * @param string|TenantProperty $property The name of the TenantProperty, or an instance of the TenantProperty object, indicating which TenantProperty to retrieve the value for. 
		 * @param mixed $defaultValue The default value to return if the TenantProperty could not be resolved.
		 * @return NULL|mixed The value associated with the specified TenantProperty, or NULL if the property could not be resolved or the default value was not specified.
		 */
		public function GetPropertyValue($property, $defaultValue = null)
		{
			global $MySQL;
			
			// if we passed in a string to this property (because it's easier) then let's get a reference to that property
			if (is_string($property))
			{
				$propname = $property;
				$property = $this->GetProperty($property);
			}
			
			$query = "SELECT (CASE WHEN (propval_Value IS NULL) THEN property_DefaultValue ELSE propval_Value END) FROM " . System::$Configuration["Database.TablePrefix"] . "TenantPropertyValues, " . System::$Configuration["Database.TablePrefix"] . "TenantProperties WHERE " . System::$Configuration["Database.TablePrefix"] . "TenantProperties.property_ID = " . $property->ID . " AND " . System::$Configuration["Database.TablePrefix"] . "TenantProperties.property_ID = " . System::$Configuration["Database.TablePrefix"] . "TenantPropertyValues.propval_PropertyID";
			
			$result = $MySQL->query($query);
			if ($result === false)
			{
				PhoenixSNS::Log("Database error when trying to look up a value of a property on the tenant.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query,
					"Property" => ($property == null ? ($propname == null ? "(null)" : $propname) : (is_string($property) ? $property : $property->Name))
				));
				return null;
			}
			
			$count = $result->num_rows;
			if ($count == 0)
			{
				PhoenixSNS::Log("The property has no defined values on the specified tenant.", array
				(
					"Property" => ($property == null ? ($propname == null ? "(null)" : $propname) : (is_string($property) ? $property : $property->Name))
				), LogMessageSeverity::Warning);
				if ($defaultValue != null) return $defaultValue;
				return $property->DefaultValue;
			}
			
			$values = $result->fetch_array();
			return $property->Decode($values[0]);
		}
		/**
		 * Sets the value of the given property.
		 * @param string|TenantProperty $property The name of the TenantProperty to set, or the instance of the TenantProperty. 
		 * @param mixed $value The desired value with which to update the TenantProperty.
		 * @return boolean True if the operation completed successfully; false if the operation failed.
		 */
		public function SetPropertyValue($property, $value)
		{
			global $MySQL;
			
			// if we passed in a string to this property (because it's easier) then let's get a reference to that property
			if (is_string($property))
			{
				$propname = $property;
				$property = $this->GetProperty($property);
			}
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantPropertyValues (propval_PropertyID, propval_Value) VALUES (" . $property->ID . ", '" . $MySQL->real_escape_string($property->Encode($value)) . "') ON DUPLICATE KEY UPDATE propval_Value = '" . $MySQL->real_escape_string($property->Encode($value)) . "'";
			
			$result = $MySQL->query($query);
			if ($result === false)
			{
				PhoenixSNS::Log("Database error when trying to update the value of a property on the tenant.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query,
					"Property" => ($property == null ? ($propname == null ? "(null)" : $propname) : (is_string($property) ? $property : $property->Name))
				));
			}
			
			if ($MySQL->errno != 0) return false;
			
			if ($this->mvarJournalID != null)
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantPropertyJournalEntries (entry_JournalID, entry_PropertyID, entry_Value) VALUES (" . $this->mvarJournalID . ", " . $property->ID . ", " . $MySQL->real_escape_string($property->Encode($value)) . ")";
				$result = $MySQL->query($query);
				if ($result === false) return false;
			}
			
			return true;
		}
		/**
		 * Retrieves an array of all TenantObjectInstances associated with this Tenant.
		 * @param string $conditions The conditions that must be satisfied to retrieve an instance.
		 * @param string $validObjects The objects that are considered valid for retrieval.
		 * @param string $max The maximum number of objects that may be retrieved by this function (or NULL to not specify a limit).
		 * @return TenantObjectInstance[] array of all TenantObjectInstances associated with this Tenant.
		 */
		public function GetObjectInstances($conditions = null, $validObjects = null, $max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " .
				System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances, " .
				System::$Configuration["Database.TablePrefix"] . "TenantObjects" .
				" WHERE " .
				System::$Configuration["Database.TablePrefix"] . "TenantObjects.object_TenantID = " . $this->ID . " AND " .
				System::$Configuration["Database.TablePrefix"] . "TenantObjects.object_ID = " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances.instance_ObjectID";
				
			if (is_array($validObjects))
			{
				$count = count($validObjects);
				if ($count > 0)
				{
					$query .= " AND (";
					for ($i = 0; $i < $count; $i++)
					{
						$query .= System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances.instance_ObjectID = " . $validObjects[$i]->ID;
						if ($i < $count - 1) $query .= " OR ";
					}
					$query .= ")";
				}
			}
			
			$retval = array();
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$item = TenantObjectInstance::GetByAssoc($values);
				
				if ($conditions != null)
				{
					if (!$conditions->Evaluate(array($item))) continue;
				}
				
				$retval[] = $item;
			}
			return $retval;
		}
		
		/**
		 * Determines if the specified module is enabled on this tenant.
		 * @return boolean True if the specified module is enabled on this tenant; false otherwise.
		 */
		public function HasModule($module)
		{
			global $MySQL;
			
			$query = "SELECT (tenantmodule_ModuleID = module_ID) AS module_Enabled";
			$query .= " FROM " . System::$Configuration["Database.TablePrefix"] . "Modules";
			$query .= ", " . System::$Configuration["Database.TablePrefix"] . "TenantModules";
			$query .= "  WHERE tenantmodule_ModuleID = module_ID AND tenantmodule_TenantID = " . $this->ID;
			
			$result = $MySQL->query($query);
			if ($result->num_rows < 0) return false;
			
			$values = $result->fetch_assoc();
			return ($values[0] == 1);
		}

		/**
		 * Gets the JSON representation of this object for use in AJAX calls.
		 * @return string The JSON representation of this object.
		 */
		public function ToJSON()
		{
			$json = "";
			$json .= "{ ";
			$json .= "\"ID\": " . $this->ID . ", ";
			$json .= "\"URL\": \"" . \JH\Utilities::JavaScriptEncode($this->URL, "\"") . "\", ";
			$json .= "\"Description\": \"" . \JH\Utilities::JavaScriptEncode($this->Description, "\"") . "\", ";
			// $json .= "\"Status\": " . $this->Status . ", ";
			$json .= "\"BeginTimestamp\": \"" . $this->BeginTimestamp . "\", ";
			$json .= "\"EndTimestamp\": \"" . $this->EndTimestamp . "\"";
			$json .= " }";
			return $json;
		}
	}
?>