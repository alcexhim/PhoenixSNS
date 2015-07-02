<?php
	namespace PhoenixSNS\Modules\World;
	
	require_once("Controls/PhoenixVision.inc.php");
	
	require_once("Objects/PlaceClippingRegionPoint.inc.php");
	require_once("Objects/PlaceClippingRegion.inc.php");
	
	require_once("Objects/PlaceHotspot.inc.php");
	require_once("Objects/Place.inc.php");
	require_once("Pages/WorldPage.inc.php");
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use PhoenixSNS\Objects\User;
	
	use PhoenixSNS\Modules\World\Objects\Place;
	use PsychaticaErrorPage;
	
	use PhoenixSNS\Modules\World\Pages\WorldPage;
	
	System::$Modules[] = new Module("net.phoenixsns.World", array
	(
		new ModulePage("world", function($path)
		{
			$CurrentUser = User::GetCurrent();
			
			if (!System::GetConfigurationValue("World.Enabled", false))
			{
				System::Redirect("~/");
				return true;
			}
			
			$CurrentPlace = null;
			if ($path[0] == "places")
			{
				if ($path[1] != "")
				{
					$CurrentPlace = Place::GetByIDOrName($path[1]);
					if ($path[2] == "images")
					{
						switch ($path[3])
						{
							case "backgrnd.png":
							{
								header("Content-Type: image");
								readfile("images/world/places/" . $CurrentPlace->ID . "/backgrnd.png");
								return true;
							}
							case "preview.png":
							{
								header("Content-Type: image");
								readfile("images/world/places/" . $CurrentPlace->ID . "/backgrnd.png");
								return true;
							}
							case "hotspot.png":
							{
								header("Content-Type: image");
								readfile("images/world/places/" . $CurrentPlace->ID . "/hotspot.png");
								return true;
							}
							default:
							{
								return false;
							}
						}
					}
					else if ($CurrentPlace == null)
					{
						System::Redirect("~/world");
						return true;
					}
				}
			}
			else if ($path[0] == "")
			{
				// TODO: don't hardcode this!
				$CurrentPlace = Place::GetByID(11);
			}
			else
			{
				System::Redirect("~/world");
			}
			
			$_SESSION["CurrentPlaceID"] = ($CurrentPlace == null ? 0 : $CurrentPlace->ID);

			if ($CurrentUser == null)
			{
				$_SESSION["LoginRedirectURL"] = "~/world";
				System::Redirect("~/account/login.page");
				/*
				$page = new PsychaticaErrorPage();
				$page->Title = "Not Logged In";
				$page->Message = "You must be logged in to visit the World.  Please log in and then try visiting the World again.";
				*/
				return true;
			}
			else
			{
				$page = new WorldPage();
				$page->CurrentPlace = $CurrentPlace;
			}
			
			$page->Render();
			return true;
		})
	));
?>