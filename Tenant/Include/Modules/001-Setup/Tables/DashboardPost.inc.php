<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("DashboardPosts", "post_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("PostTypeID", "INT", null, null, true),
		new Column("TargetUserID", "INT", null, null, true),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("Content", "LONGTEXT", null, null, false),
		
		new Column("AllowLike", "INT", null, 1, false),
		new Column("AllowComment", "INT", null, 1, false),
		new Column("AllowShare", "INT", null, 1, false),
		
		new Column("CreationUserID", "INT", null, null, true),
		new Column("CreationTimestamp", "DATETIME", null, null, false)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("TenantID", 1),
			new RecordColumn("PostTypeID", ColumnValue::Undefined),
			new RecordColumn("TargetUserID", ColumnValue::Undefined),
			new RecordColumn("Title", "A few words about the new site design"),
			new RecordColumn("Content",
				"<ul><li>The new design is based around the concept of &quot;cards&quot;, which can update automatically over time and present you" .
				"with only the most important information and an intuitive way to act upon it.</li>" .
				"<li>The terminology &quot;User name&quot; for the private credential associated with your account has been changed to &quot;User " .
				"ID&quot; to avoid confusion with the publicly-visible Short and Long names so that people are not encouraged to use the same User" .
				"ID as their Short or Long name. It will eventually be made impossible to create a user account with the same User ID and Short/Long" .
				" name.</li>" .
				"<li>The World button on the navigation bar now only displays when you are logged in. This is to avoid confusing users who don't " .
				"realize that they must be logged in to use the World.</li></ul>"
			),
			
			new RecordColumn("AllowLike", 1),
			new RecordColumn("AllowComment", 1),
			new RecordColumn("AllowShare", 1),
			
			new RecordColumn("CreationUserID", ColumnValue::Undefined),
			new RecordColumn("CreationTimestamp", ColumnValue::Now)
		)),
		new Record(array
		(
			new RecordColumn("TenantID", 1),
			new RecordColumn("PostTypeID", ColumnValue::Undefined),
			new RecordColumn("TargetUserID", ColumnValue::Undefined),
			new RecordColumn("Title", "Log into your Administrator Control Panel"),
			new RecordColumn("Content",
				"Your PhoenixSNS Administrator Control Panel is a powerful tool to help you administer your new social network. You can broadcast " .
				"posts and advertisements to all of your users at once, control who has access to see what parts of the site, maintain market and " .
				"avatar items, and develop campaigns to entertain your users and generate revenue."
			),
			
			new RecordColumn("AllowLike", 1),
			new RecordColumn("AllowComment", 1),
			new RecordColumn("AllowShare", 1),
			
			new RecordColumn("CreationUserID", ColumnValue::Undefined),
			new RecordColumn("CreationTimestamp", ColumnValue::Now)
		))
	));
	$tables[] = new Table("DashboardPostTypes", "posttype_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("CssClass", "VARCHAR", 100, null, true)
	));
	$tables[] = new Table("DashboardPostActions", "action_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("PostID", "INT", null, null, false),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("URL", "LONGTEXT", null, null, true),
		new Column("Script", "LONGTEXT", null, null, true)
	));
	$tables[] = new Table("DashboardPostComments", "comment_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("PostID", "INT", null, null, false),
		new Column("ParentCommentID", "INT", null, null, false),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("Content", "LONGTEXT", null, null, false),
		new Column("CreationUserID", "INT", null, null, false),
		new Column("CreationTimestamp", "DATETIME", null, null, false)
	));
	$tables[] = new Table("DashboardPostShares", "share_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("PostID", "INT", null, null, false),
		new Column("TargetUserID", "INT", null, null, false),
		new Column("CreationUserID", "INT", null, null, false),
		new Column("CreationTimestamp", "DATETIME", null, null, false)
	));
	$tables[] = new Table("DashboardPostImpressions", "impression_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("PostID", "INT", null, null, false),
		new Column("CreationUserID", "INT", null, null, false),
		new Column("CreationTimestamp", "DATETIME", null, null, false)
	));
	$tables[] = new Table("DashboardPostReactions", "post_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("PostID", "INT", null, null, false),
		new Column("ReactionTypeID", "INT", null, null, false),
		new Column("ReactionComments", "LONGTEXT", null, null, true),
		new Column("CreationUserID", "INT", null, null, true),
		new Column("CreationTimestamp", "DATETIME", null, null, false)
	));
?>