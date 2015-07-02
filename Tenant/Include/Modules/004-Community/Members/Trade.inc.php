<?php	

	page_begin("Trade Resources");

	$wndItemSelect = new Window();
	$wndItemSelect->Name = "wndItemSelect";
	$wndItemSelect->Title = "Select Items to Trade";
	$wndItemSelect->Left = 500;
	$wndItemSelect->Width = 500;
	
	$wndItemSelect->BeginRender();
?>
<input type="text" id="txtSearch" onchange="txtSearch_TextChanged();" placeholder="Type here to search your inventory" style="width: 100%" />
<div class="ButtonGroup ButtonGroupHorizontal">
	<?php
		$items = mmo_get_items_by_user($CurrentUser);
		foreach ($items as $item)
		{
	?>
			<a class="ButtonGroupButton" href="#" onclick="">
				<img class="ButtonGroupButtonImage" src="/market/items/<?php echo($item->Name); ?>/images/thumbnail.png" />
				<span class="ButtonGroupButtonText"><?php echo($item->Title); ?></span>
			</a>
	<?php
		}
	?>
</div>
<div style="text-align: right;">
	<a class="Button" href="#" onclick="wndItemSelect.Close();">Add Items</a>
	<a class="Button" href="#" onclick="wndItemSelect.Close();">Cancel</a>
</div>
<?php
	$wndItemSelect->EndRender();
?>
<table style="width: 100%">
	<tr>
		<td colspan="2">
			<div class="Panel">
				<h3 class="PanelTitle">Trade with <a href="/community/members/<?php echo($thisuser->ShortName); ?>"><?php echo($thisuser->LongName); ?></a></h3>
				<div class="PanelContent">
					<p>
						When you offer a trade, you will send a trade request to the person with whom you are trading. The request will detail
						the items you are offering, as well as the items you would like to receive. It should be noted that this is just a
						guideline; the receiver is not required to send you the items that you would like to receive. When the receiver replies
						to the trade request, you will be able to see the items the receiver is offering in exchange for your items, and you can
						proceed with the trade or reject it.
					</p>
					<p>
						You can trade resources or items or both. If you wish to send items to <?php echo($thisuser->LongName); ?> without expecting
						anything in return, use the <a href="/community/members/<?php echo($thisuser->ShortName); ?>/gift">gifting system</a>. Please
						do NOT use the gifting system if you are expecting something in return!
					</p>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td style="width: 50%">
			<div class="Panel">
				<h3 class="PanelTitle">Trade Resources</h3>
				<div class="PanelContent">
					<table style="width: 100%;">
					<?php
						$resource_types = MarketResource::Enumerate();
						foreach ($resource_types as $resource_type)
						{
							?>
							<tr>
								<td style="width: 200px;"><?php echo($resource_type->ToString()); ?></td>
								<td style="width: 100px;"><input type="number" value="0" name="resource_amount_<?php echo($resource_type->ID); ?>" style="width: 100px" /></td>
								<td>out of 200 (200 remaining)</td>
							</tr>
							<?php
						}
					?>
					</table>
				</div>
			</div>
		</td>
		<td>
			<div class="Panel">
				<h3 class="PanelTitle">Trade Items</h3>
				<div class="PanelContent">
					<div class="ProfilePage">
						<div class="ProfileTitle">
							<span class="ProfileUserName">What I'm willing to trade</span>
							<span class="ProfileControlBox">
								<a href="#" onclick="wndItemSelect.ShowDialog();">Add item</a>
							</span>
						</div>
						<div class="ProfileContent">
						</div>
					</div>
					<div class="ProfilePage">
						<div class="ProfileTitle">
							<span class="ProfileUserName">What I want</span>
							<span class="ProfileControlBox">
								<a href="#" onclick="wndItemSelect.ShowDialog();">Add item</a>
							</span>
						</div>
						<div class="ProfileContent">
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr />
			<div style="text-align: center;">
				<a class="Button" href="#">Offer Trade</a>
				<a class="Button" href="/community/members/<?php echo($thisuser->ShortName); ?>">Cancel</a>
			</div>
		</td>
	</tr>
</table>
<?php
	page_end();
?>