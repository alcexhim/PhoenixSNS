<?php
	if ($path[3] == "items")
	{
		require("Detail.inc.php");
	}
	else
	{
		require("Browse.inc.php");
	}
?>