function ValidatePassword()
{
	var frmLogin = document.getElementById("frmLogin");
	var txtUserName = document.getElementById("txtSidebarUserName");
	var txtPassword = document.getElementById("txtSidebarPassword");
	
	var imgSidebarLoginSpinner = document.getElementById("imgSidebarLoginSpinner");
	var cmdSidebarLogin = document.getElementById("cmdSidebarLogin");
	
	cmdSidebarLogin.style.display = "none";
	imgSidebarLoginSpinner.style.display = "inline";
	
	return $.ajax(
	{
		async: false,
		type: "POST",
		url: "/ajax/ValidateCredentials.php",
		data:
		{
			'username': txtUserName.value,
			'password': txtPassword.value
		},
		dataType: "json",
		success: function(data)
		{
			if (data.result == "reset")
			{
				window.location.href = "/account/login?action=reset&member_username=" + txtUserName.value;
				return;
			}
			else if (data.result == "expired")
			{
				imgSidebarLoginSpinner.style.display = "none";
				alert("Sorry, you entered the incorrect password too many times. Please contact an administrator to reset your password.");
				cmdSidebarLogin.style.display = "inline";
				return;
			}
			else if (data.result == "failure")
			{
				imgSidebarLoginSpinner.style.display = "none";
				alert("The user name or password you entered is incorrect.  Please check to ensure you typed it correctly.");
				cmdSidebarLogin.style.display = "inline";
				return;
			}
			
			frmLogin.onsubmit = "";
			frmLogin.submit();
		},
		error: function()
		{
			alert("Could not process your login credentials");
			cmdSidebarLogin.style.display = "inline";
			imgSidebarLoginSpinner.style.display = "none";
		}
	});
}