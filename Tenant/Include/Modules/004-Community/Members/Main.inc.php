<?php
	use WebFX\System;
	
	use PhoenixSNS\Objects\Item;
	use PhoenixSNS\Objects\User;
	
	if (count($path) > 1 && $path[1] != "")
	{
		if ($path[2] == "images")
		{
			$thisuser = User::GetByIDOrShortName($path[1], true);
			if ($thisuser == null)
			{
				header("HTTP/1.1 404 Not Found");
				header("User-Agent: psychati/1.1");
				return;
			}
			$location = "avatar";
			$size = "thumbnail.png";
			if (count($path) > 3)
			{
				$location = $path[3];
				if (count($path) > 4)
				{
					$size = $path[4];
				}
			}
			
			$headers = apache_request_headers();
			switch ($location)
			{
				case "avatar":
				{
					if (isset($headers["If-Modified-Since"]) && ($headers["If-Modified-Since"] != null))
					{
						// load from cache if available
						$time1 = strtotime($thisuser->OutfitCacheTimestamp);
						$time2 = strtotime($headers["If-Modified-Since"]);
						
						if ($time1 <= $time2)
						{
							header("HTTP/1.1 304 Not Modified");
							return;
						}
					}
					
					header("Cache-Control: public");
					// header("Last-Modified: " . gmdate(DATE_RFC1123, $thisuser->OutfitCacheTimestamp));
					
					$base = 2;	// new base
					$view = 1;	// side view
					$bundle = "TinierWorld";
					
					$filenames = array("Resources/" . $bundle . "/Images/Avatar/Bases/" . $base . "/" . $view . "/Preview.png");
					
					$items = Item::GetEquippedByUser($thisuser);
					foreach ($items as $item)
					{
						foreach ($item->Images as $image)
						{
							$filenames[] = "Resources/" . $bundle . "/Images/Avatar/Items/" . $item->ID . "/" . $base . "/" . $view . "/" . $image->ID . ".png";
						}
					}
					
					global $RootPath;
					foreach ($filenames as $filename)
					{
						if (!file_exists($RootPath . "/" . $filename))
						{
							return false;
						}
					}
					
					switch ($size)
					{
						case "thumbnail.png":
						{
							$image = imagelayerfiles($filenames);
							$thumb = imagecreatetruecolor(112, 112);
							
							imagesavealpha($thumb, true);
							
							$trans_colour = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
							imagefill($thumb, 0, 0, $trans_colour);
							
							imagecopyresized($thumb, $image, 0, 0, 0, 0, 112, 112, /* 112, 112 */ 368, 409);
							
							imagesavealpha($thumb, true);
							imagealphablending($thumb, true);
							
							header('Content-Type: image/png');
							imagepng($thumb);
							
							
							// Update the outfit cache status for this user
							// mysql_query("UPDATE phpmmo_members SET member_outfit_cache_timestamp = '" . gmdate(DATE_RFC1123) . "' WHERE member_id = " . $thisuser->ID);
							return;
						}
						case "preview.png":
						{
							$image = imagelayerfiles($filenames);
							header('Content-Type: image/png');
							imagepng($image);
							
							
							// Update the outfit cache status for this user
							// mysql_query("UPDATE phpmmo_members SET member_outfit_cache_timestamp = '" . gmdate(DATE_RFC1123) . "' WHERE member_id = " . $thisuser->ID);
							return;
						}
					}
					return;
				}
			}
			return;
		}
		else
		{
			require("Profile.inc.php");
		}
	}
	else
	{
		require("Browse.inc.php");
	}
	return;
?>