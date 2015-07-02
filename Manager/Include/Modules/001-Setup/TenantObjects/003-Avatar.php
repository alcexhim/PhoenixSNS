<?php
	use PhoenixSNS\Objects\DataType;
	
	use PhoenixSNS\Objects\MultipleInstanceProperty;
	use PhoenixSNS\Objects\SingleInstanceProperty;
	
	use PhoenixSNS\Objects\TenantObjectProperty;
	use PhoenixSNS\Objects\TenantObjectInstanceProperty;
	use PhoenixSNS\Objects\TenantObjectInstancePropertyValue;
	use PhoenixSNS\Objects\TenantObjectMethodParameter;
	
	use PhoenixSNS\Objects\TenantStringTableEntry;
	
	$objAvatar2DSlice = $tenant->CreateObject("Avatar2DSlice",
	array
	(
		new TenantStringTableEntry($langEnglish, "Avatar 2D Slice")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A movable portion (also known as a 'bone') of the two-dimensional avatar.")
	),
	array
	(
		new TenantObjectInstanceProperty("Title", DataType::GetByName("String")),
		// TODO: figure out how to self-reference objects!
		// new TenantObjectInstanceProperty("ParentSlice", DataType::GetByName("SingleInstance"), DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($tenant->GetObject("Avatar2DSlice")))),
		new TenantObjectInstanceProperty("Left", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("Top", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("Width", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("Height", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("TransformOriginLeft", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("TransformOriginTop", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("ZIndex", DataType::GetByName("Number"))
	));
	$objAvatar2DView = $tenant->CreateObject("Avatar2DView",
	array
	(
		new TenantStringTableEntry($langEnglish, "Avatar 2D View")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A particular view of the two-dimensional avatar. Each avatar item should have at least one image that corresponds to at least one view. Therefore if you have a front and side view, you should have at least two images (front and side) for each item, or one image associated with both views.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Title", DataType::GetByName("String")),
		new TenantObjectInstanceProperty("Width", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("Height", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("Slices", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objAvatar2DSlice)))
	));
	$objAvatar2DBase = $tenant->CreateObject("Avatar2DBase",
	array
	(
		new TenantStringTableEntry($langEnglish, "Avatar 2D Base")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "The two-dimensional base upon which all avatar items are overlaid. Bases can have multiple views, which allow different images per view for each item.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Author", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($tenant->GetObject("User")))),
		new TenantObjectInstanceProperty("Title", DataType::GetByName("String")),
		new TenantObjectInstanceProperty("Views", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objAvatar2DView)))
	));
	$objAvatar2DItemImage = $tenant->CreateObject("Avatar2DItemImage",
	array
	(
		new TenantStringTableEntry($langEnglish, "Avatar 2D Item Image")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "An image for an item that can be displayed on a two-dimensional avatar.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Slice", DataType::GetByName("SingleInstance"), new MultipleInstanceProperty(null, array($objAvatar2DSlice)), "The slice to which the item image will be attached."),
		new TenantObjectInstanceProperty("FileNameExtension", DataType::GetByName("String")),
		new TenantObjectInstanceProperty("Left", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("Top", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("Width", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("Height", DataType::GetByName("Measurement")),
		new TenantObjectInstanceProperty("ZIndex", DataType::GetByName("Number"))
	));
	$objAvatar2DItem = $tenant->CreateObject("Avatar2DItem",
	array
	(
		new TenantStringTableEntry($langEnglish, "Avatar 2D Item")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "An item that can be displayed on a two-dimensional avatar.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Author", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($tenant->GetObject("User")))),
		new TenantObjectInstanceProperty("Title", DataType::GetByName("String")),
		new TenantObjectInstanceProperty("Images", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objAvatar2DItemImage)))
	));
	
	$objAvatar2D = $tenant->CreateObject("Avatar2D",
	array
	(
		new TenantStringTableEntry($langEnglish, "Avatar 2D")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Base", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($objAvatar2DBase))),
		new TenantObjectInstanceProperty("View", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($objAvatar2DView))),
		new TenantObjectInstanceProperty("Title"),
		new TenantObjectInstanceProperty("Content"),
		new TenantObjectInstanceProperty("PostType"), // single instance of a PostType object
		new TenantObjectInstanceProperty("CreationDate") // DateTime
	));
?>