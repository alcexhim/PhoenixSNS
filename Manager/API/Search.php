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
	use PhoenixSNS\Objects\TenantObject;
	use PhoenixSNS\Objects\TenantObjectInstanceProperty;
	
	use PhoenixSNS\Objects\ConditionalStatement;
	use PhoenixSNS\Objects\ConditionalStatementGroup;
	use PhoenixSNS\Objects\ConditionalStatementCombination;
	use PhoenixSNS\Objects\ConditionalStatementComparison;
	use PhoenixSNS\Objects\PhoenixSNS;
	
	use PhoenixSNS\Objects\User;
	
	class SearchResult
	{
		public $Title;
		public $Description;
		public $Type;
		public $TargetURL;
		
		public function __construct($title, $description, $type, $targetURL)
		{
			$this->Title = $title;
			$this->Description = $description;
			$this->Type = $type;
			$this->TargetURL = $targetURL;
		}
	}
	
	$query = $_GET["q"];
	
	$results = array();
	
	$items = System::$Tasks;
	$count = count($items);
	for ($i = 0; $i < $count; $i++)
	{
		$item = $items[$i];
		$results[] = new SearchResult($item->Title, $item->Description, "Task", $item->TargetURL);
	}
	
	$items = User::Get();
	$count = count($items);
	for ($i = 0; $i < $count; $i++)
	{
		$item = $items[$i];
		$displayName = ($item->DisplayName == null ? $item->UserName : $item->DisplayName);
		$results[] = new SearchResult($displayName, "", "User", "~/users/modify/" . $item->ID);
	}
	
	$realResults = array();
	$count = count($results);
	for ($i = 0; $i < $count; $i++)
	{
		$item = $results[$i];
		if (strpos(strtolower($item->Title), strtolower($query)) === false) continue;
		$realResults[] = $item;
	}
	
	echo("{ ");
	echo("\"result\": \"success\", ");
	echo("\"items\": [ ");
	
	$count = count($realResults);
	for ($i = 0; $i < $count; $i++)
	{
		$item = $realResults[$i];
		echo("{ ");
		echo("\"Title\": \"" . \JH\Utilities::JavaScriptEncode($item->Title, "\"") . "\", ");
		echo("\"Description\": \"" . \JH\Utilities::JavaScriptEncode($item->Description, "\"") . "\", ");
		echo("\"Subtitle\": \"" . \JH\Utilities::JavaScriptEncode($item->Type, "\"") . "\", ");
		echo("\"TargetURL\": \"" . \JH\Utilities::JavaScriptEncode(System::ExpandRelativePath($item->TargetURL), "\"") . "\"");
		echo(" }");
		if ($i < $count - 1) echo(", ");
	}
	echo(" ]");
	echo(" }");
?>