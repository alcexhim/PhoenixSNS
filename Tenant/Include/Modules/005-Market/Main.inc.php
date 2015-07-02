<?php
	namespace PhoenixSNS\Modules\Market;
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use PhoenixSNS\Objects\User;
	use PhoenixSNS\Objects\MarketStarterPack;
	
	use PhoenixSNS\MasterPages\WebPage;
	use PhoenixSNS\MasterPages\ErrorPage;
	
	class MarketWebPage extends WebPage
	{
	}
	
	System::$Modules[] = new Module("net.phoenixsns.Market", array
	(
		new ModulePage("market", array
		(
			new ModulePage("items", function($path)
			{
				require("Items/Main.inc.php");
				return true;
			}),
			new ModulePage("trade", function($path)
			{
				require("Trade/Main.inc.php");
				return true;
			}),
			new ModulePage("starter", function($path)
			{
				$CurrentUser = User::GetCurrent();
				if ($path[0] == "packs")
				{
					$pack_id = $path[1];
					$pack = MarketStarterPack::GetByID($pack_id);
					if ($path[2] == "images")
					{
						if ($path[3] == "thumbnail.png")
						{
							$filenames = array("images/avatar/base.png");
							$items = $pack->GetItems();
							foreach ($items as $item)
							{
								foreach ($item->Images as $image)
								{
									$filenames[] = "images/avatar/items/" . $image->ID . ".png";
								}
							}
							
							$image = imagelayerfiles($filenames);
							imagesavealpha($image, true);
							imagealphablending($image, true);
							
							header('Content-Type: image/png');
							imagepng($image);
							return;
						}
					}
				}

				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					if (!is_numeric($_POST["starterpack_id"]))
					{
						$page = new ErrorPage();
						$page->Message = "The Starter Pack could not be applied. <code>starterpack_id</code> must be a number.";
						$page->ReturnButtonURL = "~/market/starter";
						$page->ReturnButtonText = "Return to Starter Packs";
						$page->Render();
						return;
					}
					
					$pack = MarketStarterPack::GetByID($_POST["starterpack_id"]);
					
					if (!$pack->ApplyToUser($CurrentUser))
					{
						$errno = mysql_errno();
						$error = mysql_error();
						
						$page = new ErrorPage();
						$page->ErrorCode = $errno;
						$page->ErrorDescription = $error;
						$page->Render();
						return true;
					}
					else
					{
						$page = new WebPage();
						$page->Title = "Success";
						$page->BeginContent();
						?>
						<p>Enjoy your <?php echo($pack->Title); ?>! Check out your new look on the avatar to the right.</p>
						<p style="text-align: center;"><a href="<?php echo(System::ExpandRelativePath("~/market")); ?>">Buy some more clothes</a> or <a href="<?php echo(System::ExpandRelativePath("~/")); ?>">return to home page</a>!</p>
						<?php
						$page->EndContent();
						return true;
					}
				}
				if ($CurrentUser == null)
				{
					$_SESSION["LoginRedirectURL"] = "~/market/starter";
					System::Redirect("~/account/login.page");
					return true;
				}
				else if ($CurrentUser->CountInventoryItems() > 0)
				{
					$errorPage = new ErrorPage();
					$errorPage->Message = "Only new Members are allowed to use the Starter Pack. Sorry for the inconvenience!";
					$errorPage->ReturnButtonURL = "~/market";
					$errorPage->ReturnButtonText = "Return to Market";
					$errorPage->Render();
					return true;
				}
				else
				{
					$packs = MarketStarterPack::Get();
					
					$_SESSION["MarketStarterVisited"] = "1";
					$page = new WebPage();
					$page->Title = "Starter Pack";
					$page->BeginContent();
					?>
					<div class="Panel">
						<h3 class="PanelTitle">Choose Starter Pack</h3>
						<div class="PanelContent">
							<p>Please select from one of the outfits below to get started!</p>
							<p><strong>Note that once chosen and applied, a Member cannot change to a different Starter Pack. Make your choice carefully!</strong></p>
							<form action="starter" method="POST">
								<div style="text-align: center;">
								<?php
									foreach ($packs as $pack)
									{
										?>
										<div style="display: inline-block; width: 200px" class="ButtonGroupButton">
											<div>
												<label for="optStarterPack<?php echo($pack->ID); ?>">
													<div style="text-align: center;"><?php echo($pack->Title); ?></div>
													<img style="margin-left: auto; margin-right: auto; display: block; width: 122px;" src="/market/starter/packs/<?php echo($pack->ID); ?>/images/thumbnail.png" />
												</label>
											</div>
											<div style="text-align: center;">
												<input type="radio" id="optStarterPack<?php echo($pack->ID); ?>" name="starterpack_id" value="<?php echo($pack->ID); ?>" />
											</div>
										</div>
										<?php
									}
								?>
								</div>
								<div style="text-align: center;"><input type="submit" value="Apply My Choice" /></div>
							</form>
						</div>
					</div>
					<?php
					$page->EndContent();
					return true;
				}
			})
		),
		function($path)
		{
			// function called before the module is executed
			if (!System::GetConfigurationValue("Market.Enabled", false))
			{
				System::Redirect("~/");
				return false;
			}
			return true;
		},
		function($path)
		{
			// function called when the file could not be found
			System::Redirect("~/market/items");
		})
	));
?>