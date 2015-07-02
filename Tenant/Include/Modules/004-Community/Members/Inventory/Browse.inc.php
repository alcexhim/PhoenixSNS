<?php
	use WebFX\System;
?>
<table style="width: 100%">
	<tr>
		<td><input type="text" style="width: 100%" /></td>
		<td id="tdSearchBar" style="width: 75px;"><noscript><input type="submit" value="Search" /></noscript></td>
	</tr>
	<tr>
		<td colspan="2" style="vertical-align: top;">
			<div class="Panel">
				<h3 class="PanelTitle"><?php echo($thisuser->LongName); ?>'s Inventory</h3>
				<div class="PanelContent">
					<table style="width: 100%">
						<tr>
							<td style="width: 96px; vertical-align: top;">
								<div class="ActionList">
									<?php if ($path[3] == null || $path[3] == "") { ?>
									<span class="Selected">All Items</span>
									<?php } else { ?>
									<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $id . "/inventory")); ?>">All Items</a>
									<?php } ?>
									
									<?php if ($path[3] != null && $path[3] == "equipped") { ?>
									<span class="Selected">Equipped</span>
									<?php } else { ?>
									<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $id . "/inventory/equipped")); ?>">Equipped</a>
									<?php } ?>
									
									<?php if ($path[3] != null && $path[3] == "selling") { ?>
									<span class="Selected">For Sale</span>
									<?php } else { ?>
									<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $id . "/inventory/selling")); ?>">For Sale</a>
									<?php } ?>
									
									<?php if ($path[3] != null && $path[3] == "trading") { ?>
									<span class="Selected">For Trade</span>
									<?php } else { ?>
									<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $id . "/inventory/trading")); ?>">For Trade</a>
									<?php } ?>
									
									<?php if ($path[3] != null && $path[3] == "wishlist") { ?>
									<span class="Selected">Wish List</span>
									<?php } else { ?>
									<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $id . "/inventory/wishlist")); ?>">Wish List</a>
									<?php } ?>
								</div>
							</td>
							<td style="vertical-align: top;">
								<?php
									$items = array();
									if (count($path) < 3 || $path[3] == "")
									{
										$items = Item::GetByUser($thisuser);
									}
									else
									{
										switch ($path[3])
										{
											case "equipped":
											{
												$items = Item::GetEquippedByUser($thisuser);
												break;
											}
										}
									}
								
									if (count($items) > 0)
									{
									?>
									<div class="ButtonGroup ButtonGroupHorizontal" style="min-height: 304px;">
									<?php
										foreach ($items as $item)
										{
									?>
										<a class="ButtonGroupButton" href="/community/members/<?php echo($thisuser->ShortName); ?>/inventory/items/<?php echo($item->Name); ?>" onclick="DisplayItemInformation(<?php echo($item->ID); ?>);" style="text-align: center;">
											<img class="ButtonGroupButtonImage" style="height: 112px;" src="/market/items/<?php echo($item->Name); ?>/images/thumbnail.png" />
											<span class="ButtonGroupButtonText"><?php echo($item->Title); ?></span>
										</a>
									<?php
										}
									?>
									</div>
									<?php
									}
									else
									{
										if ($thisuser->IsAuthenticated)
										{
								?>
								<span class="ListViewMessage" style="padding-top: 130px;">There are no items in your Inventory. <a href="/market/create/item">Create a new item</a> or <a href="/market/items">browse existing items</a> to purchase.</span>
								<?php
									
										}
										else
										{
								?>
								<span class="ListViewMessage" style="padding-top: 130px;">There are no items in <?php echo($thisuser->LongName) ?>'s Inventory.</span>
								<?php
										}
									}
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</td>
	</tr>
</table>
<script type="text/javascript">
	document.getElementById("tdSearchBar").style.width = "0px";
</script>