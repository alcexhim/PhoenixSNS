<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("FeedbackTypes", "feedbacktype_", array
	(
		// When a user clicks a "Feedback" button:
		// [ v ] Feedback
		//   |  :D  | I love this
		//   |  :)  | I like this
		//   |  :(  | I dislike this
		// additional types can be added at any time
		
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Title", "VARCHAR", 50, null, false),
		new Column("Description", "VARCHAR", 100, null, false)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("Title", "Love"),
			new RecordColumn("Description", "I love this!")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Like"),
			new RecordColumn("Description", "I like this!")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Dislike"),
			new RecordColumn("Description", "I do not like this")
		))
	));
?>