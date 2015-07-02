<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../";
	
	require_once("WebFX/WebFX.inc.php");
	use WebFX\System;
	
	use PhoenixSNS\Objects\User;
	use PhoenixSNS\Objects\Group;
	use PhoenixSNS\Objects\Page;
	use PhoenixSNS\Objects\StartPage;
	use PhoenixSNS\Objects\Task;
	use PhoenixSNS\Modules\World\Objects\Place;
	
	global $MySQL;
	
	class LookupTable
	{
		public $TableName;
		public $LookupFieldName;
		public $RetrievalFunction;
		public $Result;
		
		public function __construct($tableName, $lookupFieldName, $retrievalFunction)
		{
			$this->TableName = $tableName;
			$this->LookupFieldName = $lookupFieldName;
			$this->RetrievalFunction = $retrievalFunction;
		}
	}
	
	$availableTables = array
	(
		'Users' => true,
		'Groups' => true,
		'Places' => true,
		'Pages' => true,
		'Tasks' => true,
		'StartPages' => false
	);
	
	$lookupTables = array();
	$include = null;
	if (isset($_GET["include"]))
	{
		$include = $_GET["include"];
	}
	if ($include != null)
	{
		$includes = explode(",", $include);
		foreach ($availableTables as $key => $value)
		{
			$availableTables[$key] = false;
		}
		
		foreach ($includes as $include)
		{
			$availableTables[$include] = true;
		}
	}
	
	if ($availableTables["Users"])
	{
		$lookupTables[] = new LookupTable("Users", "user_DisplayName", function($table, $totalCount, $previousCount)
		{
			$result = $table->Result;
			$items = array();
			$c = 0;
			for ($i = 0; $i < $result->num_rows; $i++)
			{
				$values = $result->fetch_assoc();
				$item = User::GetByAssoc($values);
				
				if (!$item->IsVisible())
				{
					$c++;
					continue;
				}
				
				$items[] = $item;
			}
			$totalCount -= $c;
			
			$count = count($items);
			for ($i = 0; $i < $count; $i++)
			{
				$item = $items[$i];
				echo("{ \"Category\": \"Members\", \"Item\": ");
				echo($item->ToJSON());
				echo(" }");
				if ($i + $previousCount < $totalCount - 1) echo(", ");
			}
		});
	}
	if ($availableTables["Groups"])
	{
		$lookupTables[] = new LookupTable("Groups", "group_Name", function($table, $totalCount, $previousCount)
		{
			$result = $table->Result;
			for ($i = 0; $i < $result->num_rows; $i++)
			{
				$values = $result->fetch_assoc();
				$item = Group::GetByAssoc($values);
				
				echo("{ \"Category\": \"Groups\", \"Item\": ");
				echo($item->ToJSON());
				echo(" }");
				
				if ($i + $previousCount < $totalCount - 1) echo(", ");
			}
		});
	}
	if ($availableTables["Places"])
	{
		$lookupTables[] = new LookupTable("Places", "place_Name", function($table, $totalCount, $previousCount)
		{
			$result = $table->Result;
			for ($i = 0; $i < $result->num_rows; $i++)
			{
				$values = $result->fetch_assoc();
				$item = Place::GetByAssoc($values);
				
				echo("{ \"Category\": \"Places\", \"Item\": ");
				echo($item->ToJSON());
				echo(" }");
				
				if ($i + $previousCount < $totalCount - 1) echo(", ");
			}
		});
	}
	if ($availableTables["Pages"])
	{
		$lookupTables[] = new LookupTable("Pages", "page_Title", function($table, $totalCount, $previousCount)
		{
			$result = $table->Result;
			for ($i = 0; $i < $result->num_rows; $i++)
			{
				$values = $result->fetch_assoc();
				$item = Page::GetByAssoc($values);
				
				echo("{ \"Category\": \"Pages\", \"Item\": ");
				echo($item->ToJSON());
				echo(" }");
				
				if ($i + $previousCount < $totalCount - 1) echo(", ");
			}
		});
	}
	if ($availableTables["Tasks"])
	{
		$lookupTables[] = new LookupTable("Tasks", "task_Title", function($table, $totalCount, $previousCount)
		{
			$result = $table->Result;
			for ($i = 0; $i < $result->num_rows; $i++)
			{
				$values = $result->fetch_assoc();
				$item = Task::GetByAssoc($values);
				
				echo("{ \"Category\": \"Tasks\", \"Item\": ");
				echo($item->ToJSON());
				echo(" }");
				
				if ($i + $previousCount < $totalCount - 1) echo(", ");
			}
		});
	}
	if ($availableTables["StartPages"])
	{
		$lookupTables[] = new LookupTable("StartPages", "startpage_Title", function($table, $totalCount, $previousCount)
		{
			$result = $table->Result;
			for ($i = 0; $i < $result->num_rows; $i++)
			{
				$values = $result->fetch_assoc();
				$item = StartPage::GetByAssoc($values);
				
				echo("{ \"Category\": \"StartPages\", \"Item\": ");
				echo($item->ToJSON());
				echo(" }");
				
				if ($i + $previousCount < $totalCount - 1) echo(", ");
			}
		});
	}
	
	header("Content-Type: application/json; charset=UTF-8");
	
	echo("{ ");
	$lookup = $_GET["query"];
	$totalCount = 0;
	
	foreach($lookupTables as $lookupTable)
	{
		$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . $lookupTable->TableName;
		if ($lookup != null && $lookup != "")
		{
			$query .= " WHERE " . $lookupTable->LookupFieldName . " LIKE '%" . $lookup . "%'";
		}
		if (isset($_GET["maximum"]))
		{
			if (is_numeric($_GET["maximum"]))
			{
				$query .= " LIMIT " . $_GET["maximum"];
			}
		}
	
		$result = $MySQL->query($query);
		if ($MySQL->errno != 0)
		{
			echo("\"result\": \"error\", \"error_code\": " . $MySQL->errno . ", \"error_message\": \"" . $MySQL->error . "\" }");
			return;
		}
		$totalCount += $result->num_rows;
		$lookupTable->Result = $result;
	}
	
	echo(" \"result\": \"success\", \"content\": [ ");
	$previousCount = 0;
	foreach ($lookupTables as $lookupTable)
	{
		call_user_func($lookupTable->RetrievalFunction, $lookupTable, $totalCount, $previousCount);
		$previousCount += $lookupTable->Result->num_rows;
	}
	
	echo(" ] }");
?>