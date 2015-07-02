var Place =
{
	"GetByID": function(id)
	{
		var xhr = new XMLHttpRequest();
		var path = System.ExpandRelativePath("~/API/Place.php");
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