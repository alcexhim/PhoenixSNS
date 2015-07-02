<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	use PhoenixSNS\Objects\UserPresenceStatus;
	use PhoenixSNS\Objects\UserProfileVisibility;
	
	$tables[] = new Table("UserLogins", "userlogin_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("UserID", "INT", null, null, false),
		new Column("IPAddress", "VARCHAR", 40, null, false),
		new Column("Timestamp", "DATETIME", null, null, false)
	));
?>