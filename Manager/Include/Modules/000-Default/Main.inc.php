<?php
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	
	use PhoenixSNS\TenantManager\Pages\DashboardPage;
	
	use PhoenixSNS\TenantManager\Pages\LoginPage;
	use PhoenixSNS\TenantManager\Pages\ErrorPage;
	
	use PhoenixSNS\TenantManager\Pages\ModuleMainPage;
	use PhoenixSNS\TenantManager\Pages\ModuleManagementPage;
	
	use PhoenixSNS\TenantManager\Pages\TenantPropertiesPage;
	use PhoenixSNS\TenantManager\Pages\TenantMainPage;
	
	use PhoenixSNS\TenantManager\Pages\TenantManagementPage;
	use PhoenixSNS\TenantManager\Pages\TenantManagementPageMode;
	
	use PhoenixSNS\TenantManager\Pages\TenantModuleManagementPage;
	
	use PhoenixSNS\TenantManager\Pages\TenantObjectManagementPage;
	
	use PhoenixSNS\TenantManager\Pages\TenantObjectInstanceBrowsePage;
	
	use PhoenixSNS\TenantManager\Pages\TenantObjectMethodManagementPage;
	
	use PhoenixSNS\TenantManager\Pages\ConfirmOperationPage;
	
	use PhoenixSNS\TenantManager\Pages\PasswordResetPage;
	
	use PhoenixSNS\Objects\DataCenter;
	use PhoenixSNS\Objects\DataType;
	use PhoenixSNS\Objects\PaymentPlan;
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantObject;
	use PhoenixSNS\Objects\TenantObjectMethod;
	use PhoenixSNS\Objects\TenantObjectInstanceMethod;
	use PhoenixSNS\Objects\TenantObjectInstanceProperty;
	use PhoenixSNS\Objects\TenantStatus;
	use PhoenixSNS\Objects\TenantType;
	use PhoenixSNS\Objects\TenantObjectMethodParameterValue;
	use PhoenixSNS\Objects\User;
	
	function IsConfigured()
	{
		if (!(
			isset(System::$Configuration["Database.ServerName"]) &&
			isset(System::$Configuration["Database.DatabaseName"]) &&
			isset(System::$Configuration["Database.UserName"]) &&
			isset(System::$Configuration["Database.Password"]) &&
			isset(System::$Configuration["Database.TablePrefix"])
		))
		{
			return false;
		}
		
		global $MySQL;
		$query = "SHOW TABLES LIKE '" . System::$Configuration["Database.TablePrefix"] . "Tenants'";
		$result = $MySQL->query($query);
		if ($result->num_rows < 1) return false;
		return true;
	}
	
	function IsAdministrator()
	{
		if (!isset($_SESSION["Authentication.UserName"]) || !isset($_SESSION["Authentication.Password"])) return false;
		
		$username = $_SESSION["Authentication.UserName"];
		$password = $_SESSION["Authentication.Password"];
		
		$user = User::GetByCredentials($username, $password);
		if ($user == null) return false;
		
		$_SESSION["Authentication.UserID"] = $user->ID;
		return true;
	}
	
	System::$BeforeLaunchEventHandler = function($path)
	{
		if (!IsConfigured() && (!($path[0] == "setup")))
		{
			System::Redirect("~/setup");
			return true;
		}
		
		if (!IsAdministrator() && (!($path[0] == "account" && ($path[1] == "login.page" || $path[1] == "resetPassword.page"))) && (!($path[0] == "setup")) && (!($path[0] == "favicon.ico")))
		{
			$path1 = implode("/", $path);
			$_SESSION["LoginRedirectURL"] = "~/" . $path1;
			
			System::Redirect("~/account/login.page");
			return true;
		}
		return true;
	};
	
	require ("Tasks.inc.php");
	
	System::$Modules[] = new Module("net.phoenixsns.TenantManager.Default", array
	(
		new ModulePage("", function($page, $path)
		{
			$page = new DashboardPage();
			$page->Render();
			return true;
		}),
		new ModulePage("system-log", function($page, $path)
		{
			global $MySQL;
			
			$page = new WebPage();
			$page->Title = "System Log";
			
			$page->BeginContent();
			if (is_numeric($path[0]))
			{
				if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "delete")
				{
					$query = "DELETE FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DebugMessages WHERE message_ID = " . $path[0];
					$result = $MySQL->query($query);
					System::Redirect("~/system-log");
				}
				else
				{
					$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "DebugMessages WHERE message_ID = " . $path[0];
					$result = $MySQL->query($query);
					$values = $result->fetch_assoc();
					
					echo("<h1>Error Details</h1>");
					echo("<p>" . $values["message_Content"] . "</p>");
					
					echo("<h2>Parameters</h2>");
					echo("<table class=\"ListView\">");
					$query1 = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "DebugMessageParameters WHERE mp_MessageID = " . $values["message_ID"];
					$result1 = $MySQL->query($query1);
					$count1 = $result1->num_rows;
					echo("<tr>");
					echo("<th>Name</th>");
					echo("<th>Value</th>");
					echo("</tr>");
					for ($j = 0; $j < $count1; $j++)
					{
						$values1 = $result1->fetch_assoc();
						echo("<tr>");
						echo("<td>");
						echo($values1["mp_Name"]);
						echo("</td>");
						echo("<td>");
						echo($values1["mp_Value"]);
						echo("</td>");
						echo("</tr>");
					}
					echo("</table>");
					
					echo("<h2>Backtrace</h2>");
					echo("<table class=\"ListView\">");
					$query1 = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "DebugMessageBacktraces WHERE bt_MessageID = " . $values["message_ID"];
					$result1 = $MySQL->query($query1);
					$count1 = $result1->num_rows;
					echo("<tr>");
					echo("<th>File name</th>");
					echo("<th>Line number</th>");
					echo("</tr>");
					for ($j = 0; $j < $count1; $j++)
					{
						$values1 = $result1->fetch_assoc();
						echo("<tr>");
						echo("<td>");
						echo($values1["bt_FileName"]);
						echo("</td>");
						echo("<td>");
						echo($values1["bt_LineNumber"]);
						echo("</td>");
						echo("</tr>");
					}
					echo("</table>");
					echo("<div class=\"Buttons\">");
					echo("<form method=\"post\">");
					echo("<input type=\"hidden\" name=\"action\" value=\"delete\" />");
					echo("<input type=\"submit\" value=\"Delete Message\" />");
					echo("<a class=\"Button\" href=\"" . System::ExpandRelativePath("~/system-log") . "\">Back to Messages</a>");
					echo("</form>");
					echo("</div>");
				}
			}
			else
			{
				if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "delete")
				{
					$query = "DELETE FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DebugMessages";
					$result = $MySQL->query($query);
					System::Redirect("~/system-log");
				}
				else
				{
					echo("<form method=\"post\">");
					echo("<input type=\"hidden\" name=\"action\" value=\"delete\" />");
					echo("<input type=\"submit\" value=\"Clear Messages\" />");
					echo("</form>");
					
					echo("<table class=\"ListView\">");
					echo("<tr>");
					echo("<th>Tenant</th>");
					echo("<th>Severity</th>");
					echo("<th>Message</th>");
					echo("<th>Timestamp</th>");
					echo("<th>IP Address</th>");
					echo("</tr>");
					
					$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "DebugMessages";
					$result = $MySQL->query($query);
					$count = $result->num_rows;
					for ($i = 0; $i < $count; $i++)
					{
						$values = $result->fetch_assoc();
						echo("<tr>");
						echo("<td>");
						$tenant = Tenant::GetByID($values["message_TenantID"]);
						if ($tenant != null)
						{
							echo("<a href=\"" . System::ExpandRelativePath("~/tenant/manage/" . $tenant->URL . "/") . "\">" . $tenant->URL . "</a>");
						}
						echo("</td>");
						echo("<td>");
						switch ($values["message_SeverityID"])
						{
						}
						echo("</td>");
						echo("<td>");
						echo("<a href=\"" . System::ExpandRelativePath("~/system-log/" . $values["message_ID"]) . "\">");
						echo($values["message_Content"]);
						echo("</a>");
						echo("</td>");
						echo("<td>");
						echo($values["message_Timestamp"]);
						echo("</td>");
						echo("<td>");
						echo($values["message_IPAddress"]);
						echo("</td>");
						echo("</tr>");
					}
					echo("</table>");
				}
			}
			$page->EndContent();
			return true;
		}),
		new ModulePage("account", array
		(
			new ModulePage("login.page", function($page, $path)
			{
				$page = new LoginPage();
				if ($_SERVER["REQUEST_METHOD"] == "POST")
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
							$page->InvalidCredentials = true;
						}
					}
				}
				$page->Render();
				return true;
			}),
			new ModulePage("logout.page", function($page, $path)
			{
				$_SESSION["Authentication.UserName"] = null;
				$_SESSION["Authentication.Password"] = null;
				System::Redirect("~/");
				return true;
			}),
			new ModulePage("resetPassword.page", function($page, $path)
			{
				$user = User::GetByID($_SESSION["ResetPasswordUserID"]);
				if ($user == null)
				{
					System::Redirect("~/");
					return true;
				}
				
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$user->Password = $_POST["user_Password"];
					$user->AccountLocked = false;
					$user->ForcePasswordChange = false;
					
					if (!$user->Update(true, $_POST["user_PasswordOld"]))
					{
						global $MySQL;
						
						if ($MySQL->errno == 0)
						{
							echo("Password hash mismatch");
						}
						else
						{
							echo($MySQL->errno . ": " . $MySQL->error);
						}
						return true;
					}
					
					$_SESSION["Authentication.Password"] = $_POST["user_Password"];
					System::Redirect("~/");
				}
				else
				{
					$page = new PasswordResetPage();
					$page->CurrentObject = $user;
					$page->Render();
				}
				return true;
			})
		)),
		new ModulePage("tenant", array
		(
			new ModulePage("", function($page, $path)
			{
				$page = new TenantMainPage();
				$page->Render();
				return true;
			}),
			new ModulePage("create", function($page, $path)
			{
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					$tenant_URL = $_POST["tenant_URL"];
					$tenant_Description = $_POST["tenant_Description"];
					
					$tenant_DataCenters = array();
					foreach ($_POST as $key => $value)
					{
						if (substr($key, 0, strlen("tenant_DataCenter_")) == "tenant_DataCenter_")
						{
							$id = substr($key, strlen("tenant_DataCenter_") + 1);
							$tenant_DataCenters[] = DataCenter::GetByID($id);
						}
					}
					
					$tenant_Status = ($_POST["tenant_Status"] == "on" ? TenantStatus::Enabled : TenantStatus::Disabled);
					$tenant_Type = TenantType::GetByID($_POST["tenant_TypeID"]);
					$tenant_PaymentPlan = PaymentPlan::GetByID($_POST["tenant_PaymentPlanID"]);
					$tenant_BeginTimestamp = ($_POST["tenant_BeginTimestampValid"] == "1" ? null : $_POST["tenant_BeginTimestamp"]);
					$tenant_EndTimestamp = ($_POST["tenant_EndTimestampValid"] == "1" ? null : $_POST["tenant_EndTimestamp"]);
					
					$retval = Tenant::Create($tenant_URL, $tenant_Description, $tenant_Status, $tenant_Type, $tenant_PaymentPlan, $tenant_BeginTimestamp, $tenant_EndTimestamp, $tenant_DataCenters);
					
					if ($retval == null)
					{
						global $MySQL;
						echo($MySQL->error . " (" . $MySQL->errno . ")");
					}
					else
					{
						System::Redirect("~/tenant");
					}
					return true;
				}
				else
				{
					$page = new TenantPropertiesPage();
					$page->Render();
					return true;
				}
			}),
			new ModulePage("clone", function($page, $path)
			{
				$sourceTenant = Tenant::GetByURL($path[0]);
				if ($sourceTenant == null)
				{
					return;
				}
				
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					$tenant_URL = $_POST["tenant_URL"];
					$tenant_Description = $_POST["tenant_Description"];
					$tenant_Status = (isset($_POST["tenant_Status"]) ? TenantStatus::Enabled : TenantStatus::Disabled);
					$tenant_Type = TenantType::GetByID($_POST["tenant_TypeID"]);
					$tenant_PaymentPlan = PaymentPlan::GetByID($_POST["tenant_PaymentPlanID"]);
					if (isset($_POST["tenant_BeginTimestampValid"]))
					{
						$tenant_BeginTimestamp = ($_POST["tenant_BeginTimestampValid"] == "on" ? null : $_POST["tenant_BeginTimestamp"]);
					}
					else
					{
						$tenant_BeginTimestamp = $_POST["tenant_BeginTimestamp"];
					}
					if (isset($_POST["tenant_EndTimestampValid"]))
					{
						$tenant_EndTimestamp = ($_POST["tenant_EndTimestampValid"] == "on" ? null : $_POST["tenant_EndTimestamp"]);
					}
					else
					{
						$tenant_EndTimestamp = $_POST["tenant_EndTimestamp"];
					}
					
					if (Tenant::Exists($tenant_URL))
					{
						$page = new ErrorPage();
						$page->Message = "The tenant '" . $tenant_URL . "' already exists.";
						$page->ReturnButtonURL = "~/tenant";
						
						$page->RenderHeader = true;
						$page->RenderSidebar = true;
						$page->Render();
						return true;
					}
					
					$retval = Tenant::Create($tenant_URL, $tenant_Description, $tenant_Status, $tenant_Type, $tenant_PaymentPlan, $tenant_BeginTimestamp, $tenant_EndTimestamp);
					
					if ($retval == null)
					{
						global $MySQL;
						echo($MySQL->error . " (" . $MySQL->errno . ")");
					}
					else
					{
						$sourceTenant->CopyTo($retval);
						System::Redirect("~/tenant");
					}
					return true;
				}
				else
				{
					$page = new TenantManagementPage();
					$page->Tenant = $sourceTenant;
					$page->Render();
					return true;
				}
			}),
			new ModulePage("delete", function($page, $path)
			{
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					if ($_POST["Confirm"] == "1")
					{
						$tenant = Tenant::GetByURL($path[0]);
						if ($tenant->Delete())
						{
							System::Redirect("~/tenant");
						}
						else
						{
							global $MySQL;
							echo($MySQL->error . " (" . $MySQL->errno . ")");
						}
					}
				}
				else
				{
					$page = new ConfirmOperationPage();
					$page->ReturnButtonURL = "~/tenant";
					$page->Message = "Are you sure you want to delete the tenant '" . $path[0] . "'? This action cannot be undone, and will destroy any and all data associated with that tenant.";
					$page->Render();
					return true;
				}
			}),
			new ModulePage("modify", function($page, $path)
			{
				if (!isset($path[1]) || $path[1] == "")
				{
					$tenant = Tenant::GetByURL($path[0]);
					System::$TenantName = $path[0];
					
					if ($_SERVER["REQUEST_METHOD"] == "POST")
					{
						$tenant->URL = $_POST["tenant_URL"];
						$tenant->Description = $_POST["tenant_Description"];
						$tenant->Status = (isset($_POST["tenant_Status"]) ? TenantStatus::Enabled : TenantStatus::Disabled);
						$tenant->Type = TenantType::GetByID($_POST["tenant_TypeID"]);
						$tenant->PaymentPlan = PaymentPlan::GetByID($_POST["tenant_PaymentPlanID"]);
						$tenant->BeginTimestamp = ($_POST["tenant_BeginTimestampValid"] == "on" ? null : $_POST["tenant_BeginTimestamp"]);
						$tenant->EndTimestamp = ($_POST["tenant_EndTimestampValid"] == "on" ? null : $_POST["tenant_EndTimestamp"]);
						
						$retval = $tenant->Update();
						if (!$retval)
						{
							global $MySQL;
							echo($MySQL->error . " (" . $MySQL->errno . ")");
						}
						else
						{
							System::$TenantName = $tenant->URL;
							
							$properties = $tenant->GetProperties();
							foreach ($properties as $property)
							{
								$tenant->SetPropertyValue($property, $_POST["Property_" . $property->ID]);
							}
							System::Redirect("~/tenant");
						}
						return true;
					}
					else
					{
						$page = new TenantManagementPage();
						$page->Mode = TenantManagementPageMode::Modify;
						$page->Tenant = $tenant;
						$page->Render();
						return true;
					}
				}
				else
				{
					switch ($path[1])
					{
						case "modules":
						{
							$page = new TenantModuleManagementPage();
							$page->Tenant = Tenant::GetByURL($path[0]);
							$page->Module = \PhoenixSNS\Objects\Module::GetByID($path[2]);
							$page->Render();
							break;
						}
						case "objects":
						{
							if ($path[2] == "")
							{
								// $page = new TenantObjectBrowsePage();
								// $page->CurrentTenant = Tenant::GetByURL($path[0]);
								// $page->Render();
							}
							else
							{
								switch ($path[3])
								{
									case "instances":
									{
										switch ($path[4])
										{
											case "":
											{
												$tenant = Tenant::GetByURL($path[0]);
												$object = TenantObject::GetByID($path[2]);
												
												$page = new TenantObjectInstanceBrowsePage();
												$page->CurrentTenant = $tenant;
												$page->CurrentObject = $object;
												$page->Render();
												break;
											}
										}
									}
									case "methods":
									{
										switch ($path[4])
										{
											case "static":
											{
												$tenant = Tenant::GetByURL($path[0]);
												$object = TenantObject::GetByID($path[2]);
												$method = TenantObjectMethod::GetByID($path[5]);
												
												if ($_SERVER["REQUEST_METHOD"] == "POST")
												{
													$method->CodeBlob = $_POST["method_CodeBlob"];
													$method->Update();
													
													System::Redirect("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID);
													return true;
												}
												
												$page = new TenantObjectMethodManagementPage();
												$page->CurrentTenant = $tenant;
												$page->CurrentObject = $object;
												$page->CurrentMethod = $method;
												$page->Render();
												break;
											}
											case "instance":
											{
												$page = new TenantObjectMethodManagementPage();
												$page->CurrentTenant = Tenant::GetByURL($path[0]);
												$page->CurrentObject = TenantObject::GetByID($path[2]);
												$page->CurrentMethod = TenantObjectInstanceMethod::GetByID($path[5]);
												$page->Render();
												break;
											}
										}
										break;
									}
									case "":
									{
										$tenant = Tenant::GetByURL($path[0]);
										$object = TenantObject::GetByID($path[2]);
										
										if ($_SERVER["REQUEST_METHOD"] == "POST")
										{
											$count = $_POST["InstanceProperty_NewPropertyCount"];
											for ($i = $count; $i > 0; $i--)
											{
												$name = $_POST["InstanceProperty_" . $i . "_Name"];
												$dataType = DataType::GetByID($_POST["InstanceProperty_" . $i . "_DataTypeID"]);
												$defaultValue = $_POST["InstanceProperty_" . $i . "_DefaultValue"];
												
												$object->CreateInstanceProperty(new TenantObjectInstanceProperty($name, $dataType, $defaultValue));
											}
											
											System::Redirect("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID);
											return true;
										}
										else
										{
											$page = new TenantObjectManagementPage();
											$page->CurrentTenant = $tenant;
											$page->CurrentObject = $object;
											$page->Render();
										}
										break;
									}
								}
							}
							break;
						}
					}
				}
				return true;
			}),
			new ModulePage("launch", function($page, $path)
			{
				$tenant = Tenant::GetByURL($path[0]);
				header("Location: http://" . $tenant->DataCenters->Items[0]->HostName . "/" . $tenant->URL);
			})
		))
	));
?>