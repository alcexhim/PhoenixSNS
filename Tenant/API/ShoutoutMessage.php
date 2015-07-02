<?php
	require("../Include/System.inc.php");
	
	// use PhoenixSNS\Objects\ShoutoutMessage;
	
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
				
				$item = ShoutoutMessage::GetByID($id);
				if ($item == null)
				{
					echo("{ \"Success\": false, \"ErrorMessage\": \"ShoutoutMessage with ID " . $id . " does not exist\" }");
					return;
				}
				
				echo("{ \"Success\": true, \"Items\": [ ");
				echo($item->ToJSON());
				echo(" ] }");
			}
			else
			{
				$items = ShoutoutMessage::Get();
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