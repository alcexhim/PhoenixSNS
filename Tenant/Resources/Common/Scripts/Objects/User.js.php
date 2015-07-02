<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../../../..";
	
	require_once("WebFX/WebFX.inc.php");
	use WebFX\System;
	
	use PhoenixSNS\Objects\User;
	
	header("Content-Type: text/javascript");
	
	$CurrentUser = User::GetCurrent();
?>
var User =
{
	"GetCurrent": function()
	{
<?php
		if ($CurrentUser == null)
		{
?>
		return null;
<?php
		}
		else
		{
?>
		return User.GetByID(<?php echo($CurrentUser->ID); ?>);
<?php
		}
?>
	},
	"GetByID": function(id)
	{
		var xhr = new XMLHttpRequest();
		var path = System.ExpandRelativePath("~/API/User.php");
		xhr.open("POST", path, false);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send("Action=Retrieve&ID=" + id + "&Format=JSON");
		
		var data = JSON.parse(xhr.responseText);
		if (data.Remedy && data.Remedy == "login")
		{
			if (confirm("You are not logged in or your session has timed out.  Please log in to continue."))
			{
				System.Redirect("~/");
			}
			return;
		}
		if (!data.Success) return null;
		
		return data.Items[0];
	}
};