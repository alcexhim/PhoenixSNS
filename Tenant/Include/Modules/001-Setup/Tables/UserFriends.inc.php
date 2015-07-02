<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	use PhoenixSNS\Objects\UserPresenceStatus;
	use PhoenixSNS\Objects\UserProfileVisibility;
	
	$tables[] = new Table("UserFriends", "userfriend_", array
	(
		new Column("UserID", "INT", null, null, false),
		new Column("FriendID", "INT", null, null, false),
		new Column("FriendshipAuthorizationKey", "VARCHAR", 16, null, false),
		new Column("FriendshipTimestamp", "DATETIME", null, null, false),
		new Column("LastInteractionTimestamp", "DATETIME", null, null, false),
		new Column("RelativePopularityScore", "INT", null, 0, false)
	));
?>