<?php
	if (System::$Configuration["Forums.Enabled"])
	{
		if (count($path) > 1 && $path[1] != "")
		{
			if (str_endswith($path[1], ".mmo"))
			{
				switch ($path[1])
				{
					case "create.mmo":
					{
						require("create.inc.php");
						return;
					}
					default:
					{
						System::Redirect("~/community/forums");
						return;
					}
				}
			}
			else
			{
				require("detail.inc.php");
			}
		}
		else
		{
			require("list.inc.php");
		}
		return;
	}
	else
	{
		System::Redirect("~/community");
		return;
	}
?>