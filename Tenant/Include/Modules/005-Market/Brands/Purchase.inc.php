<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$quantity = 1;
		if (is_numeric($_POST["purchase_quantity"])) $quantity = $_POST["purchase_quantity"];
		
		if ($quantity > 1)
		{
			if ($_POST["confirm"] != "confirm")
			{
				$page = new PsychaticaWebPage("Purchase Item | Marketplace");
				$page->BeginContent();
?>
<div class="Panel">
	<h3 class="PanelTitle">Purchase Item</h3>
	<div class="PanelContent">
		<div class="ProfilePage">
			<div class="ProfileTitle">
				<span class="ProfileUserName"><?php echo($item->Title); ?></span>
				<span class="ProfileControlBox">
					<a href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name)); ?>">Return to Item Details</a>
				</span>
			</div>
			<div class="ProfileContent">
				<table style="width: 100%">
					<tr>
						<td rowspan="3">
							<img src="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name . "/images/thumbnail.png")); ?>" alt="No image available" />
						</td>
						<td>
							<p>
								<?php
								if ($entry == null)
								{
								?>
								<p>
									Sorry, this item is not purchasable at this time.
								</p>
								<?php
								}
								else
								{
								?>
								Resource cost:
								<?php
									$resources = $entry->GetRequiredResources();
									foreach ($resources as $resource)
									{
										$resource->Count *= $quantity;
										
								?>
										<div><?php echo($resource->ToHTML()); ?></div>
								<?php
									}
								}
								?>
							</p>
							<?php
								$broke = false;
								foreach($resources as $resource)
								{
									$myResources = MarketResource::GetByUser($CurrentUser, $resource->Type);
									$myResource = $myResources[0];
									
									if ($myResource->Count < $resource->Count)
									{
										$broke = true;
										break;
									}
								}
							
								if ($broke)
								{
							?>
							<p>
								You do not have enough resources to purchase this item.
							</p>
							<?php
								}
								if ($entry != null && !$broke)
								{
									if ($CurrentUser != null)
									{
									?>
									<p>
										Please confirm your purchase by clicking on the &quot;Confirm
										Payment&quot; button. The required amount of resources will be
										removed from your account and you will be able to see your
										purchase in your Inventory.
									</p>
									<form action="purchase" method="POST">
										<table style="margin-left: auto; margin-right: auto; width: 400px;">
											<tr>
												<td colspan="2" style="text-align: right;">
													<input type="hidden" name="confirm" value="confirm" />
													<input type="hidden" name="purchase_quantity" value="<?php echo($quantity); ?>" />
													
													<input type="submit" value="Confirm Payment" />
													<a class="Button" href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name)); ?>">Cancel</a>
												</td>
											</tr>
										</table>
									</form>
									<?php
									}
									else
									{
									?>
									<p>
										Please log in to purchase this item.
									</p>
									<?php
									}
								}
							?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<?php
				$page->EndContent();
				return;
			}
		}
		
		if (!$CurrentUser->PurchaseItem($entry, $quantity))
		{
			$errorPage = new PsychaticaErrorPage();
			$errorPage->Message = "Unable to purchase the item.  Please make sure you have enough resources to complete the transaction.";
			$errorPage->ReturnButtonURL = "~/market/items/" . $item->Name;
			$errorPage->ReturnButtonText = "Return to Item";
			$errorPage->Render();
			return;
		}
		else
		{
			$errorPage = new PsychaticaMessagePage("Transaction Successful");
			$errorPage->Message = "You purchased " . $quantity . " &quot;" . $item->Title . "&quot; item" . ($quantity == 1 ? "" : "s") . ".";
			$errorPage->ReturnButtonURL = "~/market/items/" . $item->Name;
			$errorPage->ReturnButtonText = "Return to Item";
			$errorPage->Render();
		}
	}
	else
	{
		$page = new PsychaticaWebPage("Purchase Item | Marketplace");
		$page->BeginContent();
?>
<div class="Panel">
	<h3 class="PanelTitle">Purchase Item</h3>
	<div class="PanelContent">
		<div class="ProfilePage">
			<div class="ProfileTitle">
				<span class="ProfileUserName"><?php echo($item->Title); ?></span>
				<span class="ProfileControlBox">
					<a href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name)); ?>">Return to Item Details</a>
				</span>
			</div>
			<div class="ProfileContent">
				<table style="width: 100%">
					<tr>
						<td rowspan="3">
							<img src="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name . "/images/thumbnail.png")); ?>" alt="No image available" />
						</td>
						<td>
							<p>
								<?php
								if ($entry == null)
								{
								?>
								<p>
									Sorry, this item is not purchasable at this time.
								</p>
								<?php
								}
								else
								{
								?>
								Resource cost:
								<?php
									$resources = $entry->GetRequiredResources();
									foreach ($resources as $resource)
									{
								?>
										<div><?php echo($resource->ToHTML()); ?></div>
								<?php
									}
								}
								?>
							</p>
							<p>
							<?php
							if ($CurrentUser != null)
							{
								$invcount = $CurrentUser->CountInventoryItems($item);
								if ($invcount == 0)
								{
							?>
								You do not have this item in your Inventory.
							<?php
								}
								else
								{
							?>
								You have <?php echo($invcount); ?> of these in your Inventory.
							<?php
								}
							}
							?>
							</p>
							<?php
								if ($entry != null)
								{
									if ($CurrentUser != null)
									{
									?>
									<p>
										Please confirm your purchase by clicking on the &quot;Confirm
										Payment&quot; button. The required amount of resources will be
										removed from your account and you will be able to see your
										purchase in your Inventory.
									</p>
									<form action="purchase" method="POST">
										<table style="margin-left: auto; margin-right: auto; width: 400px;">
											<tr>
												<td>How many:</td>
												<td><input type="number" name="purchase_quantity" value="1" style="display: block; width: 100%;" /></td>
											</tr>
											<tr>
												<td colspan="2" style="display: none;">
													<input type="checkbox" id="chkAutoEquip" name="purchase_autoequip" value="1" />
													<label for="chkAutoEquip"><u>A</u>utomatically equip after purchasing</label>
												</td>
											</tr>
											<tr>
												<td colspan="2" style="text-align: right;">
													<input type="submit" value="Confirm Payment" />
													<a class="Button" href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name)); ?>">Cancel</a>
												</td>
											</tr>
										</table>
									</form>
									<?php
									}
									else
									{
									?>
									<p>
										Please log in to purchase this item.
									</p>
									<?php
									}
								}
							?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<?php
		$page->EndContent();
	}
?>