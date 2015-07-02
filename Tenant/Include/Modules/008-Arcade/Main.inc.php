<?php
	namespace PhoenixSNS\Modules\Arcade;
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	System::$Modules[] = new Module("net.phoenixsns.Arcade", array
	(
		new ModulePage("arcade", function($path)
		{
			echo("Arcade loaded");
		})
	));
?>