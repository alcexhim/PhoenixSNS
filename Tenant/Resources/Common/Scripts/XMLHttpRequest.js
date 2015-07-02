/*
	Provide the XMLHttpRequest constructor for Internet Explorer 5.x-6.x:
	Other browsers (including Internet Explorer 7.x-9.x) do not redefine
	XMLHttpRequest if it already exists.

	This example is based on findings at:
	http://blogs.msdn.com/xmlteam/archive/2006/10/23/using-the-right-version-of-msxml-in-internet-explorer.aspx
*/
if (typeof XMLHttpRequest === "undefined")
{
	XMLHttpRequest = function ()
	{
		try { return new ActiveXObject("Msxml2.XMLHTTP.6.0"); }
		catch (e) {}
		try { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); }
		catch (e) {}
		try { return new ActiveXObject("Microsoft.XMLHTTP"); }
		catch (e) {}
		// Microsoft.XMLHTTP points to Msxml2.XMLHTTP and is redundant
		throw new Error("This browser does not support XMLHttpRequest.");
	};
}