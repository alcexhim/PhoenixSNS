var animStep = 100;
function GachaAnimateSpinnerStep(gacha, step)
{
	var button = document.getElementById("Chance_" + gacha.id + "_spinbutton");
	var style = button.style;
	style.setProperty("-webkit-transform", "rotate(" + (step * 30) + "deg)");
	style.setProperty("-moz-transform", "rotate(" + (step * 30) + "deg)");
	style.setProperty("-ms-transform", "rotate(" + (step * 30) + "deg)");
	style.setProperty("-o-transform", "rotate(" + (step * 30) + "deg)");
	style.setProperty("transform", "rotate(" + (step * 30) + "deg)");
	
	if (step < 12)
	{
		window.setTimeout(function()
		{
			GachaAnimateSpinnerStep(gacha, step + 1);
		}, animStep);
	}
	else
	{
		window.setTimeout(function()
		{
			GachaAnimateCompletionStep(gacha, 0);
		}, animStep);
	}
}
function GachaAnimateCompletionStep(gacha, step)
{
	// TODO: figure out how to animate the ball rolling out of the slot
	if (step < 12)
	{
		window.setTimeout(function()
		{
			GachaAnimateCompletionStep(gacha, step + 1);
		}, animStep);
	}
	else
	{
		gacha.completed();
	}
}
function GetRandomNumber(min, max)
{
	return Math.floor((Math.random()*max)+min);
}
function GachaGenerateRandomEggs(id)
{
	var globe = document.getElementById("Chance_" + id + "_globe_eggs");
	var maxEggs = 15;
	// <div class="ChanceEgg" style="background-color: #FF0000; left: 80px; top: 80px;"></div>
	// <div class="ChanceEgg" style="background-color: #00CC00; left: 40px; top: 30px;"></div>
	// <div class="ChanceEgg" style="background-color: #00FFFF; left: 60px; top: 40px;"></div>
	
	var colors = ["red", "green", "blue", "teal", "magenta", "gold"];
	var color = "#FF0000";
	
	var html = "";
	
	var minX = 40;
	
	var maxX = 174 - 40 - 10;
	var maxY = 178;
	var y = 178;
	var maxLeft = 0, maxRight = 0;
	
	for (i = 0; i < maxEggs; i++)
	{
		color = colors[GetRandomNumber(0, colors.length)];
		var x = globe.offsetParent.offsetLeft + globe.offsetLeft + GetRandomNumber(minX, maxX);
		var rot = GetRandomNumber(0, 359);
		
		html += "<div class=\"ChanceEgg\" style=\"background-color: " + color + "; left: " + x + "px; top: " + y + "px; -moz-transform: rotate(" + rot + "deg); -webkit-transform: rotate(" + rot + "deg);\"></div>";
		
		if (x > maxRight)
		{
			maxRight = x;
		}
		if (maxLeft < minX || (maxRight + 39) > maxX)
		{
			y -= 4;
			maxLeft = 0;
			maxRight = 0;
		}
	}
	globe.innerHTML = html;
}

function Gacha(id)
{
	this.id = id;
	this.spin = function()
	{
		GachaAnimateSpinnerStep(this, 0);
	};
	this.completed = function()
	{
	};
	this.setBankrupt = function(value)
	{
		var bankruptNotification = document.getElementById("Chance_" + id + "_bankrupt");
		if (value)
		{
			bankruptNotification.style.display = "block";
			
			var bankruptNotificationNew = bankruptNotification.cloneNode(true);
			bankruptNotification.parentNode.replaceChild(bankruptNotificationNew, bankruptNotification);
		}
		else
		{
			bankruptNotification.style.display = "none";
		}
	}
	
	// GachaGenerateRandomEggs(id);
}
