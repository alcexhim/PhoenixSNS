<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../../..";
	
	require_once("WebFX/WebFX.inc.php");
	use WebFX\System;
	
	header("Content-Type: text/javascript");
?>
var System =
{
	"ExpandRelativePath": function(path)
	{
		var basepath = "<?php echo(System::$Configuration["Application.BasePath"]); ?>";
		var retpath = path.replace(/~\//g, basepath + "/");
		return retpath;
	},
	"Redirect": function(url)
	{
		window.location.href = System.ExpandRelativePath(url);
	}
};