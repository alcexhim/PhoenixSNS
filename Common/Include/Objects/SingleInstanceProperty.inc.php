<?php
	namespace PhoenixSNS\Objects;
	
	/**
	 * The in-memory representation of a single-instance property for the
	 * SingleInstance DataType that stores TenantObjectInstances.
	 * @author Michael Becker
	 * @see DataType
	 * @see MultipleInstanceProperty
	 * @see TenantObjectInstance
	 */
	class SingleInstanceProperty
	{
		/**
		 * The TenantObjectInstance associated with this SingleInstanceProperty.
		 * @var TenantObjectInstance
		 */
		private $mvarInstance;
		/**
		 * Gets the TenantObjectInstance associated with this SingleInstanceProperty.
		 * @return TenantObjectInstance
		 */
		public function GetInstance()
		{
			return $this->mvarInstance;
		}
		/**
		 * Sets the TenantObjectInstance associated with this SingleInstanceProperty.
		 * @param TenantObjectInstance $value The instance to set.
		 * @return boolean True if the value is valid for this property; false otherwise.
		 */
		public function SetInstance($value)
		{
			foreach ($this->ValidObjects as $obj)
			{
				if ($obj->ID != $value->ParentObject->ID) return false;
			}
			$this->mvarInstance = $value;
			return true;
		}
		
		/**
		 * Array of TenantObjects that are valid objects for instances to be
		 * stored in this property.
		 * @var TenantObject[]
		 */
		public $ValidObjects;
		
		/**
		 * Creates a new SingleInstanceProperty with the specified parameters.
		 * @param TenantObjectInstance $instance The TenantObjectInstance associated with this SingleInstanceProperty.
		 * @param TenantObject[] $validObjects Array of TenantObjects that are valid objects for instances to be stored in this property.
		 */
		public function __construct($instance = null, $validObjects = null)
		{
			$this->mvarInstance = $instance;
			
			if ($validObjects == null) $validObjects = array();
			$this->ValidObjects = $validObjects;
		}
	}
?>