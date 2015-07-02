<?php
	use PhoenixSNS\Objects\DataType;
	
	use PhoenixSNS\Objects\MultipleInstanceProperty;
	use PhoenixSNS\Objects\SingleInstanceProperty;
	
	use PhoenixSNS\Objects\TenantProperty;
	
	use PhoenixSNS\Objects\TenantObjectProperty;
	use PhoenixSNS\Objects\TenantObjectInstanceProperty;
	use PhoenixSNS\Objects\TenantObjectInstancePropertyValue;
	use PhoenixSNS\Objects\TenantObjectMethodParameter;
	use PhoenixSNS\Objects\TenantQueryParameter;
	
	use PhoenixSNS\Objects\TenantEnumerationChoice;
	
	// Set up the basic configuration
	$tenant->CreateProperty(new TenantProperty("ApplicationTitle", DataType::GetByName("Text"), "The title of the social network. This is displayed in various areas around the site.", "PhoenixSNS"));
	$tenant->CreateProperty(new TenantProperty("ApplicationDescription", DataType::GetByName("Text"), "A short description of your social network. This will appear in search results and other areas that use the HTML META description attribute.", "A virtual world social network that anyone can join, powered by PhoenixSNS."));
	$tenant->CreateProperty(new TenantProperty("ApplicationSlogan", DataType::GetByName("Text"), "A short attention-grabbing slogan for your social network.", "Connect. Build. Explore."));
	
	// Install the resource bundles
	$objResourceBundle = $tenant->GetObject("ResourceBundle");
	$instRBCommon = $objResourceBundle->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objResourceBundle->GetInstanceProperty("Name"), "Common")
	));
	$instRBDefault = $objResourceBundle->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objResourceBundle->GetInstanceProperty("Name"), "Default")
	));
	
	$tenant->CreateProperty(new TenantProperty
	(
		"ResourceBundles", DataType::GetByName("MultipleInstance"), "The resource bundles that are loaded with this tenant.", new MultipleInstanceProperty
		(
			array($instRBDefault),
			array($objResourceBundle)
		)
	));
	
?>