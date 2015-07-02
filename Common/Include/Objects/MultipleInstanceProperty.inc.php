<?php
	namespace PhoenixSNS\Objects;
	
	/**
	 * The in-memory representation of a multiple-instance property for the
	 * MultipleInstance DataType that stores TenantObjectInstances.
	 * @author Michael Becker
	 */
	class MultipleInstanceProperty
	{
		/**
		 * The instances associated with this MultipleInstanceProperty.
		 * @var TenantObjectInstance[]
		 */
		private $mvarInstances;
		/**
		 * Gets the instances associated with this MultipleInstanceProperty.
		 * @return TenantObjectInstance[] The instances associated with this MultipleInstanceProperty.
		 */
		public function GetInstances()
		{
			return $this->mvarInstances;
		}
		/**
		 * Adds an instance to this MultipleInstanceProperty. If the ValidObjects array is not empty, the instance must be of an object specified in this array.
		 * @param TenantObjectInstance $value The instance to add to this MultipleInstanceProperty.
		 * @return boolean True if the value is valid for this property; false otherwise.
		 */
		public function AddInstance($value)
		{
			if ($value == null) return false;
			foreach ($this->ValidObjects as $obj)
			{
				if ($obj->ID != $value->ParentObject->ID) return false;
			}
			$this->mvarInstances[] = $value;
			return true;
		}
		/**
		 * Removes all instances from this MultipleInstanceProperty.
		 */
		public function ClearInstances()
		{
			$this->mvarInstances = array();
		}
		/**
		 * Counts all instances associated with this MultipleInstanceProperty.
		 * @return int The number of instances associated with this MultipleInstanceProperty.
		 */
		public function CountInstances()
		{
			return count($this->mvarInstances);
		}

		/**
		 * Array of TenantObjects that are valid objects for instances to be
		 * stored in this property.
		 * @var TenantObject[]
		 */
		public $ValidObjects;

		/**
		 * Creates a new MultipleInstanceProperty with the specified parameters.
		 * @param TenantObjectInstance[] $instances The instances associated with this MultipleInstanceProperty.
		 * @param TenantObject[] $validObjects Array of TenantObjects that are valid objects for instances to be stored in this property.
		 */
		public function __construct($instances = null, $validObjects = null)
		{
			if ($instances == null) $instances = array();
			$this->mvarInstances = $instances;
			
			if ($validObjects == null) $validObjects = array();
			$this->ValidObjects = $validObjects;
		}
	}
?>