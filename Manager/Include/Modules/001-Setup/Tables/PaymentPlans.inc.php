<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	use PhoenixSNS\Objects\PaymentPlanPaymentIntervalType;
	
	$tables[] = new Table("PaymentPlans", "paymentplan_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("Title", "VARCHAR", 256, null, true),
		new Column("Description", "LONGTEXT", null, null, true)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("Title", "Complimentary"),
			new RecordColumn("Description", "The client is not paying for the provisioning of this tenant.")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Trial"),
			new RecordColumn("Description", "The client will not pay for this tenant until the trial period expires.")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Fixed Rate Per Interval"),
			new RecordColumn("Description", "The client is paying a fixed amount per month, year, or other period of time. Customize this payment plan to change the amount and interval of payment.")
		)),
		new Record(array
		(
			new RecordColumn("Title", "Variable Rate Based on Data Usage with Initial Setup Fee"),
			new RecordColumn("Description", "The client is paying a percentage based on the usage of this tenant. Tenant is provisioned after a one-time setup fee is paid by the client. Customize this payment plan to change the amount and factors that contribute to usage (e.g. disk space, bandwidth consumption, etc.)")
		))
	));
	
	$tables[] = new Table("PaymentPlanPayments", "paymentplanpayments_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("PaymentPlanID", "INT", null, null, false, true, true),
		new Column("FixedAmount", "DECIMAL", null, null, true),
		new Column("VariableAmount", "DECIMAL", null, null, true),
		new Column("IntervalValue", "DECIMAL", null, null, true),
		new Column("IntervalType", "INT", null, null, true)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("PaymentPlanID", 3),
			new RecordColumn("FixedAmount", 5.00),
			new RecordColumn("VariableAmount", null),
			new RecordColumn("IntervalValue", 1),
			new RecordColumn("IntervalType", PaymentPlanPaymentIntervalType::Month)
		)),
		new Record(array
		(
			new RecordColumn("PaymentPlanID", 4),
			new RecordColumn("FixedAmount", 5.00),
			new RecordColumn("VariableAmount", 0.5)
		))
	));
?>