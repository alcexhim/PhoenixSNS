<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("StartPages", "startpage_", array
	(
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Title", "VARCHAR", 50, null, false),
		new Column("URL", "LONGTEXT", null, null, false)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("Title", "Account"),
			new RecordColumn("URL", "~/account")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Dashboard"),
			new RecordColumn("URL", "~/dashboard")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Market"),
			new RecordColumn("URL", "~/market")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Profile"),
			new RecordColumn("URL", "~/account/profile")
		)),
		new Record(array
		(
			new RecordColumn("Title", "World"),
			new RecordColumn("URL", "~/world")
		))
	));
?>