function Hovercard(id)
{
	this.ID = id;
	this.Data = null;
	this.Timeout = Hovercard.DefaultTimeout;
	this.OnBeforeShow = function()
	{
	};
	this.OnAfterShow = function()
	{
	};
	this.Show = function(x, y, data)
	{
		this.Data = data;
		var hovercard = document.getElementById("Hovercard_" + id);
		if (hovercard.htimer) window.clearTimeout(hovercard.htimer);
		hovercard.htimer = window.setTimeout(function(hovercard)
		{
			hovercard.OnBeforeShow(hovercard, { "Data": hovercard.Data });
			var hc = document.getElementById("Hovercard_" + hovercard.ID);
			if (hc.style.display != "block")
			{
				hc.style.left = (x + 32) + "px";
				hc.style.top = (y - hc.offsetHeight) + "px";
			}
			hc.style.display = "block";
		}, this.Timeout, this);
	};
	this.Hide = function()
	{
		var hovercard = document.getElementById("Hovercard_" + id);
		if (hovercard.htimer) window.clearTimeout(hovercard.htimer);
		hovercard.style.display = "none";
	};
	this.SetInnerHTML = function(value)
	{
		var hovercard = document.getElementById("Hovercard_" + id);
		hovercard.innerHTML = value;
	};
}
Hovercard.DefaultTimeout = 1000;