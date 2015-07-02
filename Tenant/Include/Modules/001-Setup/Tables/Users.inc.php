<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	use PhoenixSNS\Objects\UserPresenceStatus;
	use PhoenixSNS\Objects\UserProfileVisibility;
	
	$tables[] = new Table("Users", "user_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("LoginID", "VARCHAR", 50, null, false),
		new Column("URLName", "VARCHAR", 50, null, false),
		new Column("DisplayName", "VARCHAR", 100, null, false),
		new Column("EmailAddress", "VARCHAR", 200, null, true),
		new Column("EmailConfirmationCode", "VARCHAR", 16, ColumnValue::Undefined, true),
		new Column("BirthDate", "DATETIME", null, null, true),
		new Column("RealName", "VARCHAR", 200, null, true),
		new Column("PasswordHash", "VARCHAR", 256, null, false),
		new Column("PasswordSalt", "VARCHAR", 32, null, false),
		new Column("ThemeID", "INT", null, 0, false),
		new Column("LanguageID", "INT", null, 1, false),
		new Column("ProfileVisibility", "INT", null, UserProfileVisibility::ToIndex(UserProfileVisibility::Sitewide), false),
		new Column("ConsecutiveLoginCount", "INT", null, 0, false),
		new Column("ConsecutiveLoginFailures", "INT", null, 0, false),
		new Column("LastLoginTimestamp", "DATETIME", null, null, false),
		new Column("PresenceStatus", "INT", null, UserPresenceStatus::GetDatabaseID(UserPresenceStatus::Offline), false),
		new Column("PresenceMessage", "VARCHAR", 256, null, false),
		new Column("RegistrationTimestamp", "DATETIME", null, null, false),
		new Column("RegistrationIPAddress", "VARCHAR", 40, null, false),
		new Column("StartPageID", "INT", null, null, false)
	));
?>