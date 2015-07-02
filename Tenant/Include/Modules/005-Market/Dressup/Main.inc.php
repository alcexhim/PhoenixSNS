<?php
	class PsychaticaMarketDressupPage extends PsychaticaMarketWebPage
	{
		public function __construct()
		{
			parent::__construct();
			$this->Title = "Dressing Room | Market";
			$this->BreadcrumbItems = array
			(
				new WebBreadcrumbItem("~/market", "Market"),
				new WebBreadcrumbItem("~/market/dressup", "Dressing Room")
			);
		}
		
		public function RenderContent()
		{
			$CurrentUser = User::GetCurrent();
?>
			<div class="Panel">
				<h3 class="PanelTitle">Dressing Room</h3>
				<div class="PanelContent">
					<p>
						Select an item from the list to add it to your Shopping Cart and see how it would look on your avatar.
					
					<?php if ($CurrentUser != null) { ?>
						Click the &quot;Purchase&quot; button to purchase all the items in your Shopping Cart!
					<?php } else { ?>
						<strong>You must be logged in to purchase items in your Shopping Cart.</strong>
					<?php } ?>
					</p>
					<table style="width: 100%">
						<tr>
							<td style="width: 200px;">
								<div class="Panel">
									<h3 class="PanelTitle">Preview</h3>
									<div class="PanelContent" style="height: 340px;">
										<div class="AvatarAdorner" style="position: relative; left: 80px;">
										<?php
											$renderer = new AvatarRenderer("r1DressupPreview");
											$renderer->ZoomFactor = 0.2;
											$renderer->Base = AvatarBase::GetByID(1);
											$renderer->Render();
										?>
										</div>
									</div>
								</div>
							</td>
							<td>
								<div class="Panel">
									<h3 class="PanelTitle">Available Items</h3>
									<div class="PanelContent">
										<script type="text/javascript">
										<?php echo($renderer->Name); ?>.OnItemEquipped = function(sender, e)
										{
											for (var i = 0; i < e.Item.Images.length; i++)
											{
												var image = e.Item.Images[i];
												var img = new Image();
												img.src = "<?php echo(System::ExpandRelativePath("~/images/avatar/items/")); ?>" + e.Item.ID + "/<?php echo($renderer->Base->ID); ?>/" + image.ID + "." + image.FileNameExtension;
												
												alert(img.src);
											}
											e.Data.Button.className = "ButtonGroupButton Selected";
										};
										<?php echo($renderer->Name); ?>.OnItemUnequipped = function(sender, e)
										{
											e.Data.Button.className = "ButtonGroupButton";
										};
										
										function AvatarEquipItem(itemid)
										{
											var parent = <?php echo($renderer->Name); ?>;
											var btnid = "AvatarClothesBrowser_cb1_Items_" + itemid + "_Button";
											var btn = document.getElementById(btnid);
											if (btn.className == "ButtonGroupButton Selected")
											{
												parent.Unequip(itemid, { "Button": btn });
											}
											else
											{
												parent.Equip(itemid, { "Button": btn });
											}
										}
										</script>
										<div class="ButtonGroup ButtonGroupHorizontal" style="height: 340px; overflow: scroll; overflow-x: hidden;">
										<?php
											$entries = ItemMarketEntry::Get();
											foreach ($entries as $entry)
											{
												?>
												<a class="ButtonGroupButton" id="AvatarClothesBrowser_cb1_Items_<?php echo($entry->Item->ID); ?>_Button" href="#" onclick="AvatarEquipItem(<?php echo($entry->Item->ID); ?>); return false;">
													<img class="ButtonGroupButtonImage" src="<?php echo(System::ExpandRelativePath("~/market/items/" . $entry->Item->Name . "/images/thumbnail.png")); ?>" style="width: auto;" />
													<span class="ButtonGroupButtonText"><?php echo($entry->Item->Title); ?></span>
												</a>
												<?php
											}
										?>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<?php
						if ($CurrentUser != null)
						{
						?>
						<tr>
							<td colspan="2">
								<div class="Panel">
									<h3 class="PanelTitle">Shopping Cart</h3>
									<div class="PanelContent">
										<div class="ProfilePage">
											<div class="ProfileTitle">
												<span class="ProfileUserName"><span id="lblItemCount">No</span> items in cart</span>
												<span class="ProfileControlBox">
													<a href="#">Purchase</a>
												</span>
											</div>
											<div class="ProfileContent">
												Please select an item from &quot;Available Items&quot;
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<?php
						}
						?>
					</table>
				</div>
			</div>
<?php
		}
	}
	(new PsychaticaMarketDressupPage())->Render();
?>