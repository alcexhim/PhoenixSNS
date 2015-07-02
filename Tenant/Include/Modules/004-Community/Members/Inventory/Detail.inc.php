<?php
	$item = Item::GetByIDOrName($path[4]);
?>
<div class="Panel">
	<h3 class="PanelTitle">Item Details</h3>
	<div class="PanelContent">
		<div class="ProfilePage">
			<div class="ProfileTitle">
				<span class="ProfileUserName"><?php echo($item->Title); ?></span>
				<span class="ProfileControlBox"><?php
					if ($CurrentUser != null)
					{
						if ($item->IsEquipped($CurrentUser))
						{
						?>
							<a href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name . "/unequip")); ?>">Unequip</a>
						<?php
						}
						else
						{
						?>
							<a href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name . "/equip")); ?>">Equip</a>
						<?php
						}
						
						$entry = $item->GetMarketEntry();
						if ($entry != null)
						{
						?>
							<a href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name . "/purchase")); ?>">Purchase</a>
						<?php
						}
						else
						{
						?>
							<span title="Item is not available for purchase" class="Disabled">Purchase</span>
						<?php
						}
						?>
						<a href="<?php echo(System::ExpandRelativePath("~/Market/WishList/AddItem/" . $item->Name)); ?>">Add to Wish List</a>
						<form action="<?php echo(System::ExpandRelativePath("~/Market/Trade/Create")); ?>" method="POST">
							<input type="hidden" name="item_id" value="<?php echo($item->ID); ?>" />
							<input class="LinkButton" type="submit" value="Request a Trade" />
						</form>
					<?php
					}
					?>
				</span>
			</div>
			<div class="ProfileContent">
				<table style="width: 100%">
					<tr>
						<td rowspan="3">
							<img src="<?php echo(System::ExpandRelativePath("~/Market/Items/" . $item->Name . "/Images/Thumbnail.png")); ?>" alt="No image available" />
						</td>
						<td>
							<p>
							<?php
							$invcount = $thisuser->CountInventoryItems($item);
							if ($invcount == 0)
							{
								if ($thisuser->IsAuthenticated)
								{
							?>
								You do not have this item in your Inventory.
							<?php
								}
								else
								{
									echo($thisuser->LongName . " does not have this item in their Inventory.");
								}
							}
							else
							{
								if ($thisuser->IsAuthenticated)
								{
							?>
								You have <?php echo($invcount); ?> of these in your Inventory.
							<?php
								}
								else
								{
									echo($thisuser->LongName . " has " . $invcount . " of these in their Inventory.");
								}
							}
							?>
							</p>
							<?php
								if ($CurrentUser != null)
								{
									if ($CurrentUser->ID != $thisuser->ID)
									{
										$invcount = $CurrentUser->CountInventoryItems($item);
										if ($invcount == 0)
										{
								?>
											<p>You do not have this item in your Inventory.</p>
								<?php
										}
										else
										{
								?>
											<p>You have <?php echo($invcount); ?> of these in your Inventory.</p>
								<?php
										}
									}
								}
							?>
						</td>
					</tr>
					<tr>
						<td><hr /></td>
					</tr>
					<tr>
						<td><?php echo($item->Description); ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>