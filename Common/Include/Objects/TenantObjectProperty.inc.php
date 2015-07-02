<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	/**
	 * Represents a static property on a TenantObject.
	 * @author Michael Becker
	 */
	class TenantObjectProperty
	{
		/**
		 * The unique, incremental ID number of this property.
		 * @var int
		 */
		public $ID;
		/**
		 * @deprecated The tenant can be accessed through the ParentObject property.
		 */
		public $Tenant;
		/**
		 * The object which contains this property.
		 * @var TenantObject
		 */
		public $ParentObject;
		
		/**
		 * The name used in code to identify this property.
		 * @var string
		 */
		public $Name;
		/**
		 * A short description of this property.
		 * @var string
		 * @deprecated Please use string tables to provide human-readable descriptions.
		 */
		public $Description;
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
		 * @deprecated Please use SingleInstance or MultipleInstance data type for enumeration properties.
		 */
		public $Enumeration;
		/**
		 * @deprecated Please use SingleInstance or MultipleInstance data type for enumeration properties.
		 */
		public $RequireChoiceFromEnumeration;

		/**
		 * Determines whether this TenantObjectInstanceProperty is visible when rendered as a column in a ListView.
		 * @var boolean
		 */
		public $ColumnVisible;
		
		/**
		 * Renders this TenantObjectProperty in a column view (e.g. ListView).
		 * @param mixed $value The value of the TenantObjectProperty to render.
		 */
		public function RenderColumn($value = null)
		{
			if ($this->DataType == null || $this->DataType->ColumnRendererCodeBlob == null)
			{
				?>
				<input style="width: 100%;" type="text" id="txtProperty_<?php echo($property->ID); ?>" name="Property_<?php echo($property->ID); ?>" value="<?php
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
		 * Creates a new TenantObjectProperty with the specified parameters.
		 * @param string $name The name used in code to identify this TenantObjectProperty.
		 * @param string $description A short description of this TenantObjectProperty.
		 * @param DataType $dataType The data type associated with this TenantObjectProperty.
		 * @param mixed $defaultValue The default value of this TenantObjectProperty.
		 * @param boolean $required True if this TenantObjectProperty is required to have a value; false otherwise.
		 */
		public function __construct($name, $description = null, $dataType = null, $defaultValue = null, $required = false, $enumeration = null, $requireChoiceFromEnumeration = false)
		{
			$this->Name = $name;
			$this->Description = $description;
			$this->DataType = $dataType;
			$this->DefaultValue = $defaultValue;
			$this->Required = $required;
		}
		
		/**
		 * Creates a new TenantObjectProperty object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\TenantObjectProperty based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new TenantObjectProperty();
			$item->ID = $values["property_ID"];
			$item->Tenant = Tenant::GetByID($values["property_TenantID"]);
			$item->ParentObject = TenantObject::GetByID($values["property_ObjectID"]);
			$item->Name = $values["property_Name"];
			$item->Description = $values["property_Description"];
			$item->DataType = DataType::GetByID($values["property_DataTypeID"]);
			$item->DefaultValue = $values["property_DefaultValue"];
			$item->Required = ($values["property_IsRequired"] == 1);
			$item->Enumeration = TenantEnumeration::GetByID($values["property_EnumerationID"]);
			$item->RequireChoiceFromEnumeration = ($values["property_RequireChoiceFromEnumeration"] == 1);
			return $item;
		}
		/**
		 * Retrieves a single TenantObjectProperty with the given ID.
		 * @param int $id The ID of the TenantObjectProperty to return
		 * @return NULL|\PhoenixSNS\Objects\TenantObjectProperty The TenantObjectProperty with the given ID, or NULL if no TenantObjectProperty with the given ID was found
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectProperties";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantObjectProperty::GetByAssoc($values);
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
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectProperties (property_TenantID, property_ObjectID, property_Name, property_Description, property_DataTypeID, property_DefaultValue, property_IsRequired) VALUES (";
				$query .= ($this->Tenant == null ? "NULL" : $this->Tenant->ID) . ", ";
				$query .= ($this->ParentObject == null ? "NULL" : $this->ParentObject->ID) . ", ";
				$query .= "'" . $MySQL->real_escape_string($this->Name) . "', ";
				$query .= "'" . $MySQL->real_escape_string($this->Description) . "', ";
				$query .= ($this->DataType == null ? "NULL" : $this->DataType->ID) . ", ";
				$query .= $this->DefaultValue == null ? "NULL" : ("'" . $this->DefaultValue . "'") . ", ";
				$query .= ($this->Required ? "1" : "0") . ", ";
			}
			else
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "TenantObjectProperties SET ";
				$query .= "property_TenantID = " . ($this->Tenant == null ? "NULL" : $this->Tenant->ID) . ", ";
				$query .= "property_ObjectID = " . ($this->ParentObject == null ? "NULL" : $this->ParentObject->ID) . ", ";
				$query .= "property_Name = '" . $MySQL->real_escape_string($this->Name) . "', ";
				$query .= "property_Description = '" . $MySQL->real_escape_string($this->Description) . "', ";
				$query .= "property_DataTypeID = " . ($this->DataType == null ? "NULL" : $this->DataType->ID) . ", ";
				$query .= "property_DefaultValue = " . $this->DefaultValue == null ? "NULL" : ("'" . $this->DefaultValue . "'") . ", ";
				$query .= "property_IsRequired = " . ($this->Required ? "1" : "0") . ", ";
				$query .= " WHERE property_ID = " . $this->ID;
			}
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			if ($this->ID == null)
			{
				$this->ID = $MySQL->insert_id;
			}
			
			return true;
		}
	}
	
	/**
	 * A property/value pair associated with a TenantObjectProperty.
	 * @author Michael Becker
	 */
	class TenantObjectPropertyValue
	{
		/**
		 * The TenantObjectProperty associated with this property/value pair.
		 * @var TenantObjectProperty
		 */
		public $Property;
		/**
		 * The value associated with this property/value pair.
		 * @var mixed
		 */
		public $Value;
		
		/**
		 * Creates a new TenantObjectPropertyValue.
		 * @param TenantObjectProperty $property The TenantObjectProperty associated with this property/value pair.
		 * @param mixed $value The value associated with this property/value pair.
		 */
		public function __construct($property, $value)
		{
			$this->Property = $property;
			$this->Value = $value;
		}
	}
?>