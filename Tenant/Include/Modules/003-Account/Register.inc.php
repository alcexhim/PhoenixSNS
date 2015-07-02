<?php
	use WebFX\System;
	
	use PhoenixSNS\Objects\MarketResourceTransaction;
	use PhoenixSNS\Objects\Tenant;
	
	use PhoenixSNS\Objects\TenantObject;
	use PhoenixSNS\Objects\TenantQueryParameter;
	use PhoenixSNS\Objects\TenantObjectInstancePropertyValue;
	use PhoenixSNS\Objects\TenantObjectMethodParameterValue;
	
	use PhoenixSNS\Objects\User;
	
	use PhoenixSNS\MasterPages\MessagePage;
	use PhoenixSNS\Pages\ErrorPage;
	
	require_once("Pages/RegistrationWebPage.inc.php");
	use PhoenixSNS\Modules\Account\Pages\RegistrationWebPage;
	
	\Enum::Create("PhoenixSNS\\Modules\\Account\\UserRegistrationStatus", "Unregistered", "AwaitingVerification", "Registered", "GeneralError", "VerificationCodeInvalid", "UserNameTaken", "DisplayNameTaken", "PasswordMismatch");
	use PhoenixSNS\Modules\Account\UserRegistrationStatus;
	
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	class RegistrationManager
	{
		public static $ErrorCode;
		public static $ErrorMessage;
		
		public static function ActivateAccountByValidationCode($code)
		{
			global $MySQL;
			
			$CurrentTenant = Tenant::GetCurrent();
			
			// Does the User object have an instance with that e-mail confirmation code?
			$result = $CurrentTenant->GetObject("User")->CountInstances
			(
				array
				(
					new TenantQueryParameter("EmailConfirmationCode", $code)
				)
			);
			
			if ($result == 1)
			{
				// enable the user
				$inst = $CurrentTenant->GetObject("User")->GetInstance
				(
					array
					(
						new TenantQueryParameter("EmailConfirmationCode", $code)
					)
				);
				
				$inst->ClearPropertyValue("EmailConfirmationCode");
				$inst->SetPropertyValue("RegistrationDate", date_create());
				
				return UserRegistrationStatus::Registered;
			}
			return UserRegistrationStatus::VerificationCodeInvalid;
		}
		
		public static function RegisterUser($loginID, $password, $longName, $shortName, $emailAddress = null)
		{
			$CurrentTenant = Tenant::GetCurrent();
			
			// create an instance of the User object in this tenant
			$obj = $CurrentTenant->GetObject("User");
			
			// retrieve the salted password from the User object
			$pwsalt = $obj->GetMethod("SaltPassword")->Execute();
			
			// tell the User object to hash the password
			$pwhash = $obj->GetMethod("HashPassword")->Execute(array
			(
				new TenantObjectMethodParameterValue("input", $pwsalt . $password)
			));
			
			$inst = $obj->CreateInstance
			(
				array
				(
					new TenantObjectInstancePropertyValue($obj->GetInstanceProperty("LoginID"), $loginID),
					new TenantObjectInstancePropertyValue($obj->GetInstanceProperty("DisplayName"), $longName),
					new TenantObjectInstancePropertyValue($obj->GetInstanceProperty("URL"), $shortName),
					new TenantObjectInstancePropertyValue($obj->GetInstanceProperty("EmailAddress"), $emailAddress),
					new TenantObjectInstancePropertyValue($obj->GetInstanceProperty("PasswordHash"), $pwhash),
					new TenantObjectInstancePropertyValue($obj->GetInstanceProperty("PasswordSalt"), $pwsalt)
				)
			);
			
			if ($inst != null)
			{
				return UserRegistrationStatus::AwaitingVerification;
			}
			
			global $MySQL;
			RegistrationManager::$ErrorCode = $MySQL->errno; // DataFX::$Errors->Items[0]->Code;
			RegistrationManager::$ErrorMessage = $MySQL->error; // DataFX::$Errors->Items[0]->Message;
			return UserRegistrationStatus::GeneralError;
		}
	}
	
	$path = System::GetVirtualPath();
	if (isset($_GET["code"]))
	{
		$UserRegistered = RegistrationManager::ActivateAccountByValidationCode($_GET["code"]);
	}
	else if ($_POST["un"] != null && $_POST["pw"] != null && $_POST["pwc"] != null && $_POST["ln"] != null && $_POST["sn"] != null)
	{
		if ($_POST["pw"] != $_POST["pwc"])
		{
			$UserRegistered = UserRegistrationStatus::PasswordMismatch;
		}
		else
		{
			// Add the new entry for this user in the database
			$UserRegistered = RegistrationManager::RegisterUser($_POST["un"], $_POST["pw"], $_POST["ln"], $_POST["sn"]);
		}
	}
	else
	{
		$UserRegistered = UserRegistrationStatus::Unregistered;
	}
	
	if ($UserRegistered == UserRegistrationStatus::Unregistered || $UserRegistered == UserRegistrationStatus::UserNameTaken || $UserRegistered == UserRegistrationStatus::DisplayNameTaken || $UserRegistered == UserRegistrationStatus::PasswordMismatch)
	{
		$page = new RegistrationWebPage();
		$page->Render();
	}
	else if ($UserRegistered == UserRegistrationStatus::AwaitingVerification)
	{
		if (System::$Configuration["Account.Registration.EmailVerification.Enabled"])
		{
			$page = new MessagePage("Register");
			$page->Message =	"Thank you for your interest in becoming a member of the " . System::GetConfigurationValue("Application.Name") . " community. Before your request can be processed, " .
								"you must verify your e-mail address. A verification e-mail has been sent to <strong>" . $_POST["em"] . "</strong>. Please click on the link in the " .
								"verification e-mail to complete your registration.";
			$page->Render();
		}
		else
		{
			$page = new MessagePage("Register");
			
			$message = "<p>Thank you for your interest in becoming a member of the " . System::GetConfigurationValue("Application.Name") . " community. Your request has been processed, and you may now log in to the site.</p>";
			
			$resources = MarketResourceTransaction::GetInitialResources();
			if (count($resources) > 0)
			{
				$message .= "<p>You have received the following as a thank-you gift for joining our community.</p>";
				$message .= "<div style=\"text-align: center;\">";
				foreach ($resources as $resource)
				{
					$title = ($resource->Amount == 1 ? $resource->ResourceType->TitleSingular : $resource->ResourceType->TitlePlural);
					$message .= "<div class=\"Resource\">";
					$message .= "<div class=\"Icon\"><img alt=\"" . $title . "\" src=\"" . System::ExpandRelativePath("~/images/resources/24x24/" . $resource->ResourceType->ID . ".png") . "\" title=\"" . $title . "\" /></div>";
					$message .= "<div class=\"Value\">" . $resource->Amount . "</div>";
					$message .= "</div>";
				}
				$message .= "</div>";
			}
			
			$page->Message = $message;
			$page->ReturnButtonText = "Log In to " . System::GetConfigurationValue("Application.Name");
			$page->ReturnButtonURL = System::ExpandRelativePath(System::GetConfigurationValue("Account.LoginPath"));
			$page->Render();
		}
	}
	else if ($UserRegistered == UserRegistrationStatus::Registered)
	{
		$page = new MessagePage("Success");
		$page->Message = "Your membership request has been approved. You may now <a href=\"" . System::ExpandRelativePath(System::GetConfigurationValue("Account.LoginPath")) . "\">log in</a> to continue using the site.";
		$page->Render();
	}
	else if ($UserRegistered == UserRegistrationStatus::GeneralError)
	{
		$page = new ErrorPage();
		$page->Message =	"Thank you for your interest in becoming a member of the " . System::GetConfigurationValue("Application.Name") . " community. Unfortunately, an error has occurred " .
							"while attempting to process your request. You can try refreshing the page, or (better yet) clicking the &quot;Log In/Register&quot; link again and filling out " .
							"the form manually. If this continues to happen, please <a href=\"mailto:webmaster@alceproject.net\">e-mail the administrator</a> and explain the issue you are " .
							"having.";
		$page->ErrorCode = RegistrationManager::$ErrorCode;
		$page->ErrorDescription = RegistrationManager::$ErrorMessage;
		$page->Render();
	}
	else if ($UserRegistered == UserRegistrationStatus::VerificationCodeInvalid)
	{
		$page = new ErrorPage();
		$page->Message =	"The user verification code that you have provided is invalid. Your session may have expired, or an internal error may have occurred. You can try clicking " .
							"the &quot;Log In/Register&quot; link and filling out the registration form again. If this continues to happen, please " .
							"<a href=\"mailto:webmaster@alceproject.net\">e-mail the administrator</a> and explain the issue you are having.";
		$page->Render();
	}
?>