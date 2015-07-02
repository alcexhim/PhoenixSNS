<?php
	namespace PhoenixSNS\Objects;
	
	/**
	 * An entry in the Tenant String Table for a given language.
	 * @author Michael Becker
	 */
	class TenantStringTableEntry
	{
		/**
		 * The language associated with this TenantStringTableEntry.
		 * @var Language
		 */
		public $Language;
		/**
		 * The value associated with this TenantStringTableEntry.
		 * @var string
		 */
		public $Value;
		
		/**
		 * Creates a new TenantStringTableEntry with the given language and value.
		 * @param Language $language The language to associate with this TenantStringTableEntry.
		 * @param string $value The value to associate with this TenantStringTableEntry.
		 */
		public function __construct($language, $value)
		{
			$this->Language = $language;
			$this->Value = $value;
		}
	}
?>