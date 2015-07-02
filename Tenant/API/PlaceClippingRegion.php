<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../";
	
	require_once("WebFX/WebFX.inc.php");
	use WebFX\System;
	
	use PhoenixSNS\Modules\World\Objects\Place;
	use PhoenixSNS\Modules\World\Objects\PlaceClippingRegion;
	use PhoenixSNS\Modules\World\Objects\PlaceClippingRegionPoint;
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$q = $_POST;
	}
	else
	{
		$q = $_GET;
	}
	
	switch ($q["Action"])
	{
		case "Retrieve":
		{
			if (isset($q["PlaceID"]))
			{
				$id = $q["PlaceID"];
				if (!is_numeric($id))
				{
					echo("{ \"Success\": false, \"ErrorMessage\": \"ID must be an integer\" }");
					return;
				}
				
				$place = Place::GetByID($id);
				if ($place == null)
				{
					echo("{ \"Success\": false, \"ErrorMessage\": \"Place with ID " . $id . " does not exist\" }");
					return;
				}
				$items = PlaceClippingRegion::GetByPlace($place);
				$count = count($items);
				
				echo("{ \"Success\": true, \"Items\": [ ");
				for ($i = 0; $i < $count; $i++)
				{
					echo($items[$i]->ToJSON());
					if ($i < $count - 1) echo(", ");
				}
				echo(" ] }");
			}
			else
			{
				echo("{ \"Success\": false, \"ErrorMessage\": \"Retrieval of all 'PlaceClippingRegion' objects not supported\" }");
				return;
			}
			return;
		}
	}
	
	echo("{ \"Success\": false, \"ErrorMessage\": \"Unknown action \"" . $q["Action"] . "\" }");
	
?>