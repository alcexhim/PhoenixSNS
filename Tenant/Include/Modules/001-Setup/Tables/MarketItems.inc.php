<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("MarketItems", "marketitem_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ItemID", "INT", null, null, false),
		new Column("BeginTimestamp", "DATETIME", null, null, false),
		new Column("EndTimestamp", "DATETIME", null, null, false)
	));
	
	$tables[] = new Table("MarketItemResources", "marketitemresource_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ItemID", "INT", null, null, false),
		new Column("ResourceTypeID", "INT", null, null, false),
		new Column("ResourceCount", "INT", null, null, false)
	));
?>