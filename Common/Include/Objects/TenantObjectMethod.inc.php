<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	/**
	 * Represents a static method on a TenantObject.
	 * @author Michael Becker
	 */
	class TenantObjectMethod
	{
		/**
		 * The unique, incremental ID number of this TenantObjectMethod
		 * @var int
		 */
		public $ID;
		/**
		 * The TenantObject which contains this TenantObjectMethod
		 * @var TenantObject
		 */
		public $ParentObject;
		/**
		 * The name used in code to identify this TenantObjectMethod
		 * @var string
		 */
		public $Name;
		/**
		 * The executable PHP code associated with this TenantObjectMethod
		 * @var string
		 */
		public $CodeBlob;

		/**
		 * Creates a new TenantObjectMethod object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\TenantObjectMethod based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new TenantObjectMethod();
			$item->ID = $values["method_ID"];
			$item->ParentObject = TenantObject::GetByID($values["method_ObjectID"]);
			$item->Name = $values["method_Name"];
			$item->CodeBlob = $values["method_CodeBlob"];
			return $item;
		}

		/**
		 * Adds the specified namespace reference to this TenantObjectMethod.
		 * @param string $value The PHP namespace to reference.
		 * @return boolean True if the operation completed successfully; false if the operation failed.
		 */
		public function AddNamespaceReference($value)
		{
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethodNamespaceReferences (ns_MethodID, ns_Value) VALUES (";
			$query .= $this->ID . ", ";
			$query .= "'" . $MySQL->real_escape_string($value) . "'";
			$query .= ")";
			
			$result = $MySQL->query($query);
			if ($result === false)
			{
				PhoenixSNS::Log("Database error when trying to add a namespace reference to the specified object method.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query,
					"Method" => $this->Name,
					"Object" => $this->ParentObject == null ? "(null)" : $this->ParentObject->Name
				));
				return false;
			}
			return true;
		}
		/**
		 * Removes the specified namespace reference from this TenantObjectMethod.
		 * @param string $value The PHP namespace to remove.
		 * @return boolean True if the operation completed succesfully; false if the operation failed.
		 */
		public function RemoveNamespaceReference($value)
		{
			global $MySQL;
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethodNamespaceReferences WHERE ";
			$query .= "ns_MethodID = " . $this->ID . " AND ";
			$query .= "ns_Value = '" . $MySQL->real_escape_string($value) . "'";
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			return true;
		}
		
		/**
		 * Retrieves all namespace references associated with this TenantObjectMethod.
		 * @return \PhoenixSNS\Objects\TenantObjectMethodNamespaceReference[] array of namespace references associated with this TenantObjectMethod
		 */
		public function GetNamespaceReferences()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethodNamespaceReferences WHERE ns_MethodID = " . $this->ID;
			$retval = array();
			$result = $MySQL->query($query);
			if ($result === false) return $retval;
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantObjectMethodNamespaceReference::GetByAssoc($values);
			}
			return $retval;
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
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethods (method_ObjectID, method_Name, method_CodeBlob) VALUES (";
				$query .= ($this->Tenant == null ? "NULL" : $this->Tenant->ID) . ", ";
				$query .= ($this->ParentObject == null ? "NULL" : $this->ParentObject->ID) . ", ";
				$query .= "'" . $MySQL->real_escape_string($this->Name) . "', ";
				$query .= "'" . $MySQL->real_escape_string($this->CodeBlob) . "'";
				$query .= ")";
			}
			else
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethods SET ";
				$query .= "method_ObjectID = " . ($this->ParentObject == null ? "NULL" : $this->ParentObject->ID) . ", ";
				$query .= "method_Name = '" . $MySQL->real_escape_string($this->Name) . "', ";
				$query .= "method_CodeBlob = '" . $MySQL->real_escape_string($this->CodeBlob) . "'";
				$query .= " WHERE method_ID = " . $this->ID;
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
		 * Retrieves a single TenantObjectMethod with the given ID.
		 * @param int $id The ID of the TenantObjectMethod to return
		 * @return NULL|\PhoenixSNS\Objects\TenantObjectMethod The TenantObjectMethod with the given ID
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethods WHERE method_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantObjectMethod::GetByAssoc($values);
		}
		
		/**
		 * Executes this TenantObjectMethod with the specified parameters.
		 * @param array $parameters array of TenantObjectMethodParameterValue objects to pass into the TenantObjectMethod
		 */
		public function Execute($parameters = null)
		{
			if ($parameters == null) $parameters = array();
			
			$func = "";
			
			$func .= "use PhoenixSNS\\Objects\\Tenant;\n";
			$func .= "use PhoenixSNS\\Objects\\TenantObject;\n";
			$func .= "use PhoenixSNS\\Objects\\TenantQueryParameter;\n";
			
			$nses = $this->GetNamespaceReferences();
			foreach ($nses as $ns)
			{
				$func .= "use " . $ns->Value . ";\n";
			}
			
			$func .= "\$x = function(\$thisObject";
			$count = count($parameters);
			if ($count > 0) $func .= ", ";
			
			for ($i = 0; $i < $count; $i++)
			{
				$parameter = $parameters[$i];
				$func .= "\$" . $parameter->ParameterName;
				if ($i < $count - 1)
				{
					$func .= ", ";
				}
			}
			$func .= "){";
			$func .= $this->CodeBlob;
			$func .= "}; return \$x(";
			$func .= "TenantObject::GetByID(" . $this->ParentObject->ID . ")";
			if ($count > 0) $func .= ", ";
			
			for ($i = 0; $i < $count; $i++)
			{
				$parameter = $parameters[$i];
				if (is_string($parameter->Value))
				{
					$func .= ("'" . $parameter->Value . "'");
				}
				else
				{
					$func .= $parameter->Value;
				}
				if ($i < $count - 1)
				{
					$func .= ", ";
				}
			}
			$func .= ");";
			
			return eval($func);
		}
	}
	/**
	 * Represents an instance method on a TenantObject.
	 * @author Michael Becker
	 */
	class TenantObjectInstanceMethod
	{
		/**
		 * The unique, incremental ID number of this TenantObjectInstanceMethod
		 * @var int
		 */
		public $ID;
		/**
		 * The TenantObject which contains this TenantObjectInstanceMethod
		 * @var TenantObject
		 */
		public $ParentObject;
		/**
		 * The name used in code to identify this TenantObjectInstanceMethod
		 * @var string
		 */
		public $Name;
		/**
		 * The executable PHP code associated with this TenantObjectInstanceMethod
		 * @var string
		 */
		public $CodeBlob;
		
		public static function GetByAssoc($values)
		{
			$item = new TenantObjectInstanceMethod();
			$item->ID = $values["method_ID"];
			$item->ParentObject = TenantObject::GetByID($values["method_ObjectID"]);
			$item->Name = $values["method_Name"];
			$item->CodeBlob = $values["method_CodeBlob"];
			return $item;
		}
		
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceMethods WHERE method_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantObjectInstanceMethod::GetByAssoc($values);
		}

		public function AddNamespaceReference($value)
		{
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceMethodNamespaceReferences (ns_MethodID, ns_Value) VALUES (";
			$query .= $this->ID . ", ";
			$query .= "'" . $MySQL->real_escape_string($value) . "'";
			$query .= ")";
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			return true;
		}
		public function RemoveNamespaceReference($value)
		{
			global $MySQL;
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceMethodNamespaceReferences WHERE ";
			$query .= "ns_MethodID = " . $this->ID . " AND ";
			$query .= "ns_Value = '" . $MySQL->real_escape_string($value) . "'";
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			return true;
		}
		public function GetNamespaceReferences()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstanceMethodNamespaceReferences WHERE ns_MethodID = " . $this->ID;
			$retval = array();
			$result = $MySQL->query($query);
			if ($result === false) return $retval;
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = TenantObjectMethodNamespaceReference::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function Execute($parameters)
		{
			if ($parameters == null) $parameters = array();
			
			$func = "";
			$nses = $this->GetNamespaceReferences();
			foreach ($nses as $ns)
			{
				$func .= "use " . $ns->Value . ";\n";
			}
			
			$func .= "\$x = function(";
			$count = count($parameters);
			for ($i = 0; $i < $count; $i++)
			{
				$parameter = $parameters[$i];
				$func .= "\$" . $parameter->ParameterName;
				if ($i < $count - 1)
				{
					$func .= ", ";
				}
			}
			$func .= "){";
			$func .= $this->CodeBlob;
			$func .= "}; return \$x(";
			for ($i = 0; $i < $count; $i++)
			{
				$parameter = $parameters[$i];
				if (is_string($parameter->Value))
				{
					$func .= ("'" . $parameter->Value . "'");
				}
				else
				{
					$func .= $parameter->Value;
				}
				if ($i < $count - 1)
				{
					$func .= ", ";
				}
			}
			$func .= ");";
			return eval($func);
		}
	}
	/**
	 * A parameter passed into a TenantObjectMethod
	 * @author Michael Becker
	 */
	class TenantObjectMethodParameter
	{
		/**
		 * The name of this parameter
		 * @var string
		 */
		public $Name;
		/**
		 * The value used for this parameter when no value is specified in the method call
		 * @var mixed
		 */
		public $DefaultValue;
		
		/**
		 * Creates a new TenantObjectMethodParameter with the specified parameters
		 * @param string $name The name of this parameter
		 * @param mixed $defaultValue The value used for this parameter when no value is specified in the method call
		 */
		public function __construct($name, $defaultValue = null)
		{
			$this->Name = $name;
			$this->DefaultValue = $defaultValue;
		}
	}
	/**
	 * A value passed into a TenantObjectMethodParameter when calling a TenantObjectMethod.
	 * @author Michael Becker
	 */
	class TenantObjectMethodParameterValue
	{
		/**
		 * The name of the parameter with which this value is associated.
		 * @var string
		 */
		public $ParameterName;
		/**
		 * The value of this parameter.
		 * @var mixed
		 */
		public $Value;
		/**
		 * Creates a new TenantObjectMethodParameterValue with the specified parameters.
		 * @param string $parameterName The name of the parameter with which this value is associated.
		 * @param mixed $value The value of this parameter.
		 */
		public function __construct($parameterName, $value = null)
		{
			$this->ParameterName = $parameterName;
			$this->Value = $value;
		}
	}
	/**
	 * A PHP namespace referenced by a TenantObjectMethod.
	 * @author Michael Becker
	 */
	class TenantObjectMethodNamespaceReference
	{
		/**
		 * The unique, incremental ID number of this TenantObjectMethodNamespaceReference
		 * @var int
		 */
		public $ID;
		/**
		 * The name of the PHP namespace to reference
		 * @var string
		 */
		public $Value;
		/**
		 * Creates a new TenantObjectMethodNamespaceReference object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\TenantObjectMethodNamespaceReference based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new TenantObjectMethodNamespaceReference();
			$item->ID = $values["ns_ID"];
			$item->Value = $values["ns_Value"];
			return $item;
		}
	}
?>