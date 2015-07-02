<?php
	// We need to get the root path of the Web site. It's usually something like
	// /var/www/yourdomain.com.
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/..";
	
	// Now that we have defined the root path, load the WebFX content (which also
	// include_once's the modules and other WebFX-specific stuff)
	require_once("WebFX/WebFX.inc.php");
	
	// Bring in the WebFX\System and WebFX\IncludeFile classes so we can simply refer
	// to them (in this file only) as "System" and "IncludeFile", respectively, from
	// now on
	use WebFX\System;
	use WebFX\IncludeFile;
	
	use PhoenixSNS\Objects\Tenant;
	
	if ($_GET["action"] == "exists")
	{
		$query = $_GET["q"];
		if (Tenant::Exists($query))
		{
			echo("{ \"result\": \"success\", \"exists\": true }");
			return;
		}
		
		echo("{ \"result\": \"success\", \"exists\": false }");
	}
?>