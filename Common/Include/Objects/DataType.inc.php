<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	/**
	 * Associates various functionality (encoding, decoding, and rendering) with a particular type of data.
	 * @author Michael Becker
	 *
	 */
	class DataType
	{
		/**
		 * The unique, incremental ID number of this DataType.
		 * @var int
		 */
		public $ID;
		/**
		 * The language-agnostic name used in code and API calls to identify this DataType.
		 * @var string
		 */
		public $Name;
		/**
		 * A short description of this DataType.
		 * @var string
		 */
		public $Description;
		
		/**
		 * A blob of PHP code that is executed when runtime data needs to be converted into stored data.
		 * @var string
		 */
		public $EncoderCodeBlob;
		/**
		 * A blob of PHP code that is executed when stored data needs to be converted into runtime data.
		 * @var string
		 */
		public $DecoderCodeBlob;
		/**
		 * A blob of PHP code that is executed when a field with this DataType needs to render in a
		 * \WebFX\Controls\ListView.
		 * @var string
		 */
		public $ColumnRendererCodeBlob;
		/**
		 * A blob of PHP code that is executed when a field with this DataType needs to render in a
		 * \WebFX\Controls\FormView.
		 * @var string
		 */
		public $EditorRendererCodeBlob;
		
		/**
		 * Counts all of the available DataTypes.
		 * @return int The number of DataTypes available on this server.
		 */
		public static function Count()
		{
			global $MySQL;
			$query = "SELECT COUNT(datatype_ID) FROM " . System::$Configuration["Database.TablePrefix"] . "DataTypes";
			$result = $MySQL->query($query);
			if ($result === false) return 0;
			$values = $result->fetch_array();
			return $values[0];
		}
		/**
		 * Creates a new DataType object based on the given values from the database.
		 * @param array $values
		 * @return \PhoenixSNS\Objects\DataType based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new DataType();
			$item->ID = $values["datatype_ID"];
			$item->Name = $values["datatype_Name"];
			$item->Description = $values["datatype_Description"];
			
			$item->EncoderCodeBlob = $values["datatype_EncoderCodeBlob"];
			$item->DecoderCodeBlob = $values["datatype_DecoderCodeBlob"];
			$item->ColumnRendererCodeBlob = $values["datatype_ColumnRendererCodeBlob"];
			$item->EditorRendererCodeBlob = $values["datatype_EditorRendererCodeBlob"];
			return $item;
		}
		/**
		 * Retrieves all DataTypes
		 * @param int $max The maximum number of DataTypes to return
		 * @return \PhoenixSNS\Objects\DataType[]
		 */
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "DataTypes";
			if (is_numeric($max))
			{
				$query .= " LIMIT " . $max;
			}
			$result = $MySQL->query($query);
			$retval = array();
			if ($result === false) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = DataType::GetByAssoc($values);
			}
			return $retval;
		}

		/**
		 * Retrieves a single DataType with the given ID.
		 * @param int $id The ID of the DataType to return
		 * @return NULL|\PhoenixSNS\Objects\DataType The DataType with the given ID, or null if no DataType with the given ID was found or the given ID was invalid
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "DataTypes WHERE datatype_ID = " . $id;
			
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return DataType::GetByAssoc($values);
		}
		/**
		 * Retrieves a single DataType with the given name
		 * @param string $name The name used in code to identify this DataType
		 * @return NULL|\PhoenixSNS\Objects\DataType The DataType with the given name, or null if no DataType with the given name was found
		 */
		public static function GetByName($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "DataTypes WHERE datatype_Name = '" . $MySQL->real_escape_string($name) . "'";
			
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count == 0)
			{
				PhoenixSNS::Log("No data type with the specified name was found.", array
				(
					"DataType" => $name
				));
				return null;
			}
			
			$values = $result->fetch_assoc();
			return DataType::GetByAssoc($values);
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
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "DataTypes (datatype_Name, datatype_Description, datatype_EncoderCodeBlob, datatype_DecoderCodeBlob, datatype_ColumnRendererCodeBlob, datatype_EditorRendererCodeBlob) VALUES (";
				$query .= "'" . $MySQL->real_escape_string($this->Name) . "', ";
				$query .= "'" . $MySQL->real_escape_string($this->Description) . "', ";
				$query .= ($this->EncoderCodeBlob != null ? ("'" . $MySQL->real_escape_string($this->EncoderCodeBlob) . "'") : "NULL") . ", ";
				$query .= ($this->DecoderCodeBlob != null ? ("'" . $MySQL->real_escape_string($this->DecoderCodeBlob) . "'") : "NULL") . ", ";
				$query .= ($this->ColumnRendererCodeBlob != null ? ("'" . $MySQL->real_escape_string($this->ColumnRendererCodeBlob) . "'") : "NULL") . ", ";
				$query .= ($this->EditorRendererCodeBlob != null ? ("'" . $MySQL->real_escape_string($this->EditorRendererCodeBlob) . "'") : "NULL");
				$query .= ")";
			}
			else
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "DataTypes SET ";
				$query .= "datatype_Name = '" . $MySQL->real_escape_string($this->Name) . "', ";
				$query .= "datatype_Description = '" . $MySQL->real_escape_string($this->Description) . "', ";
				$query .= "datatype_EncoderCodeBlob = " . ($this->EncoderCodeBlob != null ? ("'" . $MySQL->real_escape_string($this->EncoderCodeBlob) . "'") : "NULL") . ", ";
				$query .= "datatype_DecoderCodeBlob = " . ($this->DecoderCodeBlob != null ? ("'" . $MySQL->real_escape_string($this->DecoderCodeBlob) . "'") : "NULL") . ", ";
				$query .= "datatype_ColumnRendererCodeBlob = " . ($this->ColumnRendererCodeBlob != null ? ("'" . $MySQL->real_escape_string($this->ColumnRendererCodeBlob) . "'") : "NULL") . ", ";
				$query .= "datatype_EditorRendererCodeBlob = " . ($this->EditorRendererCodeBlob != null ? ("'" . $MySQL->real_escape_string($this->EditorRendererCodeBlob) . "'") : "NULL");
				$query .= " WHERE datatype_ID = " . $this->ID;
			}
			
			$MySQL->query($query);
			if ($MySQL->errno != 0)
			{
				echo($MySQL->error);
				die();
				return false;
			}
			
			if ($this->ID == null)
			{
				$this->ID = $MySQL->insert_id;
			}
			return true;
		}
		/**
		 * Encodes the specified runtime object value into a stored database value.
		 * @param mixed $value The value to encode
		 * @return string A string representation of the given value
		 */
		public function Encode($value)
		{
			if ($this->EncoderCodeBlob == null) return $value;
			$q = '';
			$q .= 'use PhoenixSNS\Objects\MultipleInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\SingleInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\TenantObject; ';
			$q .= 'use PhoenixSNS\Objects\TenantObjectInstance; ';
			$q .= 'use PhoenixSNS\Objects\TenantObjectInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\PhoenixSNS; ';
			$q .= '$x = function($input) { ' . $this->EncoderCodeBlob . ' };';
			// trigger_error("calling EncoderCodeBlob on DataType '" . $this->Name . "'", E_USER_NOTICE);
			eval($q);
			return $x($value);
		}
		/**
		 * Decodes the specified stored database value into a runtime object value.
		 * @param string $value The string representation of the value to decode
		 * @return mixed The original runtime object data
		 */
		public function Decode($value)
		{
			if ($this->DecoderCodeBlob == null) return $value;
			$q = '';
			$q .= 'use PhoenixSNS\Objects\MultipleInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\SingleInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\TenantObject; ';
			$q .= 'use PhoenixSNS\Objects\TenantObjectInstance; ';
			$q .= 'use PhoenixSNS\Objects\TenantObjectInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\PhoenixSNS; ';
			$q .= '$x = function($input) { ' . $this->DecoderCodeBlob . ' };';
			// trigger_error("calling DecoderCodeBlob on DataType '" . $this->Name . "'", E_USER_NOTICE);
			eval($q);
			return $x($value);
		}
		/**
		 * Renders the given value in a column
		 * @param mixed $value The value to render
		 */
		public function RenderColumn($value)
		{
			if ($this->ColumnRendererCodeBlob == null) return;
			$q = '';
			$q .= 'use PhoenixSNS\Objects\MultipleInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\SingleInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\TenantObject; ';
			$q .= 'use PhoenixSNS\Objects\TenantObjectInstance; ';
			$q .= 'use PhoenixSNS\Objects\TenantObjectInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\PhoenixSNS; ';
			$q .= '$x = function($input) { ' . $this->ColumnRendererCodeBlob . ' };';
			// trigger_error("calling ColumnRendererCodeBlob on DataType '" . $this->Name . "'", E_USER_NOTICE);
			eval($q);
			$x($value);
		}
		/**
		 * Renders the given value in a \WebFX\Controls\FormView or other editor
		 * @param mixed $value The value to render
		 * @param string $name The name to use for the associated input field (if applicable)
		 */
		public function RenderEditor($value, $name)
		{
			if ($this->EditorRendererCodeBlob == null) return;
			
			$q = '';
			$q .= 'use PhoenixSNS\Objects\MultipleInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\SingleInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\TenantObject; ';
			$q .= 'use PhoenixSNS\Objects\TenantObjectInstance; ';
			$q .= 'use PhoenixSNS\Objects\TenantObjectInstanceProperty; ';
			$q .= 'use PhoenixSNS\Objects\PhoenixSNS; ';
			$q .= '$x = function($input, $name) { ' . $this->EditorRendererCodeBlob . ' };';
			// trigger_error("calling EditorRendererCodeBlob on DataType '" . $this->Name . "'", E_USER_NOTICE);
			
			eval($q);
			
			// if $x is not set, then there must have been an error in parsing so stop rendering
			if (!isset($x)) return;
			
			$x($value, $name);
		}
	}
?>