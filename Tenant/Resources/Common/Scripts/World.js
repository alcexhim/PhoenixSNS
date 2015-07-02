var World =
{
	"Width": 1600,
	"Height": 600,
	"Viewport":
	{
		"Left": 0,
		"Top": 0,
		"Width": 800,
		"Height": 600,
		"Right": function()
		{
			return this.Left + this.Width;
		},
		"Bottom": function()
		{
			return this.Top + this.Height;
		}
	},
	"Map":
	{
		"Show": function()
		{
			var worldMap = document.getElementById("worldMap");
			worldMap.style.visibility = "visible";
		},
		"Hide": function()
		{
			var worldMap = document.getElementById("worldMap");
			worldMap.style.visibility = "hidden";
		},
		"Toggle": function()
		{
			var worldMap = document.getElementById("worldMap");
			
			if (worldMap.style.visibility != "visible")
			{
				worldMap.style.visibility = "visible";
			}
			else
			{
				worldMap.style.visibility = "hidden";
			}
		},
		"Teleport": function(worldID)
		{
			this.Hide();
			
			var worldContent = document.getElementById("worldContent");
			worldContent.style.backgroundImage = "url('scenarios/basic/worlds/" + worldID + "/images/background.png')";
		}
	}
};
var player =
{
	"Move": function(x, y)
	{
		var worldContent = document.getElementById("worldContent");
		
		if (world.Viewport.Right() < world.Width)
		{
			world.Viewport.Left = x;
		}
		
		// todo: figure out
		//		if the player is at a certain position (player right + viewport width > world width)
		//		then the viewport should be moving...
		
		var pos = "-" + world.Viewport.Left + " bottom";
		// worldContent.style.backgroundPosition = pos;
		
		$.ajax(
		{
			type: "POST",
			url: "/ajax/presence.php",
			data:
			{
				'action': 'move',
				'x': x,
				'y': y
			},
			dataType: "json",
			success: function(data)
			{
				if (data.result == "success")
				{
				}
				window.setTimeout(function() { presence_RetrievePresence(); }, 5000);
			}
		});
	}
};