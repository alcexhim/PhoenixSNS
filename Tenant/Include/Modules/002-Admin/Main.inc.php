<?php
	namespace PhoenixSNS\Modules;
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use PhoenixSNS\Objects\User;
	
	require_once("MasterPages/WebPage.inc.php");
	
	require_once("Pages/AdminMainPage.inc.php");
	use PhoenixSNS\Modules\Admin\Pages\AdminMainPage;
	
	System::$Modules[] = new Module("net.phoenixsns.Administration", array
	(
		new ModulePage("admin", array
		(
			new ModulePage("", function($path)
			{
				$CurrentUser = User::GetCurrent();
				if ($CurrentUser == null)
				{
					// force login
					$_SESSION["LoginRedirectURL"] = "~/admin";
					$_SESSION["LoginRedirectMessage"] = "You must log in as a user with administrative privileges to access this area.";
					System::Redirect("~/account/login.page");
				}
				
				// TODO: Check to see if user has permission 
				// if ($CurrentUser->ID != 1) return false;
				
				$page = new AdminMainPage();
				$page->Render();
				return true;
			})
		))
	));
?>