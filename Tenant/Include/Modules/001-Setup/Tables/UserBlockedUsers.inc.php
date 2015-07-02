<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	use PhoenixSNS\Objects\UserPresenceStatus;
	use PhoenixSNS\Objects\UserProfileVisibility;
	
	$tables[] = new Table("UserBlockedUsers", "blocklist_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("UserID", "INT", null, null, false),
		new Column("BlockedUserID", "INT", null, null, false),
		new Column("BlockTimestamp", "DATETIME", null, null, false),
		new Column("BlockReason", "LONGTEXT", null, null, false),
		new Column("BlockReasonVisible", "INT", null, 0, false)
	));
?>