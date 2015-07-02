<?php
	if (count($path) > 1 && $path[1] != "")
	{
		if (StringMethods::EndsWith($path[1], ".mmo"))
		{
			switch ($path[1])
			{
				case "create.mmo":
				{
					require("Pages/Create.inc.php");
					return;
				}
				default:
				{
					System::Redirect("~/community/pages");
					return;
				}
			}
		}
		else
		{
			require("Pages/Detail.inc.php");
		}
	}
	else
	{
		require("Pages/Browse.inc.php");
	}
	return;
?>