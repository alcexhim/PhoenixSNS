<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tblUsers = new Table("Users", "user_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("LoginID", "VARCHAR", 50, null, false),
		new Column("PasswordHash", "VARCHAR", 128, null, false),
		new Column("PasswordSalt", "VARCHAR", 32, null, false),
		new Column("DisplayName", "VARCHAR", 50, null, true),
		new Column("TimestampCreated", "DATETIME", null, null, true),
		new Column("AccountLocked", "TINYINT", 1, null, true),
		new Column("ForcePasswordChange", "TINYINT", 1, null, true)
	));
	$tables[] = $tblUsers;
?>