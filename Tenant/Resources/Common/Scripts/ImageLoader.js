function ImageLoader(imageurls)
{
	this.ImageUrls = imageurls;
	this.CallbackData = null;
	this.OnLoadCompleted = function(sender, e) { };
	this.OnError = function(sender, e) { };
	this._e = false;
	this.Load = function()
	{
		// start preloading
		var f = this.ImageLoadComplete;
		var u = this;
		var c = this.ImageLoadError;
		var k = this;
		for(i = 0; i < this.ImageUrls.length; i++)
		{
			var imageObj = new Image();
			imageObj.src = this.ImageUrls[i];
			imageObj.onload = function() { f(u, { "Image": imageObj }); };
			imageObj.onerror = function() { c(k, { "Image": imageObj }); };
			if (this._e) break;
		}
	};
	this.LoadedObjects = new Array();
	this._ImageUrlCount = imageurls.length;
	this.ImageLoadError = function(sender, e)
	{
		sender._e = true;
		sender.OnError(sender, e);
	};
	this.ImageLoadComplete = function(sender, e)
	{
		sender.LoadedObjects.push({ "Image": e.Image });
		if (sender.LoadedObjects.length == sender.ImageUrls.length)
		{
			sender.OnLoadCompleted(sender, e);
		}
	};
}