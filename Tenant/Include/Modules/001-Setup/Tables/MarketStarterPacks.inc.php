<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	new Table("MarketStarterPacks", "starterpack_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("CreationUserID", "INT", null, null, false),
		new Column("CreationTimestamp", "DATETIME", null, null, false)
	));
?>