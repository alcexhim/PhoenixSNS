<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("UserResourceTransactions", "transaction_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("SendingUserID", "INT", null, null, true),
		new Column("ReceivingUserID", "INT", null, null, true),
		new Column("Comments", "LONGTEXT", null, null, false),
		new Column("CreationTimestamp", "DATETIME", null, null, false),
		new Column("CreationUserID", "INT", null, null, true)
	));
	$tables[] = new Table("UserResourceTransactionResources", "transactionresource_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("TransactionID", "INT", null, null, false),
		new Column("ResourceTypeID", "INT", null, null, false),
		new Column("Amount", "INT", null, null, false)
	));
?>