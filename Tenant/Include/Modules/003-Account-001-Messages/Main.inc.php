<?php
	use WebFX\Module;
	use WebFX\ModulePage;
	use WebFX\System;

	System::$Modules[] = new Module("net.phoenixsns.AccountManagement.Messages", array
	(
		new ModulePage("account", array
		(
			new ModulePage("messages", function($path)
			{
				if (System::$Configuration["Messages.Enabled"])
				{
					switch ($path[0])
					{
						case "":
						{
							System::Redirect("~/account/messages/inbox");
							return true;
						}
						case "inbox":
						{
							require("Inbox.inc.php");
							return true;
						}
						case "outbox":
						{
							require("Outbox.inc.php");
							return true;
						}
						case "create":
						{
							require("Create.inc.php");
							return true;
						}
						default:
						{
							header("HTTP/1.1 404 Not Found");
							$page = new PsychaticaErrorPage("Not Found");
							$page->Message = "The specified page was not found.";
							$page->ReturnButtonURL = "~/account/messages";
							$page->ReturnButtonText = "Return to Message Center";
							$page->Render();
							return true;
						}
					}
				}
				else
				{
					System::Redirect("~/account");
					return true;
				}
			})
		))
	));
?>