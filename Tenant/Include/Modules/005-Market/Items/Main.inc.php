<?php
	use WebFX\System;
	use PhoenixSNS\Objects\Item;
	
	use PsychaticaErrorPage;
	use PsychaticaMessagePage;
	
	if (count($path) > 0 && $path[0] != "")
	{
		$id = $path[0];
		$item = Item::GetByIDOrName($id);
		
		if ($item == null)
		{
			$errorPage = new PsychaticaErrorPage();
			$errorPage->Message = "The item does not exist.";
			$errorPage->Render();
			return;
		}
		
		$entry = $item->GetMarketEntry();
		
		if (count($path) > 1 && $path[1] != "")
		{
			switch ($path[1])
			{
				case "equip":
				{
					if ($CurrentUser != null)
					{
						if ($item->Equip($CurrentUser))
						{
							$page = new PsychaticaMessagePage();
							$page->Message = "The item has been equipped.";
							$page->ReturnButtonURL = $_SERVER["HTTP_REFERER"];
							$page->ReturnButtonText = "Return to Inventory";
							$page->Render();
						}
						else
						{
							$page = new PsychaticaErrorPage();
							$page->Message = "The item could not be equipped. Perhaps it has already been equipped.";
							$page->ErrorCode = mysql_errno();
							$page->ErrorDescription = mysql_error();
							
							$page->ReturnButtonURL = $_SERVER["HTTP_REFERER"];
							$page->ReturnButtonText = "Return to Inventory";
							$page->Render();
						}
					}
					else
					{
						$page = new PsychaticaErrorPage();
						$page->Message = "You must be logged in to do that.";
						$page->ErrorCode = mysql_errno();
						$page->ErrorDescription = mysql_error();
						
						$page->ReturnButtonURL = $_SERVER["HTTP_REFERER"];
						$page->ReturnButtonText = "Return to Inventory";
						$page->Render();
					}
					return;
				}
				case "unequip":
				{
					if ($CurrentUser != null)
					{
						if ($item->Unequip($CurrentUser))
						{
							$page = new PsychaticaMessagePage();
							$page->Message = "The item has been unequipped.";
							$page->ReturnButtonURL = $_SERVER["HTTP_REFERER"];
							$page->ReturnButtonText = "Return to Inventory";
							$page->Render();
						}
						else
						{
							$page = new PsychaticaErrorPage();
							$page->Message = "The item could not be unequipped. Perhaps it has not yet been equipped, or you do not have the item in your Inventory.";
							$page->ErrorCode = mysql_errno();
							$page->ErrorDescription = mysql_error();
							
							$page->ReturnButtonURL = $_SERVER["HTTP_REFERER"];
							$page->ReturnButtonText = "Return to Inventory";
							$page->Render();
						}
					}
					else
					{
						$page = new PsychaticaErrorPage();
						$page->Message = "You must be logged in to do that.";
						$page->ErrorCode = mysql_errno();
						$page->ErrorDescription = mysql_error();
						
						$page->ReturnButtonURL = $_SERVER["HTTP_REFERER"];
						$page->ReturnButtonText = "Return to Inventory";
						$page->Render();
					}
					return;
				}
				case "purchase":
				{
					require("Purchase.inc.php");
					return;
				}
				case "images":
				{
					switch ($path[2])
					{
						case "thumbnail.png":
						{
							$base = 2;
							$view = 2;
							
							$base_image_filename = "images/avatar/bases/" . $base . "/" . $view . "/preview.png";
							$base_image = imagecreatefrompng($base_image_filename);
							$base_image_info = getimagesize($base_image_filename);
							$base_image_width = $base_image_info[0];
							$base_image_height = $base_image_info[1];
							
							$item_images = array($base_image);
							
							foreach ($item->Images as $image)
							{
								// resample and layer the item images
								$item_image_filename = "images/avatar/items/" . $item->ID . "/" . $base . "/" . $view . "/" . $image->ID . ".png";
								$item_image = imagecreatefrompng($item_image_filename);
								
								$item_image_info = getimagesize($item_image_filename);
								$item_image_width = $item_image_info[0];
								$item_image_height = $item_image_info[1];
								
								$image_left = $image->Left;
								if ($image_left == null) $image_left = 0;
								
								$image_top = $image->Top;
								if ($image_top == null) $image_top = 0;
								
								$image_width = $image->Width;
								if ($image_width == null) $image_width = $base_image_width;
								
								$image_height = $image->Height;
								if ($image_height == null) $image_height = $base_image_height;
								
								$item_image_resampled = imagecreatetruecolor($item_image_width, $item_image_height);
								imagemaketransparent($item_image_resampled);
								imagecopyresampled($item_image_resampled, $item_image, $image_left, $image_top, 0, 0, $image_width, $image_height, $item_image_width, $item_image_height);
								
								$item_images[] = $item_image_resampled;
							}
    
							$final_image = imagelayerimages($item_images);
							
							header('Content-Type: image/png');
							imagepng($final_image);
							return;
						}
						case "item.png":
						{
							$array = array();
							
							foreach ($item->Images as $image)
							{
								$array[] = "images/avatar/items/" . $image->ID . ".png";
							}
							$image = imagelayerfiles($array);
							
							header('Content-Type: image/png');
							imagepng($image);
							return;
						}
					}
					return;
				}
			}
			
			System::Redirect("~/market/items/" . $path[0]);
			return;
		}
		
		require("Detail.inc.php");
		return;
	}
	else
	{
		require("Browse.inc.php");
		return;
	}
?>