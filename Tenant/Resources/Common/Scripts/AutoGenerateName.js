function AutoGenerateName(titleID, nameID)
{
	var name = document.getElementById(nameID);
	var title = document.getElementById(titleID);
	var value = title.value;
	if (!name.changed)
	{
		value = value.replace(/[^\w^\-^\s]+/g,'').toLowerCase();
		value = value.replace(/[^\w^\-]+/g,'-').toLowerCase();
		name.value = value;
	}
}
function AutoGenerateNameInvalidate(nameID)
{
	var name = document.getElementById(nameID);
	name.changed = true;
}