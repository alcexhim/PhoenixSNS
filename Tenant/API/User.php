<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../";
	
	require_once("WebFX/WebFX.inc.php");
	use WebFX\System;
	
	use PhoenixSNS\Objects\User;
	
	switch ($_POST["Action"])
	{
		case "Retrieve":
		{
			if ($_POST["ID"] != null)
			{
				$id = $_POST["ID"];
				if (!is_numeric($id))
				{
					echo("{ \"Success\": false, \"ErrorMessage\": \"ID must be an integer\" }");
					return;
				}
				
				$item = User::GetByID($id);
				if ($item == null)
				{
					echo("{ \"Success\": false, \"ErrorMessage\": \"User with ID " . $id . " does not exist\" }");
					return;
				}
				
				echo("{ \"Success\": true, \"Items\": [ ");
				echo($item->ToJSON());
				echo(" ] }");
			}
			else
			{
				$items = User::Get();
				echo("{ \"Success\": true, \"Items\": [ ");
				$count = count($items);
				for ($i = 0; $i < $count; $i++)
				{
					$item = $items[$i];
					echo($item->ToJSON());
					if ($i < $count - 1) echo(", ");
				}
				echo(" ] }");
			}
			return;
		}
	}
	
	echo("{ \"Success\": false, \"ErrorMessage\": \"Unknown action \"" . $_POST["Action"] . "\" }");
	
?>