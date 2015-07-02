<?php
	use PhoenixSNS\Objects\DataType;
	
	use PhoenixSNS\Objects\MultipleInstanceProperty;
	use PhoenixSNS\Objects\SingleInstanceProperty;
	
	use PhoenixSNS\Objects\TenantObjectProperty;
	use PhoenixSNS\Objects\TenantObjectInstanceProperty;
	use PhoenixSNS\Objects\TenantObjectInstancePropertyValue;
	use PhoenixSNS\Objects\TenantObjectMethodParameter;
	
	use PhoenixSNS\Objects\TenantEnumerationChoice;
	use PhoenixSNS\Objects\TenantStringTableEntry;
	
	$propResourceObjectName = new TenantObjectInstanceProperty("Name", DataType::GetByName("Text"));
	$objResourceObject = $tenant->CreateObject("ResourceObject",
	array
	(
		new TenantStringTableEntry($langEnglish, "Resource Object")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "Represents a single resource object (StyleSheet, Script, etc.) that is loaded in a ResourceBundle on the PhoenixSNS tenant.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		$propResourceObjectName
	),
	null, null, $propResourceObjectName);
	
	$objStyleSheet = $tenant->CreateObject("StyleSheet",
	array
	(
		new TenantStringTableEntry($langEnglish, "StyleSheet Resource Object")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "Represents a style sheet resource object, containing a set of rules that determine how particular elements appear in a page.")
	),
	array
	(
	), $objResourceObject);
	
	$objScriptResourceObject = $tenant->CreateObject("ScriptResourceObject",
	array
	(
		new TenantStringTableEntry($langEnglish, "Script Resource Object")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "Represents a script resource object, containing a single pointer to a ClientScript (executable code that can be run on the client).")
	),
	array
	(
		new TenantObjectInstanceProperty("ClientScript", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(array(), array($objClientScript)))
	), $objResourceObject);
	
	$object = $tenant->CreateObject("ResourceBundle",
	array
	(
		new TenantStringTableEntry($langEnglish, "Resource Bundle")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "Contains a bundle of resources (StyleSheets, Scripts, etc.) that are loaded in with the PhoenixSNS tenant.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Name", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("ResourceObjects", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(array(), array($objResourceObject)))
	));
?>