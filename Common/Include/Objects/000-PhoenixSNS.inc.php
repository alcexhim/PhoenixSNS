<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	\Enum::Create("PhoenixSNS\\Objects\\LogMessageSeverity", "Notice", "Warning", "Error");
	
	class PhoenixSNS
	{
		public static function BuildSearchURL($properties = null, $validObjects = null, $caseInsensitive = false, $tenant = null)
		{
			if ($properties == null) $properties = array();
			if ($validObjects == null) $validObjects = array();
			$url = System::ExpandRelativePath("~/API/Search.php?");
			if ($tenant != null)
			{
				$url .= "tenant=" . $tenant->URL;
			}
			else
			{
				$url .= "tenant=" . System::$TenantName;
			}
			$url .= "&propertyCount=" . count($properties);
			$i = 0;
			foreach ($properties as $propertyID => $searchValue)
			{
				$url .= "&propertyID" . $i . "=" . $propertyID;
				$url .= "&propertyValue" . $i . "=" . $searchValue;
				$url .= "&propertyComparison" . $i . "=" . "contains";
				$i++;
			}
			$count = count($validObjects);
			if ($count > 0)
			{
				$objectIDs = "";
				for ($i = 0; $i < $count; $i++)
				{
					$objectIDs .= $validObjects[$i]->ID;
					if ($i < $count - 1) $objectIDs .= ",";
				}
				$url .= "&validObjects=" . $objectIDs;
			}
			if ($caseInsensitive) $url .= "&caseInsensitive";
			return $url;
		}
		public static function Log($message, $params = null, $severity = LogMessageSeverity::Error)
		{
			$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			
			if ($params == null) $params = array();
			
			global $MySQL;
			
			$tenant = Tenant::GetCurrent();
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "DebugMessages (message_TenantID, message_Content, message_SeverityID, message_Timestamp, message_IPAddress) VALUES (";
			$query .= ($tenant == null ? "NULL" : $tenant->ID) . ", ";
			$query .= "'" . $MySQL->real_escape_string($message) . "', ";
			$query .= $severity . ", ";
			$query .= "NOW(), ";
			$query .= "'" . $MySQL->real_escape_string($_SERVER["REMOTE_ADDR"]) . "'";
			$query .= ")";
			$MySQL->query($query);
			
			$msgid = $MySQL->insert_id;
			
			foreach ($bt as $bti)
			{
				$filename = "(unknown)";
				$lineNumber = "(unknown)";
				if (isset($bti["file"])) $filename = $bti["file"];
				if (isset($bti["line"])) $lineNumber = $bti["line"];
				
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "DebugMessageBacktraces (bt_MessageID, bt_FileName, bt_LineNumber) VALUES (";
				$query .= $msgid . ", ";
				$query .= "'" . $MySQL->real_escape_string($filename) . "', ";
				$query .= $lineNumber;
				$query .= ")";
				$MySQL->query($query);
			}
			
			foreach ($params as $key => $value)
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "DebugMessageParameters (mp_MessageID, mp_Name, mp_Value) VALUES (";
				$query .= $msgid . ", ";
				$query .= "'" . $MySQL->real_escape_string($key) . "', ";
				$query .= "'" . $MySQL->real_escape_string($value) . "'";
				$query .= ")";
				$MySQL->query($query);
			}
		}
	}
?>