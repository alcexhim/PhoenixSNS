<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	/**
	 * An instance of a TenantObject.
	 * @author Michael Becker
	 * @see TenantObject
	 */
	class TenantObjectInstance
	{
		/**
		 * The unique, incremental ID number of this Module
		 * @var int
		 */
		public $ID;
		/**
		 * The TenantObject of which this is an instance.
		 * @var TenantObject
		 */
		public $ParentObject;
		
		/**
		 * Creates a TenantObjectInstance of the specified TenantObject.
		 * @param TenantObject $parentObject The object of which to create an instance.
		 */
		public function __construct($parentObject)
		{
			$this->ParentObject = $parentObject;
		}
		
		/**
		 * Creates a new TenantObjectInstance object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\TenantObjectInstance based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new TenantObjectInstance(TenantObject::GetByID($values["instance_ObjectID"]));
			$item->ID = $values["instance_ID"];
			return $item;
		}

		/**
		 * Retrieves a single TenantObjectInstance with the given ID.
		 * @param int $id The ID of the TenantObjectInstance to return
		 * @return NULL|\PhoenixSNS\Objects\TenantObjectInstance The TenantObjectInstance with the given ID, or NULL if no TenantObjectInstance with the given ID was found
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances WHERE instance_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$values = $result->fetch_assoc();
			return TenantObjectInstance::GetByAssoc($values);
		}
		
		/**
		 * Gets an array of all the TenantObjectInstancePropertyValues associated with this TenantObjectInstance.
		 * @return TenantObjectInstancePropertyValue[] all the property values associated with this instance
		 * @see TenantObjectInstancePropertyValue
		 */
		public function GetPropertyValues()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstancePropertyValues WHERE propval_InstanceID = " . $this->ID;
			$result = $MySQL->query($query);
			
			$retval = array();
			if ($result === false) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				
				$property = TenantObjectProperty::GetByID($values["propval_PropertyID"]);
				
				$value = $property->Decode($values["propval_Value"]);
				$retval[] = new TenantObjectInstancePropertyValue($property, $value);
			}
			return $retval;
		}
		
		/**
		 * Gets the value of the specified property on this TenantObjectInstance.
		 * @param TenantObjectInstanceProperty $property The property to search for.
		 * @param mixed $defaultValue The value to return if the specified property has no defined value. 
		 * @return mixed The value of the specified property, or the defaultValue if the specified property's value has not been defined.
		 * @see TenantObjectInstanceProperty
		 */
		public function GetPropertyValue($property, $defaultValue = null)
		{
			global $MySQL;
			
			if (is_string($property))
			{
				$property = $this->ParentObject->GetInstanceProperty($property);
			}
			if ($property == null) return $defaultValue;
			
			$query = "SELECT propval_Value FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstancePropertyValues WHERE propval_InstanceID = " . $this->ID . " AND propval_PropertyID = " . $property->ID;
			
			$result = $MySQL->query($query);
			if ($result === false) return $defaultValue;
			
			$count = $result->num_rows;
			if ($count == 0) return $defaultValue;
			
			$values = $result->fetch_array();
			return $property->Decode($values[0]);
		}
		/**
		 * Sets the value of the specified property on this TenantObjectInstance.
		 * @param unknown $property
		 * @param unknown $value
		 * @return boolean
		 * @see TenantObjectInstanceProperty
		 */
		public function SetPropertyValue($property, $value)
		{
			global $MySQL;
			
			if (is_string($property))
			{
				$property = $this->ParentObject->GetInstanceProperty($property);
			}
			if ($property == null) return false;
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstancePropertyValues (propval_InstanceID, propval_PropertyID, propval_Value) VALUES (";
			$query .= $this->ID . ", ";
			$query .= $property->ID . ", ";
			$query .= "'" . $MySQL->real_escape_string($property->Encode($value)) . "'";
			$query .= ")";
			$query .= " ON DUPLICATE KEY UPDATE ";
			$query .= "propval_PropertyID = values(propval_PropertyID), ";
			$query .= "propval_Value = values(propval_Value)";
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			return true;
		}
		/**
		 * Determines if this TenantObjectInstance has the specified property value.
		 * @param TenantObjectInstanceProperty $property The property to search for.
		 * @return boolean True if the property has a value; false otherwise.
		 */
		public function HasPropertyValue($property)
		{
			global $MySQL;
			
			if ($property == null) return false;
			
			$query = "SELECT COUNT(propval_Value) FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstancePropertyValues WHERE propval_InstanceID = " . $this->ID . " AND propval_PropertyID = " . $property->ID;
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$count = $result->num_rows;
			if ($count == 0) return false;
			
			$values = $result->fetch_array();
			return ($values[0] > 0);
		}

		/**
		 * Updates the server with the information in this object.
		 * @return boolean True if the update succeeded; false if an error occurred.
		 */
		public function Update()
		{
			global $MySQL;
			if ($this->ID == null)
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances (instance_ObjectID) VALUES (";
				$query .= $this->ParentObject->ID;
				$query .= ")";
			}
			else
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances SET ";
				$query .= "instance_ObjectID = " . $this->ParentObject->ID;
				$query .= " WHERE instance_ID = " . $this->ID;
			}
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			if ($this->ID == null)
			{
				$this->ID = $MySQL->insert_id;
			}
			return true;
		}
		
		/**
		 * Gets the user-friendly string representation of this TenantObjectInstance.
		 * @return string The user-friendly string representation of this TenantObjectInstance.
		 */
		public function ToString()
		{
			return $this->GetPropertyValue("Name");
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
			$json .= "\"ParentObject\": " . $this->ParentObject->ToJSON();
			$json .= " }";
			return $json;
		}
	}
	/**
	 * An instance property on a TenantObject.
	 * @author Michael Becker
	 * @see TenantObject
	 * @see TenantObjectInstance
	 * @see TenantObjectInstancePropertyValue
	 */
	class TenantObjectInstanceProperty
	{
		/**
		 * The unique, incremental ID number of this property.
		 * @var int
		 */
		public $ID;
		/**
		 * The object which contains this property.
		 * @var unknown
		 */
		public $ParentObject;
		/**
		 * The name used in code to identify this property.
		 * @var string
		 */
		public $Name;
		/**
		 * The data type for this property.
		 * @var DataType
		 */
		public $DataType;
		/**
		 * The default value of this property.
		 * @var mixed
		 */
		public $DefaultValue;
		/**
		 * True if this property is required to have a value; false if not.
		 * @var boolean
		 */
		public $Required;

		/**
		 * Retrieves a single TenantObjectInstanceProperty with the given ID.
		 * @param int $id The ID of the TenantObjectInstanceProperty to return
		 * @return NULL|\PhoenixSNS\Objects\TenantObjectInstanceProperty The TenantObjectInstanceProperty with the given ID, or NULL if no TenantObjectInstanceProperty with the given ID was found
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceProperties WHERE property_ID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantObjectInstanceProperty::GetByAssoc($values);
		}
		
		/**
		 * Determines whether this TenantObjectInstanceProperty is visible when rendered as a column in a ListView.
		 * @var boolean
		 */
		public $ColumnVisible;

		/**
		 * Renders this TenantObjectInstanceProperty in a column view (e.g. ListView).
		 * @param mixed $value The value of the TenantObjectInstanceProperty to render.
		 */
		public function RenderColumn($value = null)
		{
			if ($this->DataType == null || $this->DataType->ColumnRendererCodeBlob == null)
			{
				?>
				<input style="width: 100%;" type="text" id="txtProperty_<?php echo($this->ID); ?>" name="Property_<?php echo($this->ID); ?>" value="<?php
				if ($value == null)
				{
					echo($this->DefaultValue);
				}
				else
				{
					echo($value);
				}
				?>" />
				<?php
			}
			else
			{
				if ($value == null)
				{
					$this->DataType->RenderColumn($this->DefaultValue);
				}
				else
				{
					$this->DataType->RenderColumn($value);
				}
			}
		}
		
		/**
		 * @deprecated Please call Encode() on this property's DataType directly.
		 * @param mixed $value
		 * @return string
		 */
		public function Encode($value)
		{
			if ($this->DataType == null) return $value;
			return $this->DataType->Encode($value);
		}
		/**
		 * @deprecated Please call Decode() on this property's DataType directly.
		 * @param mixed $value
		 * @return string
		 */
		public function Decode($value)
		{
			if ($this->DataType == null) return $value;
			return $this->DataType->Decode($value);
		}
		
		/**
		 * Creates a new TenantObjectInstanceProperty with the specified parameters.
		 * @param string $name The name used in code to identify this property.
		 * @param string $dataType The data type for this property.
		 * @param string $defaultValue The default value of this property.
		 * @param string $required True if this property is required to have a value; false if not.
		 */
		public function __construct($name = null, $dataType = null, $defaultValue = null, $required = false)
		{
			$this->Name = $name;
			$this->DataType = $dataType;
			$this->DefaultValue = $defaultValue;
			$this->Required = $required;
		}

		/**
		 * Creates a new TenantObjectInstanceProperty object based on the given values from the database.
		 * @param array $values
		 * @return TenantObjectInstanceProperty based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new TenantObjectInstanceProperty();
			$item->ID = $values["property_ID"];
			$item->ParentObject = TenantObject::GetByID($values["property_ObjectID"]);
			$item->Name = $values["property_Name"];
			$item->DataType = DataType::GetByID($values["property_DataTypeID"]);
			if ($item->DataType != null)
			{
				$item->DefaultValue = $item->DataType->Decode($values["property_DefaultValue"]);
			}
			$item->Required = ($values["property_IsRequired"] == 1);
			$item->ColumnVisible = ($values["property_ColumnVisible"] == 1);
			return $item;
		}
	}
	/**
	 * A value associated with a TenantObjectInstanceProperty.
	 * @author Michael Becker
	 * @see TenantObject
	 * @see TenantObjectInstance
	 * @see TenantObjectInstanceProperty
	 */
	class TenantObjectInstancePropertyValue
	{
		/**
		 * The property on which this value is set.
		 * @var TenantObjectInstanceProperty
		 */
		public $Property;
		/**
		 * The value to set.
		 * @var mixed
		 */
		public $Value;
		
		/**
		 * Creates a new TenantObjectInstanceProperty with the specified parameters.
		 * @param TenantObjectInstanceProperty $property The property on which this value is set.
		 * @param mixed $value The value to set.
		 */
		public function __construct(TenantObjectInstanceProperty $property, $value = null)
		{
			$this->Property = $property;
			$this->Value = $value;
		}
	}
?>