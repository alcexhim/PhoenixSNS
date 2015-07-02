<?php
	use WebFX\Module;
	use WebFX\ModulePage;
	use WebFX\System;
	
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\TextBox;
	
	use PhoenixSNS\Objects\User;
	use PhoenixSNS\Objects\Language;
	use PhoenixSNS\Objects\LanguageString;
	
	use PhoenixSNS\MasterPages\WebPage;
	use PhoenixSNS\MasterPages\MessagePage;
	use PhoenixSNS\Pages\ErrorPage;
	
	function AutoRedirect()
	{
		$loc = $_POST["AccountLoginRedirect"];
		if ($loc == null) $loc = $_SESSION["AccountLoginRedirect"];
		if ($loc == null) $loc = "~/";
		$_SESSION["AccountLoginRedirect"] = null;
		System::Redirect($loc);
		return true;
	}
	function NotifyUserPresence($user, $login = true)
	{
		global $MySQL;
		if ($login)
		{
			$user->Login();
		}
		else
		{
			$user->Logout();
		}
	}
	
	System::$Modules[] = new Module("net.phoenixsns.AccountManagement", array
	(
		new ModulePage("account", array
		(
			new ModulePage("password", array
			(
				new ModulePage("reset.page", function($path)
				{
					if (count($path) == 3 && $path[2] != "")
					{
						global $MySQL;
						$query = "UPDATE phpmmo_members SET member_emailcode = NULL, member_password = NULL WHERE member_emailcode = '" . $MySQL->real_escape_string($path[2]) . "'";
						$result = $MySQL->query($query);
						
						$count = mysql_affected_rows();
						
						$errno = $MySQL->errno;
						$error = $MySQL->error;
						
						if ($errno == 0 && $count == 1)
						{
							$page = new MessagePage("Password reset successful");
							$page->Message = "You may now log in to the site to change your password.";
							$page->ReturnButtonURL = System::$Configuration["Account.LoginPath"];
							$page->ReturnButtonText = "Change Password";
							$page->Render();
							return true;
						}
						else
						{
							$page = new ErrorPage();
							if ($errno == 0)
							{
								$page->Message = "The password could not be reset.  Please check to ensure that you clicked on the correct link.";
							}
							else
							{
								$page->Message = "There was an error connecting to the database.  Please submit a support ticket and provide the staff with this information.";
							}
							$page->ErrorCode = $errno;
							$page->ErrorDescription = $error;
							$page->ReturnButtonURL = System::$Configuration["Account.ResetPasswordPath"];
							$page->ReturnButtonText = "Return to Reset Password";
							$page->Render();
							return true;
						}
						return true;
					}
					
					if ($_SERVER["REQUEST_METHOD"] == "POST")
					{
						if ($_POST["reset_type"] == "1")
						{
							if ($_POST["member_email"] != null)
							{
								$users = User::GetByEmail($_POST["member_email"]);
								$count = count($users);
								if ($count == 0)
								{
									$page = new MessagePage("Member Not Found");
									$page->Message = "Sorry, there's no member with that e-mail address registered on this site.";
									$page->ReturnButtonURL = System::$Configuration["Account.ResetPasswordPath"];
									$page->ReturnButtonText = "Return to Password Reset";
									$page->Render();
									return true;
								}
								else if ($count == 1)
								{
									$page = new MessagePage("Confirm your e-mail address");
									$page->Message = "An e-mail has been sent to you with a link to confirm your e-mail address. Click on the link in the e-mail to reset your password.";
									$page->Render();
									return true;
								}
								else
								{
									$page = new WebPage("Who are you?");
									$page->BeginContent();
									?>
									<div class="Panel">
										<h3 class="PanelTitle">Who are you?</h3>
										<div class="PanelContent">
											<form action="reset" method="POST">
												<p>That e-mail address is registered to more than one member.  Please select the account you wish to restore.</p>
												<div>
												<?php
												foreach ($users as $user)
												{
													?>
													<div>
														<input type="radio" id="optMember<?php echo($user->ID); ?>" name="member_id" value="<?php echo($user->ID); ?>" /> <label for="optMember<?php echo($user->ID); ?>"><?php echo($user->LongName); ?> (<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $user->ShortName)); ?>" target="_blank">view profile</a>)</label>
													</div>
													<?php
												}
												?>
												</div>
												<div style="text-align: center;">
													<input class="LinkButton" type="submit" value="Reset Password" />
												</div>
											</form>
										</div>
									</div>
									<?php
									$page->EndContent();
									return true;
								}
							}
							else
							{
								if ($_POST["member_id"] != null)
								{
									$id = $_POST["member_id"];
									$member = User::GetByID($id);
									
									$member_emailcode = get_random_string("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890", 16);
									$query = "UPDATE phpmmo_members SET member_emailcode = '" . $member_emailcode . "' WHERE member_id = " . $id;
									$result = $MySQL->query($query);
									
									$errno = $MySQL->errno;
									$error = $MySQL->error;
									if ($errno != 0)
									{
										$page = new ErrorPage();
										$page->Mesasge = "An error has occurred, and the confirmation e-mail could not be sent.";
										$page->ReturnButtonURL = System::$Configuration["Account.ResetPasswordPath"];
										$page->ReturnButtonText = "Return to Password Reset";
										$page->Render();
										return true;
									}
									
									$result = mail($member->EmailAddress, "Password reset confirmation", "<p>Before you can reset your password, you must confirm your e-mail address. Please click the link below (or copy-and-paste it into your browser's address bar) to continue.</p><p><a href=\"" . System::ExpandRelativePath(System::$Configuration["Account.ResetPasswordPath"]) . "/" . $member_emailcode . "\">" . System::ExpandRelativePath(System::$Configuration["Account.ResetPasswordPath"]) . "/" . $member_emailcode . "</a></p><p>Thank you for being a part of Psychatica!</p>", "From: noreply@psychatica.com\r\nContent-Type: text/html");
									if (!$result)
									{
										$page = new ErrorPage();
										$page->Message = "An error has occurred, and the confirmation e-mail could not be sent.";
										$page->ReturnButtonURL = System::$Configuration["Account.ResetPasswordPath"];
										$page->ReturnButtonText = "Return to Password Reset";
										$page->Render();
										return true;
									}
									else
									{
										$page = new MessagePage();
										$page->Message = "The confirmation e-mail has been sent.  Please remember to click the link in the confirmation e-mail in order to reset your password.";
										$page->Render();
										return true;
									}
								}
								
								System::Redirect(System::$Configuration["Account.ResetPasswordPath"]);
								return true;
							}
						}
						else if ($_POST["reset_type"] == "2")
						{
							$query = "UPDATE phpmmo_members SET member_password_reset_code = NULL, member_password = '" . $MySQL->real_escape_string(hash("sha512", $_POST["member_password"])) . "' WHERE member_password_reset_code = '" . $MySQL->real_escape_string($_POST["member_password_reset_code"]) . "'";
							$result = $MySQL->query($query);
							
							$count = mysql_affected_rows();
							
							$errno = $MySQL->errno;
							$error = $MySQL->error;
							
							if ($errno == 0 && $count == 1)
							{
								$page = new MessagePage("Password reset successful");
								$page->Message = "You may now log in to the site using the password you just entered.";
								$page->ReturnButtonURL = "~/";
								$page->ReturnButtonText = "Return to Log In";
								$page->Render();
								return true;
							}
							else
							{
								$page = new ErrorPage();
								if ($errno == 0)
								{
									$page->Message = "The password could not be reset.  Please check to ensure that you typed the verification code correctly.";
								}
								else
								{
									$page->Message = "There was an error connecting to the database.  Please submit a support ticket and provide the staff with this information.";
								}
								$page->ErrorCode = $errno;
								$page->ErrorDescription = $error;
								$page->ReturnButtonURL = System::$Configuration["Account.ResetPasswordPath"];
								$page->ReturnButtonText = "Return to Reset Password";
								$page->Render();
								return true;
							}
							
							System::Redirect(System::$Configuration["Account.LoginPath"]);
							return true;
						}
					}
					
					$page = new WebPage("Reset Password");
					$page->BeginContent();
					?>
					<form method="POST" action="<?php echo(System::ExpandRelativePath("~/account/password/reset")); ?>">
						<table style="width: 100%">
							<tr>
								<td><input type="radio" name="reset_type" value="1" id="optResetTypeEmail" /> <label for="optResetTypeEmail">I want to reset my password with a verification e-mail</label></td>
							</tr>
							<tr>
								<td><input type="radio" name="reset_type" value="2" id="optResetTypeAdmin" <?php if ($_GET["code"] != null) { echo("checked=\"checked\""); } ?>/> <label for="optResetTypeAdmin">An administrator has reset my password and given me a reset code</label></td>
							</tr>
							<tr>
								<td id="pnlResetPasswordTypeEmail">
									<p>
										Please provide the e-mail address you used to register your account.  A confirmation e-mail will be sent to this e-mail
										address so that you may reset your password.
									</p>
									<p>
										<table style="margin-left: auto; margin-right: auto">
											<tr>
												<td><label for="txtEmailAddress">E-mail address:</label></td>
												<td><input type="text" id="txtEmailAddress" name="member_email" /></td>
											</tr>
											<tr>
												<td colspan="2" style="text-align: right;">
													<input type="submit" value="Reset Password" />
													<a class="Button" href="/">Cancel</a>
												</td>
											</tr>
										</table>
									</p>
								</td>
							</tr>
							<tr>
								<td id="pnlResetPasswordTypeAdmin">
									<p>
										Please enter the password reset verification code the administrator has given you.  Then enter your new password and
										click the Change Password button to complete the process.
									</p>
									<p>
										<table style="margin-left: auto; margin-right: auto">
											<tr>
												<td><label for="txtVerificationCode">Verification code:</label></td>
												<td><input type="text" id="txtVerificationCode" name="member_password_reset_code" value="<?php echo($_GET["code"]); ?>" /></td>
											</tr>
											<tr>
												<td><label for="txtPassword">New password:</label></td>
												<td><input type="password" id="txtPassword" name="member_password" /></td>
											</tr>
											<tr>
												<td><label for="txtPasswordConfirm">Confirm new password:</label></td>
												<td><input type="password" id="txtPasswordConfirm" name="member_password_confirm" /></td>
											</tr>
											<tr>
												<td colspan="2" style="text-align: right;">
													<input type="submit" value="Change Password" />
													<a class="Button" href="/">Cancel</a>
												</td>
											</tr>
										</table>
									</p>
								</td>
							</tr>
						</table>
					</form>
					<script type="text/javascript">
					var optResetTypeAdmin = document.getElementById("optResetTypeAdmin");
					var optResetTypeEmail = document.getElementById("optResetTypeEmail");
					var pnlResetPasswordTypeAdmin = document.getElementById("pnlResetPasswordTypeAdmin");
					var pnlResetPasswordTypeEmail = document.getElementById("pnlResetPasswordTypeEmail");
					
					<?php if ($_GET["code"] == null) { ?>pnlResetPasswordTypeAdmin.style.display = "none";<?php } ?>
					pnlResetPasswordTypeEmail.style.display = "none";
						
					function UpdatePanels()
					{
						if (optResetTypeAdmin.checked)
						{
							pnlResetPasswordTypeAdmin.style.display = "table-cell";
							pnlResetPasswordTypeEmail.style.display = "none";
						}
						else if (optResetTypeEmail.checked)
						{
							pnlResetPasswordTypeAdmin.style.display = "none";
							pnlResetPasswordTypeEmail.style.display = "table-cell";
						}
					}
					
					optResetTypeAdmin.onchange = function()
					{
						UpdatePanels();
					};
					optResetTypeEmail.onchange = function()
					{
						UpdatePanels();
					};
					</script>
					<?php
					$page->EndContent();
					return true;
				})
			)),
			new ModulePage("register.page", function($path)
			{
				require("Register.inc.php");
			}),
			new ModulePage("notifications", function($path)
			{
				require("Notifications.inc.php");
			}),
			new ModulePage("settings", function($path)
			{
				require("Settings.inc.php");
			},
			function($path)
			{
				// before execute /settings
				$CurrentUser = User::GetCurrent();
				if ($CurrentUser == null)
				{
					$_SESSION["LoginRedirectURL"] = "~/account/settings/" . implode("/", $path);
					System::Redirect("~/account/login.page");
				}
			})
		))
	));
?>