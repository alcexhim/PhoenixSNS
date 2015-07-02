<?php
	use PhoenixSNS\Objects\DataType;
	
	use PhoenixSNS\Objects\MultipleInstanceProperty;
	use PhoenixSNS\Objects\SingleInstanceProperty;
	
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantObjectProperty;
	use PhoenixSNS\Objects\TenantObjectInstanceProperty;
	use PhoenixSNS\Objects\TenantObjectInstancePropertyValue;
	use PhoenixSNS\Objects\TenantObjectMethodParameter;
	use PhoenixSNS\Objects\TenantStringTableEntry;
	
	use PhoenixSNS\Objects\TenantEnumerationChoice;
	
	$object = $tenant->CreateObject("Place",
	array
	(
		new TenantStringTableEntry($langEnglish, "Place")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A place in the virtual world.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Title"),
		new TenantObjectInstanceProperty("URL"),
		new TenantObjectInstanceProperty("BeginTimestamp"),
		new TenantObjectInstanceProperty("EndTimestamp")
	));
	
	$object->CreateMethod("GetActiveUsers", array
	(
		new TenantObjectMethodParameter("username"),
		new TenantObjectMethodParameter("password")
	),
	
	// code goes here... you cannot "use" namespaces here; please put them in NamespaceReferences!!!
<<<'EOD'
$tenant = Tenant::GetCurrent();
$tobjUser = $tenant->GetObject("User");
$retval = array();

return $retval;
EOD
, "Returns a MultipleInstanceProperty containing all the Users that are currently inhabiting this Place.", array
(
	// using statements go here
	'PhoenixSNS\Objects\Tenant',
	'PhoenixSNS\Objects\TenantObjectMethodParameterValue',
	'PhoenixSNS\Objects\TenantQueryParameter'
));
	
?>