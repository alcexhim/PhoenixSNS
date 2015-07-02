<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("Objects", "object_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("ModuleID", "INT", null, null, true), // if set, object is only visible/referencable when specified module is enabled
		new Column("ParentObjectID", "INT", null, null, true), // if set, object inherits its permissions, properties, and other attributes from given object
		new Column("Title", "VARCHAR", 256, null, false),
		new Column("Description", "LONGTEXT", null, null, true)
	),
	array
	(
	));
	
	// Available static properties for the objects.
	$tables[] = new Table("ObjectProperties", "property_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("ObjectID", "INT", null, null, true),
		new Column("Title", "VARCHAR", 256, null, true),
		new Column("DataTypeID", "INT", null, null, true),
		new Column("DefaultValue", "LONGTEXT", null, null, true)
	),
	array
	(
	));
?>