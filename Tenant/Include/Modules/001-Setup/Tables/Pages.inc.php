<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("Pages", "page_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 50, null, false),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("Description", "LONGTEXT", null, null, true),
		new Column("CreationTimestamp", "DATETIME", null, null, false),
		new Column("CreationUserID", "INT", null, null, false)
	));
	
	$tables[] = new Table("PageActions", "pageaction_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("PageID", "INT", null, null, false),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("URL", "LONGTEXT", null, null, false)
	));
	
	$tables[] = new Table("PageMembers", "pagemember_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("PageID", "INT", null, null, false),
		new Column("UserID", "INT", null, null, false)
	));
?>