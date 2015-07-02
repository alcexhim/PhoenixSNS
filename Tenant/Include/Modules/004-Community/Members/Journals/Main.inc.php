<?php
	if ($path[3] != null && $path[3] != "")
	{
		switch ($path[3])
		{
			case "create.mmo":
			{
				require("Create.inc.php");
				return;
			}
			default:
			{
				if (count($path) > 4)
				{
					switch ($path[4])
					{
						case "modify.mmo":
						{
							require("Modify.inc.php");
							return;
						}
						case "remove.mmo":
						{
							require("Remove.inc.php");
							return;
						}
						case "Images":
						{
							if (count($path) > 5)
							{
								switch ($path[5])
								{
									case "Thumbnail.png":
									{
										$filename = "images/journals/" . $journal->ID . ".png";
										if (!file_exists($filename))
										{
											$filename = "images/journals/default.png";
										}
										header("Content-Type: image"); // . mime_content_type($filename));
										readfile($filename);
										return;
									}
								}
							}
							return;
						}
					}
				}
				require("Detail.inc.php");
				return;
			}
		}
	}
	else
	{
		require("Browse.inc.php");
		return;
	}
?>