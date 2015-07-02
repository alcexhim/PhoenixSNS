<?php
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	
	use WebFX\Parser\WebFXParser;
	
	chdir(dirname(__FILE__));
	
	$webfx = new WebFXParser();
	
	$files = glob("*.phpx.php");
	foreach ($files as $file)
	{
		require($file);
	}
	
	$files = glob("*.phpx");
	foreach ($files as $file)
	{
		$webfx->LoadFile($file);
	}
	
	$pages = array();
	
	foreach ($webfx->Pages as $page)
	{
		$mpage = new ModulePage($page->FileName, function($page, $path)
		{
			$page->ExtraData->Render();
			return true;
		});
		$mpage->ExtraData = $page;
		
		$pages[] = $mpage;
	}
	
	System::$Modules[] = new Module("net.phoenixsns.TenantManager.WebFXTest", $pages);
?>