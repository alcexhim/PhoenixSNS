<?php
	namespace WebFXTest
	{
		use WebFX\System;
		use WebFX\HTMLControl;
		use WebFX\WebControlAttribute;
		
		use PhoenixSNS\Objects\User;
		
		class LoginPage
		{
			public function OnInit()
			{
				if ($this->IsPostback)
				{
					if (isset($_POST["user_LoginID"]) && isset($_POST["user_Password"]))
					{
						$admun = $_POST["user_LoginID"];
						$admpw = $_POST["user_Password"];
						
						$user = User::GetByCredentials($admun, $admpw);
						
						if ($user != null)
						{
							if ($user->ForcePasswordChange)
							{
								$_SESSION["ResetPasswordUserID"] = $user->ID;
								System::Redirect("~/account/resetPassword.page");
							}
							else
							{
								$_SESSION["Authentication.UserName"] = $admun;
								$_SESSION["Authentication.Password"] = $admpw;
								
								if (isset($_SESSION["LoginRedirectURL"]))
								{
									System::Redirect($_SESSION["LoginRedirectURL"]);
								}
								else
								{
									System::Redirect("~/");
								}
							}
							return true;
						}
						else
						{
							$script = new HTMLControl();
							$script->TagName = "script";
							$script->Attributes[] = new WebControlAttribute("type", "text/javascript");
							$script->InnerHTML = "window.addEventListener(\"load\", function() { Notification.Show('The user name or password you entered is incorrect', 'Invalid Credentials', 'Error'); });";
							
							// child control has to go into the Section (which is control at index 1 on the page)
							$this->Controls[1]->Controls[] = $script;
						}
					}
				}
			}
		}
	}
?>