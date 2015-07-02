<?php
	require_once("Objects/Chance.inc.php");
	require_once("Pages/ChancePage.inc.php");
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use PhoenixSNS\Objects\User;
	use PhoenixSNS\Pages\ChancePage;
	
	System::$Modules[] = new Module("net.phoenixsns.Chance", array
	(
		new ModulePage("chance", function($path)
		{
			$CurrentUser = User::GetCurrent();
			
			$page = new ChancePage();
			$page->Render();
			return true;
		})
	));
?>
