<?php
	namespace PhoenixSNS\Objects;
	
	/**
	 * A parameter used in a tenant query.
	 * @author Michael Becker
	 */
	class TenantQueryParameter
	{
		/**
		 * The name of the object to search for.
		 * @var string 
		 */
		public $Name;
		/**
		 * The value of the object to search for.
		 * @var string
		 */
		public $Value;
		
		/**
		 * Creates a new TenantQueryParameter with the given parameters.
		 * @param string $name The name of the object to search for.
		 * @param string $value The value of the object to search for.
		 */
		public function __construct($name, $value)
		{
			$this->Name = $name;
			$this->Value = $value;
		}
	}
?>