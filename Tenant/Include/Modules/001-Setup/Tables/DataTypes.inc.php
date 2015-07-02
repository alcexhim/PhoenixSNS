<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("DataTypes", "datatype_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("Title", "VARCHAR", 256, null, true),
		new Column("Description", "LONGTEXT", null, null, true),
		new Column("PerceivedType", "VARCHAR", 256, null, true)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("Title", "String"),
			new RecordColumn("Description", "Stores an array of UTF-8 characters."),
			new RecordColumn("PerceivedType", "string")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Number"),
			new RecordColumn("Description", "Stores numeric data."),
			new RecordColumn("PerceivedType", "number")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Boolean"),
			new RecordColumn("Description", "Stores a true/false or yes/no value."),
			new RecordColumn("PerceivedType", "boolean")
		))
	));
?>