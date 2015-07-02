function NMDismissNotification(notif_id)
{
	var notif = document.getElementById("Notification" + notif_id);
	notif.style.opacity = 0.3;
	
	// execute the AJAX request to dismiss the notification
	$.ajax(
	{
		type: "POST",
		url: "/ajax/NotificationManager.php",
		data:
		{
			'action': 'remove',
			'id': notif_id
		},
		dataType: "json",
		success: function(data)
		{
			if (data.result == "success")
			{
				// delete the notification from the DOM if the delete succeeded
				notif.parentNode.removeChild(notif);
			}
			else
			{
				notif.style.opacity = 1;
				alert(data.message);
			}
		},
		error: function()
		{
			notif.style.opacity = 1;
			alert("An unexpected error has occurred.  Please report this to the developers.");
		}
	});
}