<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("Places", "place_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 50, null, false),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("Description", "LONGTEXT", null, null, true),
		new Column("CreationTimestamp", "DATETIME", null, null, false),
		new Column("CreationUserID", "INT", null, null, false)
	));
	
	$tables[] = new Table("PlaceClippingRegions", "region_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("Left", "INT", null, null, false),
		new Column("Top", "INT", null, null, false),
		new Column("Width", "INT", null, null, false),
		new Column("Height", "INT", null, null, false),
		new Column("TargetPlaceID", "INT", null, null, true),
		new Column("TargetScript", "LONGTEXT", null, null, true),
		new Column("TargetURL", "LONGTEXT", null, null, true)
	));
	
	$tables[] = new Table("PlaceClippingRegionPoints", "regionpoint_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("RegionID", "INT", null, null, false),
		new Column("Left", "INT", null, null, false),
		new Column("Top", "INT", null, null, false)
	));
	
	$tables[] = new Table("PlaceHotspots", "hotspot_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Title", "VARCHAR", 100, null, false),
		new Column("Left", "INT", null, null, false),
		new Column("Top", "INT", null, null, false),
		new Column("Width", "INT", null, null, false),
		new Column("Height", "INT", null, null, false),
		new Column("TargetPlaceID", "INT", null, null, true),
		new Column("TargetScript", "LONGTEXT", null, null, true),
		new Column("TargetURL", "LONGTEXT", null, null, true)
	));
?>