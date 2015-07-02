<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	/**
	 * An ObjectFX object that resides on a Tenant.
	 * @author Michael Becker
	 *
	 */
	class TenantObject
	{
		/**
		 * The unique, incremental ID number of this Object
		 * @var int
		 */
		public $ID;
		/**
		 * The Tenant in which this TenantObject resides
		 * @var Tenant
		 */
		public $Tenant;
		/**
		 * The Module providing this TenantObject, or NULL if this TenantObject is a global object.
		 * @var Module|NULL
		 */
		public $Module;
		/**
		 * The TenantObject which owns this TenantObject.
		 * @var TenantObject
		 */
		public $ParentObject;
		/**
		 * The name used in code to identify this TenantObject.
		 * @var string
		 */
		public $Name;
		/**
		 * The default property of this TenantObject.
		 * @var TenantObjectProperty
		 */
		public $DefaultProperty;

		/**
		 * Creates a new TenantObject object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\TenantObject based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new TenantObject();
			$item->ID = $values["object_ID"];
			$item->Tenant = Tenant::GetByID($values["object_TenantID"]);
			$item->Module = Module::GetByID($values["object_ModuleID"]);
			$item->ParentObject = TenantObject::GetByID($values["object_ParentObjectID"]);
			$item->DefaultProperty = TenantObjectProperty::GetByID($values["object_DefaultPropertyID"]);
			$item->Name = $values["object_Name"];
			return $item;
		}
		/**
		 * Retrieves all TenantObjects, optionally associated with the given Tenant.
		 * @param string $max The maximum number of TenantObjects to return
		 * @param Tenant $tenant The tenant whose TenantObjects to return (or null to return all TenantObjects) 
		 * @return TenantObject[] array of TenantObjects residing on the server and optionally associated with the specified Tenant
		 */
		public static function Get($max = null, $tenant = null)
		{
			global $MySQL;
			
			$retval = array();
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			if ($tenant == null) return $retval;
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjects WHERE object_TenantID = " . $tenant->ID;
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = $MySQL->query($query);
			if ($result === false) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantObject::GetByAssoc($values);
			}
			return $retval;
		}
		/**
		 * Retrieves a single TenantObject with the given ID.
		 * @param int $id The ID of the TenantObject to return
		 * @return NULL|\PhoenixSNS\Objects\TenantObject The TenantObject with the given ID, or NULL if no TenantObject with the given ID was found
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjects WHERE object_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantObject::GetByAssoc($values);
		}
		
		/**
		 * Creates an instance of this ObjectFX object with the specified properties. 
		 * @param TenantObjectInstancePropertyValue[] $properties
		 * @return \PhoenixSNS\Objects\TenantObjectInstance
		 */
		public function CreateInstance($properties)
		{
			if (!is_array($properties)) return false;
			
			$inst = new TenantObjectInstance($this);
			$inst->Update();
			
			foreach ($properties as $instprop)
			{
				$inst->SetPropertyValue($instprop->Property, $instprop->Value);
			}
			return $inst;
		}
		/**
		 * Retrieves the value for the specified property, or the specified default value if no value is associated with the specified property or the specified property does not exist.
		 * @param TenantObjectProperty $property
		 * @param mixed $defaultValue The default value used when no value is associated with the specified property or the specified property does not exist.
		 * @return mixed The value for the specified property, or the specified default value if no value is associated with the specified property or the specified property does not exist.
		 */
		public function GetPropertyValue($property, $defaultValue = null)
		{
			global $MySQL;
			
			if (is_string($property))
			{
				// we were passed a string for the property, to make things easier, so we
				// need to get the property object reference for that string
				$property = $this->GetProperty($property);
			}
			if ($property == null) return $defaultValue;
			
			$query = "SELECT propval_Value FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectPropertyValues WHERE propval_PropertyID = " . $property->ID;
			
			$result = $MySQL->query($query);
			if ($result === false) return $defaultValue;
			
			$count = $result->num_rows;
			if ($count == 0) return $defaultValue;
			
			$values = $result->fetch_array();
			return $property->DataType->Decode($values[0]);
		}
		/**
		 * Sets the value for the specified property.
		 * @param TenantObjectProperty $property The property for which to set the value.
		 * @param string $value The value to set.
		 * @return boolean True if the operation completed successfully; false if the operation failed.
		 */
		public function SetPropertyValue($property, $value)
		{
			global $MySQL;
			
			if (is_string($property))
			{
				$property = $this->GetProperty($property);
			}
			if ($property == null) return false;
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectPropertyValues (propval_PropertyID, propval_Value) VALUES (";
			$query .= $property->ID . ", ";
			$query .= "'" . $MySQL->real_escape_string($property->Encode($value)) . "'";
			$query .= ")";
			$query .= " ON DUPLICATE KEY UPDATE ";
			$query .= "propval_PropertyID = values(propval_PropertyID), ";
			$query .= "propval_Value = values(propval_Value)";
			
			$result = $MySQL->query($query);
			if ($result === false)
			{
				PhoenixSNS::Log("Database error when trying to update a property value for the specified object.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query
				));
				return false;
			}
			
			return true;
		}
		
		/**
		 * Associates the specified TenantObjectInstanceProperty with this TenantObject.
		 * @param TenantObjectInstanceProperty $property The TenantObjectInstanceProperty to assocaite with this TenantObject.
		 * @return boolean True if the operation completed successfuly; false if the operation failed.
		 */
		public function CreateInstanceProperty($property)
		{
			global $MySQL;
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceProperties (property_ObjectID, property_Name, property_DataTypeID, property_DefaultValue, property_IsRequired) VALUES (";
			$query .= $this->ID . ", ";
			$query .= "'" . $MySQL->real_escape_string($property->Name) . "', ";
			$query .= ($property->DataType == null ? "NULL" : $property->DataType->ID) . ", ";
			$query .= "'" . $MySQL->real_escape_string($property->Encode($property->DefaultValue)) . "', ";
			$query .= ($property->Required ? "1" : "0");
			$query .= ")";
			$result = $MySQL->query($query);
			if ($result === false)
			{
				PhoenixSNS::Log("Database error when trying to create an instance property for the specified tenant object.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query
				));
				return false;
			}
			return true;
		}
		
		/**
		 * Creates a static method on this TenantObject using the given parameters.
		 * @param string $name The name used in code to identify this TenantObjectMethod.
		 * @param TenantObjectMethodParameter[] $parameters Array of TenantObjectMethodParameter objects representing parameters to this TenantObjectMethod.
		 * @param string $codeblob The PHP code that runs when this TenantObjectMethod is executed.
		 * @param string[] $namespaceReferences Array of PHP namespaces that should be referenced by this TenantObjectMethod.
		 * @return TenantObjectMethod|NULL The newly created TenantObjectMethod, or NULL if the creation operation failed.
		 */
		public function CreateMethod($name, $parameters, $codeblob, $namespaceReferences = null)
		{
			global $MySQL;
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethods (method_ObjectID, method_Name, method_CodeBlob) VALUES (";
			$query .= $this->ID . ", ";
			$query .= "'" . $MySQL->real_escape_string($name) . "', ";
			$query .= "'" . $MySQL->real_escape_string($codeblob) . "'";
			$query .= ")";
			$result = $MySQL->query($query);
			if ($result === false)
			{
				PhoenixSNS::Log("Database error when trying to create a static method for the specified tenant object.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query
				));
				return null;
			}
			
			$method = TenantObjectMethod::GetByID($MySQL->insert_id);
			
			if (is_array($namespaceReferences))
			{
				foreach ($namespaceReferences as $ref)
				{
					$method->AddNamespaceReference($ref);
				}
			}
			return $method;
		}
		/**
		 * Creates an instance method on this TenantObject using the given parameters.
		 * @param string $name The name used in code to identify this TenantObjectMethod.
		 * @param TenantObjectMethodParameter[] $parameters Array of TenantObjectMethodParameter objects representing parameters to this TenantObjectMethod.
		 * @param string $codeblob The PHP code that runs when this TenantObjectMethod is executed.
		 * @param string $description  Obsolete - do not use
		 * @param string[] $namespaceReferences Array of PHP namespaces that should be referenced by this TenantObjectMethod.
		 * @return TenantObjectMethod|NULL The newly created TenantObjectMethod, or NULL if the creation operation failed.
		 */
		public function CreateInstanceMethod($name, $parameters, $codeblob, $description = null, $namespaceReferences = null)
		{
			global $MySQL;
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceMethods (method_ObjectID, method_Name, method_Description, method_CodeBlob) VALUES (";
			$query .= $this->ID . ", ";
			$query .= "'" . $MySQL->real_escape_string($name) . "', ";
			$query .= ($description == null ? "NULL" : ("'" . $MySQL->real_escape_string($description) . "'")) . ", ";
			$query .= "'" . $MySQL->real_escape_string($codeblob) . "'";
			$query .= ")";
			$result = $MySQL->query($query);
			if ($result === false)
			{
				PhoenixSNS::Log("Database error when trying to create an instance method for the specified tenant object.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query
				));
				return false;
			}
			
			$method = TenantObjectInstanceMethod::GetByID($MySQL->insert_id);
			
			if (is_array($namespaceReferences))
			{
				foreach ($namespaceReferences as $ref)
				{
					$method->AddNamespaceReference($ref);
				}
			}
			return $method;
		}
		
		/**
		 * Retrieves the TenantObjectProperty with the specified name.
		 * @param string $propertyName The name of the TenantObjectProperty to search for.
		 * @return TenantObjectProperty|NULL The TenantObjectProperty with the specified name, or NULL if no TenantObjectProperty with the specified name exists.
		 */
		public function GetProperty($propertyName)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectProperties WHERE property_ObjectID = " . $this->ID . " AND property_Name = '" . $MySQL->real_escape_string($propertyName) . "'";
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantObjectProperty::GetByAssoc($values);
		}
		public function GetProperties($max = null)
		{
			global $MySQL;
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectProperties WHERE property_ObjectID = " . $this->ID;
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = $MySQL->query($query);
			$retval = array();
			
			if ($result === false) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantObjectProperty::GetByAssoc($values);
			}
			return $retval;
		}
		public function GetInstanceProperty($propertyName)
		{
			global $MySQL;
			
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceProperties WHERE property_ObjectID = " . $this->ID . " AND property_Name = '" . $MySQL->real_escape_string($propertyName) . "'";
			
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0)
			{
				PhoenixSNS::Log("Could not fetch the specified instance property on the object.", array
				(
					"Object" => $this->Name,
					"Property" => $propertyName
				));
				return null;
			}
			
			$values = $result->fetch_assoc();
			
			return TenantObjectInstanceProperty::GetByAssoc($values);
		}
		public function GetInstanceProperties($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceProperties WHERE property_ObjectID = " . $this->ID;
			
			$result = $MySQL->query($query);
			$retval = array();
			
			if ($result === false) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantObjectInstanceProperty::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function GetMethod($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethods WHERE method_ObjectID = " . $this->ID . " AND method_Name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantObjectMethod::GetByAssoc($values);
		}
		public function GetMethods($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethods WHERE method_ObjectID = " . $this->ID;
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			
			$retval = array();
			if ($result === false) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantObjectMethod::GetByAssoc($values);
			}
			return $retval;
		}
		/**
		 * Gets the instance method with the specified name, or NULL if no instance method with the specified name exists.
		 * @param string $name The name of the TenantObjectInstanceMethod to search for.
		 * @return NULL|TenantObjectInstanceMethod The instance method with the specified name, or NULL if no instance method with the specified name exists.
		 */
		public function GetInstanceMethod($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceMethods WHERE method_ObjectID = " . $this->ID . " AND method_Name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			
			return TenantObjectInstanceMethod::GetByAssoc($values);
		}
		/**
		 * Retrieves all TenantObjectInstanceMethods associated with this TenantObject.
		 * @param int $max The maximum number of TenantObjectInstanceMethods to retrieve.
		 * @return TenantObjectInstanceMethod[] array of all TenantObjectInstanceMethods associated with this TenantObject
		 */
		public function GetInstanceMethods($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceMethods WHERE method_ObjectID = " . $this->ID;
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			
			$retval = array();
			if ($result === false) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantObjectInstanceMethod::GetByAssoc($values);
			}
			return $retval;
		}
		/**
		 * Counts the number of instances of this TenantObject.
		 * @return int The number of instances of this TenantObject that reside on the tenant.
		 */
		public function CountInstances()
		{
			global $MySQL;
			$query = "SELECT COUNT(instance_ID) FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances WHERE instance_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			
			if ($result === false) return 0;
			$count = $result->num_rows;
			if ($count == 0) return 0;
			
			$values = $result->fetch_array();
			return $values[0];
		}
		/**
		 * Gets a specific instance of this TenantObject based on the given parameters.
		 * @param TenantQueryParameter[] $parameters
		 * @return NULL|TenantObjectInstance The instance of this TenantObject, or NULL if no TenantObject matching the specified criteria could be found.
		 */
		public function GetInstance($parameters)
		{
			if (!is_array($parameters))
			{
				PhoenixSNS::Log("No parameters were specified by which to extract a single instance of the object.", array
				(
					"Object" => $this->Name,
					"Property" => $propertyName
				));
				return null;
			}
			
			global $MySQL;
			
			$query = "SELECT " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances.* FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances, " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceProperties, " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstancePropertyValues";
			$result = $MySQL->query($query);
			if ($result === false)
			{
				PhoenixSNS::Log("Database error when trying to obtain an instance of an object on the tenant.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query
				));
				return null;
			}
			
			$count = $result->num_rows;
			if ($count == 0)
			{
				PhoenixSNS::Log("Could not obtain an instance of the object with the specified parameters.", array
				(
					"Object" => $this->Name,
					"Query" => $query
				));
				return null;
			}
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$inst = TenantObjectInstance::GetByAssoc($values);
				$found = true;
				foreach ($parameters as $parameter)
				{
					if ($inst->GetPropertyValue($this->GetInstanceProperty($parameter->Name)) != $parameter->Value)
					{
						$found = false;
						break;
					}
				}
				if ($found) return $inst;
			}
			return null;
		}
		/**
		 * Gets the instances of this TenantObject.
		 * @param string $max The maximum number of objects that may be retrieved by this function (or NULL to not specify a limit).
		 * @return TenantObjectInstance[] array of instances of this TenantObject
		 */
		public function GetInstances($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances WHERE instance_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			$retval = array();
			
			if ($result === false) return $retval;
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantObjectInstance::GetByAssoc($values);
			}
			return $retval;
		}
		/**
		 * Gets the title or name of this TenantObject based on whether a title is set for the specified language.
		 * @param Language $language The language to search for.
		 * @return string The title of this TenantObject in the specified language, or the name used in code to identify this TenantObject if no title is associated with the specified language.
		 */
		public function GetTitleOrName($language = null)
		{
			$title = $this->GetTitle($language);
			if ($title == null) return $this->Name;
			return $title;
		}
		/**
		 * Gets titles of this TenantObject in all available languages.
		 * @return \PhoenixSNS\Objects\TenantStringTableEntry[] array of TenantStringTableEntries which map Languages to string values
		 */
		public function GetTitles()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectTitles WHERE entry_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$retval = array();
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = new TenantStringTableEntry(Language::GetByID($values["entry_LanguageID"]), $values["entry_Value"]);
			}
			return $retval;
		}
		/**
		 * Gets the title of this TenantObject in the specified language.
		 * @param Language $language The Language for which to get this title.
		 * @return string The title of this TenantObject in the specified language or the default language if no language was specified.
		 */
		public function GetTitle($language = null)
		{
			if ($language == null) $language = Language::GetCurrent();
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectTitles WHERE entry_LanguageID = " . $language->ID . " AND entry_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return $values["entry_Value"];
		}
		/**
		 * Sets the title of this TenantObject to the specified value for the given language.
		 * @param Language $language The Language for which to set this title.
		 * @param string $value The title to set.
		 * @return boolean True if the operation completed successfully; false if the operation failed.
		 */
		public function SetTitle($language, $value)
		{
			if ($language == null) $language = Language::GetCurrent();
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectTitles WHERE entry_LanguageID = " . $language->ID . " AND entry_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$values = $result->fetch_array();
			if (is_numeric($values[0]) && $values[0] > 0)
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "TenantObjectTitles SET entry_Value = '" . $MySQL->real_escape_string($value) . "' WHERE entry_LanguageID = " . $language->ID . " AND entry_ObjectID = " . $this->ID;
				$result = $MySQL->query($query);
				if ($result === false) return false;
			}
			else
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectTitles (entry_LanguageID, entry_ObjectID, entry_Value) VALUES (" . $language->ID . ", " . $this->ID . ", '" . $MySQL->real_escape_string($value) . "')";
				$result = $MySQL->query($query);
				if ($result === false) return false;
			}
			return true;
		}
		/**
		 * Sets the titles of this TenantObject to the specified values.
		 * @param TenantStringTableEntry[] $items The language/title mappings to set.
		 * @return boolean True if all operations completed successfully; false if at least one operation failed. Some operations may have completed successfully before the failed operation.
		 */
		public function SetTitles($items)
		{
			foreach ($items as $item)
			{
				if (!$this->SetTitle($item->Language, $item->Value)) return false;
			}
			return true;
		}
		/**
		 * Gets descriptions of this TenantObject in all available languages.
		 * @return \PhoenixSNS\Objects\TenantStringTableEntry[] array of TenantStringTableEntries which map Languages to string values
		 */
		public function GetDescriptions()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectDescriptions WHERE entry_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$retval = array();
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = new TenantStringTableEntry(Language::GetByID($values["entry_LanguageID"]), $values["entry_Value"]);
			}
			return $retval;
		}
		/**
		 * Gets the description of this TenantObject in the specified language.
		 * @param Language $language The Language for which to get this description.
		 * @return string The description of this TenantObject in the specified language or the default language if no language was specified.
		 */
		public function GetDescription($language = null)
		{
			if ($language == null) return Language::GetCurrent();
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectDescriptions WHERE entry_LanguageID = " . $language->ID . " AND entry_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return $values["entry_Value"];
		}
		/**
		 * Sets the description of this TenantObject to the specified value for the given language.
		 * @param Language $language The Language for which to set this description.
		 * @param string $value The description to set.
		 * @return boolean True if the operation completed successfully; false if the operation failed.
		 */
		public function SetDescription($language, $value)
		{
			if ($language == null) $language = Language::GetCurrent();
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectDescriptions WHERE entry_LanguageID = " . $language->ID . " AND entry_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$values = $result->fetch_array();
			if (is_numeric($values[0]) && $values[0] > 0)
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "TenantObjectDescriptions SET entry_Value = '" . $MySQL->real_escape_string($value) . "' WHERE entry_LanguageID = " . $language->ID . " AND entry_ObjectID = " . $this->ID;
				$result = $MySQL->query($query);
				if ($result === false) return false;
			}
			else
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectDescriptions (entry_LanguageID, entry_ObjectID, entry_Value) VALUES (" . $language->ID . ", " . $this->ID . ", '" . $MySQL->real_escape_string($value) . "')";
				$result = $MySQL->query($query);
				if ($result === false) return false;
			}
			return true;
		}
		/**
		 * Sets the descriptions of this TenantObject to the specified values.
		 * @param TenantStringTableEntry[] $items The language/description mappings to set.
		 * @return boolean True if all operations completed successfully; false if at least one operation failed. Some operations may have completed successfully before the failed operation. 
		 */
		public function SetDescriptions($items)
		{
			foreach ($items as $item)
			{
				if (!$this->SetDescription($item->Language, $item->Value)) return false;
			}
			return true;
		}
		
		/**
		 * Deletes this TenantObject from its parent Tenant.
		 * @return boolean True if the delete operation completed successfully; false if the delete operation failed.
		 */
		public function Delete()
		{
			global $MySQL;
			
			$user = User::GetCurrent();
			if ($user == null) return false;
			
			// TODO: check user permissions before deleting object!
			
			// Relationships should prevent associated tenant data from being deleted automatically. We need to delete it manually.
			
			// First we delete the Titles string table entries for this object...
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectTitles WHERE entry_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			// ... then the Descriptions...
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectDescriptions WHERE entry_ObjectID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			// ... and finally, we can delete the object definition itself.
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjects WHERE object_ID = " . $this->ID;
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
			$json .= "{ ";
			$json .= "\"ID\": " . $this->ID . ", ";
			// $json .= "\"Tenant\": " . $this->Tenant->ToJSON() . ", ";
			// $json .= "\"Module\": " . $this->Module->ToJSON() . ", ";
			$json .= "\"ParentObject\": " . ($this->ParentObject == null ? "null" : $this->ParentObject->ToJSON()) . ", ";
			$json .= "\"Name\": \"" . \JH\Utilities::JavaScriptEncode($this->Name, "\"") . "\"";
			$json .= " }";
			return $json;
		}
	}
?>