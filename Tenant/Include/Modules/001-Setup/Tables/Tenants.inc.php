<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("Tenants", "tenant_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("URL", "VARCHAR", 30, null, true),
		new Column("Description", "LONGTEXT", null, null, false),
		new Column("Status", "INT", null, null, true),
		new Column("TypeID", "INT", null, null, true),
		new Column("PaymentPlanID", "INT", null, null, true),
		new Column("BeginTimestamp", "DATETIME", null, null, false),
		new Column("EndTimestamp", "DATETIME", null, null, false)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("URL", "default"),
			new RecordColumn("Description", "The default tenant for PhoenixSNS."),
			new RecordColumn("Status", 1 /*TenantStatus::Active*/),
			new RecordColumn("TypeID", 1 /*Default*/),
			new RecordColumn("PaymentPlanID", 0 /*no charge*/),
			new RecordColumn("BeginTimestamp", ColumnValue::Now),
			new RecordColumn("EndTimestamp", ColumnValue::Undefined)
		))
	));
	
	$tables[] = new Table("TenantProperties", "property_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("Title", "VARCHAR", 256, null, true),
		new Column("Description", "LONGTEXT", null, null, false),
		new Column("DataTypeID", "INT", null, null, false),
		new Column("DefaultValue", "LONGTEXT", null, null, true)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("Title", "Application Name"),
			new RecordColumn("Description", "The name of the social network. This is displayed in various areas around the site."),
			new RecordColumn("DataTypeID", 1 /*PropertyDataType::String*/),
			new RecordColumn("DefaultValue", "PhoenixSNS")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Application Description"),
			new RecordColumn("Description", "A short description of your social network. This will appear in search results and other areas that use the HTML META description attribute."),
			new RecordColumn("DataTypeID", 1 /*PropertyDataType::String*/),
			new RecordColumn("DefaultValue", "A virtual world social network that anyone can join, powered by PhoenixSNS.")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Application Slogan"),
			new RecordColumn("Description", "A short attention-grabbing slogan for your social network."),
			new RecordColumn("DataTypeID", 1 /*PropertyDataType::String*/),
			new RecordColumn("DefaultValue", "Connect. Build. Explore.")
		))
	));
?>