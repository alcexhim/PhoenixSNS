<?php
	use WebFX\System;
	
	if (System::$Configuration["Groups.Enabled"])
	{
		if (count($path) > 1 && $path[1] != "")
		{
			if (StringMethods::EndsWith($path[1], ".mmo"))
			{
				switch ($path[1])
				{
					case "create.mmo":
					{
						require("Create.inc.php");
						return;
					}
					default:
					{
						System::Redirect("~/community/groups");
						return;
					}
				}
			}
			else
			{
				require("Detail.inc.php");
			}
		}
		else
		{
			require("Browse.inc.php");
		}
		return;
	}
	else
	{
		System::Redirect("~/community");
		return;
	}
?>