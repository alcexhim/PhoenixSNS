function InlineSearch()
{
	var txtSearch = document.getElementById("txtSearch");
	
	alert(txtSearch.value);
	// fetch teh script via AJAX
	$.ajax(
	{
		type: "GET",
		url: "/ajax/search.php?type=typeahead&query=" + txtSearch.value,
		data: null,
		dataType: "json",
		success: function(data)
		{
			alert("Success");
			if (data.result == "success")
			{
				alert("OK");
				
				var memberCount = data.members.length;
				if (memberCount > 5) memberCount = 5;
				
				var html = "";
				for (var i = 0; i < memberCount; i++)
				{
					html += "<a href=\"/community/members/" + data.members[i].shortName + "\">" + data.members[i].longName + "</a>";
				}
				pupSearch.SetContent(html);
	
				pupSearch.Move(txtSearch.pageX, txtSearch.pageY);
				pupSearch.Show();
				
				return true;
			}
		},
		failure: function()
		{
			alert("Fail");
		}
	});
}