<?php
	namespace PhoenixSNS\Modules\Account\Pages;

	use WebFX\System;
	use WebFX\WebScript;
	
	use PhoenixSNS\MasterPages\WebPage;
	use PhoenixSNS\Modules\Account\UserRegistrationStatus;
	
	class RegistrationWebPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
			$this->CssClass = "RegistrationPage";
		}
		
		protected function RenderContent()
		{
		?>
		<form id="frmRegister" method="POST">
			<div class="Card">
				<div class="Title"><i class="fa fa-shield"></i> <span class="Text">Create an account</span></div>
				<div class="Content">
					<p>
						Welcome to the <?php echo(System::GetConfigurationValue("Application.Name")); ?> community! Before you can access members-only
						features, you must complete the following information. Please choose a private login ID and password for your account. Do not
						give this information to anyone else even if they are a <?php echo(System::GetConfigurationValue("Application.Name")); ?>
						team member.
					</p>
					<p>
						<table style="width: 100%">
							<tr>
								<td style="width: 300px;"><?php if ($_POST["un"] !== null && $_POST["un"] == "") echo("<span style=\"color: #FF0000\">*</span> "); ?> Login I<u>D</u> (private):</td>
								<td><input name="un" type="text" accesskey="D" size="50" maxlength="50" value="<?php echo($_POST["un"]) ?>" placeholder="ph3n1xxx" /></td>
							</tr>
							<tr>
								<td><?php if ($_POST["pw"] !== null && $_POST["pw"] == "") echo("<span style=\"color: #FF0000\">*</span> "); ?> <u>P</u>assword:</td>
								<td><input name="pw" type="password" accesskey="P" size="50" maxlength="50" placeholder="S0me*Sup3r/SecRET_passWord!" /></td>
							</tr>
							<tr>
								<td><?php if ($_POST["pwc"] !== null && $_POST["pwc"] == "") echo("<span style=\"color: #FF0000\">*</span> "); ?> <u>C</u>onfirm password:</td>
								<td><input name="pwc" type="password" accesskey="C" size="50" maxlength="50" /></td>
							</tr>
							<tr>
								<td><?php if ($_POST["ln"] !== null && $_POST["ln"] == "") echo("<span style=\"color: #FF0000\">*</span> "); ?> Display <u>n</u>ame:</td>
								<td><input id="txtLongName" name="ln" type="text" accesskey="L" size="50" maxlength="100" value="<?php echo($_POST["ln"]) ?>" onkeyup="AutoGenerateName('txtLongName', 'txtShortName');" placeholder="Phenix the Great" /></td>
							</tr>
							<tr>
								<td><?php if ($_POST["sn"] !== null && $_POST["sn"] == "") echo("<span style=\"color: #FF0000\">*</span> "); ?> <u>S</u>hort URL: <span class="ShortURLPath" style="font-size: 9pt; color: #AAAAAA; display: block; text-align: right;"><?php echo(System::ExpandRelativePath("~/community/members/")); ?></span></td>
								<td style="vertical-align: bottom;"><input id="txtShortName" name="sn" type="text" accesskey="S" size="50" maxlength="50" value="<?php echo($_POST["sn"]) ?>" onkeyup="AutoGenerateNameInvalidate('txtShortName');" placeholder="phenix" /></td>
							</tr>
							<tr>
								<td colspan="2" style="color: #FF0000; padding-top: 16px;">
								<?php
									if (
									($_POST["un"] !== null && $_POST["un"] == "") ||
									($_POST["pw"] !== null && $_POST["pw"] == "") ||
									($_POST["sn"] !== null && $_POST["sn"] == "") ||
									($_POST["ln"] !== null && $_POST["ln"] == "")
									)
									{
										echo("Your request for membership could not be completed because some required information was not provided. Please ensure that all fields marked with a red asterisk (<span style=\"color: #FF0000\">*</span>) have been completely filled out.");
									}
									else if ($UserRegistered == UserRegistrationStatus::UserNameTaken)
									{
										echo("Your request for membership could not be completed because the user name is already taken. Please choose a different user name.");
									}
									else if ($UserRegistered == UserRegistrationStatus::DisplayNameTaken)
									{
										echo("Your request for membership could not be completed because the display name is already taken. Please choose a different display name.");
									}
									else if ($UserRegistered == UserRegistrationStatus::PasswordMismatch)
									{
										echo("The password and confirmation password do not match.  Please re-enter your password and confirmation password, and then try again.");
									}
								?> 
								</td>
							</tr>
						</table>
					</p>
				</div>
				<div class="Actions Horizontal">
					<a href="#" onclick="document.getElementById('frmRegister').submit(); return false;"><i class="fa fa-check"></i> <span class="Text">Register</span></a>
					<a href="<?php echo(System::ExpandRelativePath("~/")); ?>"><i class="fa fa-times"></i> <span class="Text">Cancel</span></a>
				</div>
			</div>
		</form>
		<?php
		}
	}
?>