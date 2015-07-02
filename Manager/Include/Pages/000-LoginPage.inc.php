<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	use PhoenixSNS\Objects\Tenant;
	
	class LoginPage extends WebPage
	{
		public $InvalidCredentials;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "Log in to PhoenixSNS";
			$this->Subtitle = "You must log in to view this page";
			
			$this->RenderHeader = false;
			$this->RenderSidebar = false;
		}
		
		protected function RenderContent()
		{
		?>
		<form method="POST">
			<div class="LoginContainer">
				<div class="Billboard">
					<img src="<?php echo(System::ExpandRelativePath("~/Images/Billboard.png")); ?>" />
				</div>
				<div class="Branding">
					Log in to <span class="ProductName"><?php echo(System::GetConfigurationValue("Application.Name")); ?></span>
				</div>
				<div class="Credentials">
					<p><?php echo(System::GetConfigurationValue("Manager.LoginScreen.WelcomeMessage")); ?></p>
					<div class="FormView">
						<div class="Field">
							<label for="txtUserName">User <u>N</u>ame</label>
							<input type="text" name="user_LoginID" id="txtUserName" placeholder="User name" />
						</div>
						<div class="Field">
							<label for="txtPassword"><u>P</u>assword</label>
							<input type="password" name="user_Password" id="txtPassword" placeholder="Password" />
						</div>
						<?php
						if ($this->InvalidCredentials)
						{
						?>
						<div class="Field">
							<span class="ErrorMessage">Incorrect user name or password. Please try again.</span>
						</div>
						<?php
						}
						?>
					</div>
					<div class="ButtonContainer">
						<input type="submit" value="Continue" />
					</div>
				</div>
				<div class="Footer">
					<?php echo(System::GetConfigurationValue("Manager.LoginScreen.FooterMessage")); ?>
				</div>
			</div>
		</form>
		<?php
		}
	}
?>