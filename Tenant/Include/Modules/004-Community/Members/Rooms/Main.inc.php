<?php
	switch ($path[3])
	{
		case "purchase":
		{
			require("purchase.inc.php");
			return;
		}
		default:
		{
			require("list.inc.php");
			return;
		}
	}
?>