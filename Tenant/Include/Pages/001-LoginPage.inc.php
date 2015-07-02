<?php
	namespace PhoenixSNS\Pages;
	
	use WebFX\System;
	use WebFX\WebStyleSheet;
	
	use WebFX\Controls\Window;
	
	use PhoenixSNS\MasterPages\WebPage;
	
	class LoginPageBase extends WebPage
	{
		public $LoginButtonText;
		public $InvalidCredentials;
		public $Message;
		
		protected function RenderStory()
		{
		}
		
		public function __construct()
		{
			parent::__construct();
			
			$this->StyleSheets[] = new WebStyleSheet("~/style/LoginPage.css");
			
			$this->Title = "Log in to " . System::GetConfigurationValue("Application.Name");
			$this->CssClass = "LoginPage";
			$this->UseCompatibleRenderingMode = true;
			$this->LoginButtonText = "Log In";
			$this->Message = "";
			
			$this->InvalidCredentials = false;
		}
		protected function BeforeContent()
		{
			// do nothing
			
		}
		protected function BeforeRenderContent()
		{
			// do nothing
		}
		protected function RenderContent()
		{
			$wndHelpWithServer = new Window("wndHelpWithServer", "About the &quot;Server&quot; parameter");
			$wndHelpWithServer->Width = 600;
			$wndHelpWithServer->Visible = false;
			$wndHelpWithServer->BeginContent();
?>
<p>
	Enter the server name on which you registered your PhoenixSNS account. Because PhoenixSNS accounts are decentralized, you may log into multiple sites
	using the same credentials. Whenever you log in, PhoenixSNS will contact your identity provider and retrieve information that you have shared between
	your identity provider and <?php echo(System::GetConfigurationValue("Application.Name")); ?>.
</p>
<p>
	You have total control over what information gets sent from <?php echo(System::GetConfigurationValue("Application.Name")); ?> to your Identity Provider and vice versa. Your privacy and cross-posting options
	can be changed from your User Control Panel.
</p>
<?php
			$wndHelpWithServer->BeginButtons();
?>
<div style="text-align: center;"><input type="submit" value="OK" onclick="wndHelpWithServer.Hide(); return false;" /></div>
<?php
			$wndHelpWithServer->EndButtons();
			$wndHelpWithServer->EndContent();

?>
<div class="LogoAndLoginArea">
	<div class="LogoArea">
		<img class="Logo" src="<?php echo(System::ExpandRelativePath("~/images/Logo.png")); ?>" alt="<?php echo(System::GetConfigurationValue("Application.Name")); ?>" />
		<p class="Slogan"><?php echo(System::GetConfigurationValue("Application.Slogan")); ?></p>
	</div>
	<form class="LoginForm" method="POST" action="<?php echo(System::ExpandRelativePath(System::GetConfigurationValue("Account.LoginPath"))); ?>" style="margin-left: auto; margin-right: auto;">
		<div class="Field">
			<label for="txtUserName">Login <u>n</u>ame:</label>
			<input type="text" id="txtUserName" name="member_username" value="" style="width: 100%;" />
		</div>
		<div class="Field">
			<label for="txtPassword"><u>P</u>assword:</label>
			<input type="password" id="txtPassword" name="member_password" value="" style="width: 100%;" />
		</div>
		<div class="Field">
			<label for="txtServerName" id="lblServerName"><u>S</u>erver:</label>
			<input type="text" id="txtServerName" accesskey="S" name="member_servername" value="<?php echo(System::GetConfigurationValue("Application.DomainName")); ?>" style="width: 100%;" /> <a href="#" onclick="wndHelpWithServer.ShowDialog();"><img style="vertical-align: middle;" src="<?php echo(System::ExpandRelativePath("~/images/icons/help.png")); ?>" alt="(?)" title="More information" /></a>
		</div>
		<?php
		if ($this->InvalidCredentials)
		{
		?>
		<div class="Message Error">
			The login name or password you specified is invalid.  Please try again.
		</div>
		<?php
		}
		?>
		<div class="Buttons">
			<input type="submit" value="Log In" onclick="frmLogin.submit(); return false;" />
		</div>
	</form>
	<p class="LoginMessage" style="font-style: oblique;"><?php echo($this->Message); ?></p>
	<div class="ActionList">
		<a class="Action" href="<?php echo(System::ExpandRelativePath(System::GetConfigurationValue("Account.RegisterPath"))); ?>">
			<span class="ActionTitle">Don't have an account?</span>
			<span class="ActionLink">Register now!</span>
		</a>
		<a class="Action" href="<?php echo(System::ExpandRelativePath(System::GetConfigurationValue("Account.ResetPasswordPath"))); ?>">
			<span class="ActionTitle">Forgot your password?</span>
			<span class="ActionLink">Reset it here.</span>
		</a>
		<a class="Action" href="<?php echo(System::ExpandRelativePath("~/contribute")); ?>">
			<span class="ActionTitle">Want to help out?</span>
			<span class="ActionLink">See ways you can contribute.</span>
		</a>
	</div>
</div>

<script type='text/javascript'>
	// TEST!!!
	/*
	var supportsVibrate = "vibrate" in navigator;
	
	// Vibrate multiple times for multiple durations
	// Vibrate for three seconds, wait two seconds, then vibrate for one second
	navigator.vibrate([500, 500, 500, 500, 500]);
	*/
</script>

<?php
		}
		protected function AfterRenderContent()
		{
			// do nothing
		}
		protected function AfterContent()
		{
			// do nothing
		}
	}
	
	class LoginPage extends LoginPageBase
	{
	}
?>