<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use WebFX\Controls\AdditionalDetailWidget;
	use WebFX\Controls\AdditionalDetailWidgetDisplayStyle;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\Disclosure;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	use PhoenixSNS\Objects\Tenant;
	
	class PasswordResetPage extends WebPage
	{
		public $CurrentObject;
		
		public function __construct()
		{
			parent::__construct();
			$this->Title = "Reset Password";
			
			$this->RenderHeader = false;
			$this->RenderSidebar = false;
		}
		
		protected function RenderContent()
		{
			?>
			<form method="POST">
				<table>
					<tr>
						<td><label for="txtUserName">Login ID:</label></td>
						<td><?php echo($this->CurrentObject->UserName); ?></td>
					</tr>
					<tr>
						<td><label for="txtOldPassword">Old password:</label></td>
						<td><input type="password" id="txtOldPassword" name="user_PasswordOld" /></td>
					</tr>
					<tr>
						<td><label for="txtNewPassword">New password:</label></td>
						<td><input type="password" id="txtNewPassword" name="user_Password" /></td>
					</tr>
					<tr>
						<td><label for="txtNewPasswordConfirm">Confirm new password:</label></td>
						<td><input type="password" id="txtNewPasswordConfirm" name="user_PasswordConfirm" /></td>
					</tr>
				</table>
				<div class="Buttons">
					<input class="Button Default" type="submit" value="Reset Password" />
					<a class="Button" href="#" onclick="history.back();">Cancel</a>
				</div>
			</form>
			<?php
		}
	}
?>