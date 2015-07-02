<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("MarketResourceTypes", "resourcetype_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 50, null, false),
		new Column("TitleSingular", "VARCHAR", 100, null, false),
		new Column("TitlePlural", "VARCHAR", 100, null, false)
	));
	$tables[] = new Table("MarketInitialResources", "initialresource_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ResourceTypeID", "INT", null, null, false),
		new Column("Amount", "INT", null, null, 0, false)
	));
?>