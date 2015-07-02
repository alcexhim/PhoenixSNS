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
	
	$tenant = Tenant::GetByID(1);
	
	$objScriptLanguage = $tenant->CreateObject("ScriptLanguage",
	array
	(
		new TenantStringTableEntry($langEnglish, "Script Language")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A programming language used to write scripts.")
	),
	array
	(
		new TenantObjectInstanceProperty("Name", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("ContentType", DataType::GetByName("Text"))
	));
	
	$objClientScriptLanguage = $tenant->CreateObject("ClientScriptLanguage",
	array
	(
		new TenantStringTableEntry($langEnglish, "Client Script Language")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A programming language used to write scripts to be run on the client.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Name", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("ContentType", DataType::GetByName("Text"))
	), $objScriptLanguage,
	array
	(
		array
		(
			new TenantObjectInstancePropertyValue(TenantObjectInstanceProperty::GetByName("Name"), "JavaScript"),
			new TenantObjectInstancePropertyValue(TenantObjectInstanceProperty::GetByName("ContentType"), "text/javascript")
		),
		array
		(
			new TenantObjectInstancePropertyValue(TenantObjectInstanceProperty::GetByName("Name"), "VBScript"),
			new TenantObjectInstancePropertyValue(TenantObjectInstanceProperty::GetByName("ContentType"), "text/vbscript")
		)
	));
	$objServerScriptLanguage = $tenant->CreateObject("ServerScriptLanguage",
	array
	(
		new TenantStringTableEntry($langEnglish, "Server Script Language")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A programming language used to write scripts to be run on the server.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Name", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("ContentType", DataType::GetByName("Text"))
	), $objScriptLanguage,
	array
	(
		array
		(
			new TenantObjectInstancePropertyValue(TenantObjectInstanceProperty::GetByName("Name"), "PHP"),
			new TenantObjectInstancePropertyValue(TenantObjectInstanceProperty::GetByName("ContentType"), "application/x-php")
		)
	));
	
	$objScript = $tenant->CreateObject("Script",
	array
	(
		new TenantStringTableEntry($langEnglish, "Script")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A code blob that can be used in various scriptable areas.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Name", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("Description", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("Language", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($objScriptLanguage))),
		new TenantObjectInstanceProperty("Content", DataType::GetByName("CodeBlob"))
	));
	
	$objClientScript = $tenant->CreateObject("ClientScript",
	array
	(
		new TenantStringTableEntry($langEnglish, "Client Script")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A code blob that can be used in various scriptable areas on the client.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Name", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("Description", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("Language", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($objClientScriptLanguage))),
		new TenantObjectInstanceProperty("Content", DataType::GetByName("CodeBlob"))
	), $objScript);
	
	$objServerScript = $tenant->CreateObject("ServerScript",
	array
	(
		new TenantStringTableEntry($langEnglish, "Server Script")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A code blob that can be used in various scriptable areas on the server.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Name", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("Description", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("Language", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($objServerScriptLanguage))),
		new TenantObjectInstanceProperty("Content", DataType::GetByName("CodeBlob"))
	), $objScript);
?>