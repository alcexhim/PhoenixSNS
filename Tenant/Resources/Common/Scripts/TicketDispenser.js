function TicketDispenser(name, amount)
{
	this.Name = name;
	this.Amount = amount;
	this.Value = 0;
	
	this.GetElement = function(section)
	{
		var id = "TicketDispenser_" + this.Name;
		if (section) id += "_" + section;
		return document.getElementById(id);
	};
	this.Dispense = function()
	{
		DispenseStep(this);
	};
}

function DispenseStep(parent)
{
	parent.Value++;
	parent.GetElement("value").innerHTML = parent.Value;
	
	if (parent.Value == parent.Amount) return;
	window.setTimeout(function() { DispenseStep(parent); }, 50);
}
