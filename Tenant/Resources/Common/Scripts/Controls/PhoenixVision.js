// PhoenixVision Player version 1.0.3a
// Copyright (C) 2014  Mike Becker
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

window.addEventListener("load", function(e)
{
	var items = document.getElementsByTagName("DIV");
	for (var i = 0; i < items.length; i++)
	{
		if (items[i].className == "PhoenixVision")
		{
			items[i].pv = new PhoenixVision(items[i], items[i].attributes["data-typename"].value);
		}
	}
});

function PhoenixVision(parent, typename)
{
	this.Parent = parent;
	this.Parent.addEventListener("contextmenu", function(e)
	{
		if (this.ContextMenu != null)
		{
			this.ContextMenu.Show();
		}
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
	this.Parent.addEventListener("selectstart", function(e)
	{
		e.preventDefault();
		e.stopPropagation();
		return false;
	});

	switch (typename)
	{
		case "avatar":
		{
			var items = null;
			
			var base = null;
			if (typeof parent.attributes["data-base-id"] != "undefined")
			{
				base = AvatarBase.GetByID(parent.attributes["data-base-id"].value);
			}
			var view = AvatarView.GetByID(1);
			if (typeof parent.attributes["data-view-id"] != "undefined")
			{
				view = AvatarView.GetByID(parent.attributes["data-view-id"].value);
			}
			
			if (typeof parent.attributes["data-member-id"] != "undefined")
			{
				var member = User.GetByID(parent.attributes["data-member-id"].value);
				// items = member.GetEquippedItems();
				alert ("PhoenixVision (" + parent.id + "): avatar loading for member " + member.ShortName + " not implemented yet");
			}
			else if (typeof parent.attributes["data-item"] != "undefined")
			{
				items = new Array();
				for (var i = 0; i < parent.attributes.length; i++)
				{
					if (parent.attributes[i].name == "data-item")
					{
						items.push(parent.attributes[i].value);
					}
				}
			}
			else
			{
				alert ("PhoenixVision (" + parent.id + "): must specify either member ID or item collection");
			}
			
			if (items.length > 0)
			{
				var hw = '';
				for (var i = 0; i < items.length; i++)
				{
					hw += items[i].ID + ', ';
				}
				alert(hw);
			}
			return;
			
			var html = '<div style="position: relative; top: 128px; left: 128px;" class="AvatarContainer">';
			html += '<div style="transform: scale(0.2); -webkit-transform: scale(0.2); -o-transform: scale(0.2); -ms-transform: scale(0.2); -moz-transform: scale(0.2); text-align: center; " class="Avatar Avatar2">';
			html += '<div id="Avatar_avatar1_chatbubble" class="chatbubble"></div>';
			html += '<div style="background-image: url(\'http://www.psychatica.com/images/avatar/bases/2/1/loading/error.png\'); display: none; width: 400px; height: 1516px;" title="Please enable JavaScript to see Avatars" id="Avatar_avatar1_error" class="error"></div>';
			html += '<div style="background-image: url(\'http://www.psychatica.com/images/avatar/bases/2/1/loading/outline.png\'); display: none; width: 400px; height: 1516px;" id="Avatar_avatar1_loading" class="loading">';
			html += '<img src="http://www.psychatica.com/images/avatar/bases/2/1/loading/fill.png" id="Avatar_avatar1_loading_fill" class="fill" style="opacity: 0.5; width: 400px; height: 1516px;"></div>';
			html += '<div id="Avatar_avatar1_body" class="body" style="display: block; width: 400px; height: 1516px;">';
			html += '<div id="Avatar_avatar1_body_Slices_head" class="head" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/head.png&quot;);">&nbsp;</div>';
			/*
			<div id="Avatar_avatar1_body_Slices_torso" class="torso" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/torso.png&quot;);">&nbsp;</div><div id="Avatar_avatar1_body_Slices_arm_right_upper" class="arm_right_upper" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/arm_right_upper.png&quot;);"><div id="Avatar_avatar1_body_Slices_arm_right_lower" class="arm_right_lower" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/arm_right_lower.png&quot;); transform: rotate(-90deg);"><div id="Avatar_avatar1_body_Slices_hand_right" class="hand_right" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/hand_right.png&quot;);">&nbsp;</div></div></div><div id="Avatar_avatar1_body_Slices_arm_left_upper" class="arm_left_upper" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/arm_left_upper.png&quot;);"><div id="Avatar_avatar1_body_Slices_arm_left_lower" class="arm_left_lower" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/arm_left_lower.png&quot;); transform: rotate(90deg);"><div id="Avatar_avatar1_body_Slices_hand_left" class="hand_left" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/hand_left.png&quot;);">&nbsp;</div></div></div><div id="Avatar_avatar1_body_Slices_leg_right_upper" class="leg_right_upper" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/leg_right_upper.png&quot;);"><div id="Avatar_avatar1_body_Slices_leg_right_lower" class="leg_right_lower" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/leg_right_lower.png&quot;);"><div id="Avatar_avatar1_body_Slices_foot_right" class="foot_right" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/foot_right.png&quot;);">&nbsp;</div></div></div><div id="Avatar_avatar1_body_Slices_leg_left_upper" class="leg_left_upper" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/leg_left_upper.png&quot;);"><div id="Avatar_avatar1_body_Slices_leg_left_lower" class="leg_left_lower" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/leg_left_lower.png&quot;);"><div id="Avatar_avatar1_body_Slices_foot_left" class="foot_left" style="background-image: url(&quot;http://www.psychatica.com/images/avatar/bases/2/1/slices/foot_left.png&quot;);">&nbsp;</div></div></div></div></div><script type="text/javascript">var avatar1 = new Avatar("avatar1", AvatarBase.GetByID(2));</script></div>
			*/
			break;
		}
		case "world":
		{
			var world = document.createElement("div");
			world.className = "World";
			
			// create the progress and content elements
			var worldProgress = document.createElement("div");
			worldProgress.className = "WorldProgress";
			worldProgress.style.display = "block";
			
			this.ProgressBar = ProgressBar.Create("pbWorld_" + this.ID, worldProgress);
			
			world.appendChild(worldProgress);
			
			var worldContent = document.createElement("div");
			worldContent.className = "WorldContent";
			worldContent.style.display = "none";
			worldContent.onclick = function(e)
			{
				var txtChat = document.getElementById("txtChat");
				txtChat.focus();
				
				// NOTE: this may only work on Mozilla browsers!!
				e.stopPropagation();
				
				var x = e.clientX;
				var y = e.clientY;
				
				x -= 128; // Math.floor((document.body.clientWidth - 800) / 2)
				y -= 128;
				player.Move(x, y);
			};
			world.appendChild(worldContent);
			
			this.BootstrapObjects = new Array();
			this.ContextMenu = null;
			this.DebugMode = false;
			this.SetCurrentPlace = function(place)
			{
				alert("Navigating to Place " + place.Name);
			};
			this.ShowAboutDialog = function()
			{
				alert("PhoenixVision Viewer version 1.0.3a\r\nCopyright (c)2014 Mike Becker/Psychatic Entertainment Group\r\n\r\nLicensed under the GNU General Public License, PhoenixVision Viewer is free software.");
			};
			this.DisplayContent = function()
			{
				worldProgress.style.display = "none";
				worldContent.style.display = "block";
			};
			this.Refresh = function()
			{
				var pv = this;
				worldProgress.style.display = "block";
				
				this.ProgressBar.MinimumValue = 0;
				this.ProgressBar.MaximumValue = pv.BootstrapObjects.length;
				this.ProgressBar.Update();
				
				if (pv.BootstrapObjects.length == 0)
				{
					pv.DisplayContent();
				}
				else
				{
					for (var i = 0; i < pv.BootstrapObjects.length; i++)
					{
						var bso = pv.BootstrapObjects[i];
						var imageObj = new Image();
						
						if (pv.DebugMode)
						{
							console.log("attempting to load background for \"" + bso.Place.Name + "\"");
							console.log("image url: \"" + bso.ImageURL + "\"");
						}
						imageObj.onload = function()
						{
							if (pv.DebugMode) console.log("loaded background for \"" + bso.Place.Name + "\"");
							pv.ProgressBar.SetCurrentValue(pv.ProgressBar.CurrentValue + 1);
							if (pv.ProgressBar.CurrentValue == pv.ProgressBar.MaximumValue) pv.DisplayContent();
						};
						imageObj.onerror = function()
						{
							if (pv.DebugMode) console.error("failed to load background for \"" + bso.Place.Name + "\"");
							pv.ProgressBar.SetCurrentValue(pv.ProgressBar.CurrentValue + 1);
							if (pv.ProgressBar.CurrentValue == pv.ProgressBar.MaximumValue) pv.DisplayContent();
						};
						imageObj.src = bso.ImageURL;
					}
				}
			};
			
			this.Parent.appendChild(world);
			
			this.Refresh();
			break;
		}
	}
}
