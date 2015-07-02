<?php
	class PsychaticaMarketItemDetailPage extends PsychaticaMarketWebPage
	{
		public $Item;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->BreadcrumbItems = array
			(
				new WebBreadcrumbItem("~/market", "Market"),
				new WebBreadcrumbItem("~/market/items", "Items"),
				new WebBreadcrumbItem("~/market/items/" . $item->Name, $item->Title)
			);
		}
		protected function Initialize()
		{
			parent::Initialize();
			$this->Title = $this->Item->Title . " | Items | Marketplace";
		}
		protected function RenderContent()
		{
			$CurrentUser = User::GetCurrent();
			$entry = $this->Item->GetMarketEntry();
			
?>
			<div class="ProfilePage">
				<div class="ProfileTitle">
					<span class="ProfileUserName"><?php echo($this->Item->Title); ?></span>
					<span class="ProfileControlBox"><?php
						if ($CurrentUser != null)
						{
							if ($entry != null)
							{
							?>
								<a href="<?php echo(System::ExpandRelativePath("~/market/items/" . $this->Item->Name . "/purchase")); ?>">Purchase</a>
							<?php
							}
							?>
							<a href="<?php echo(System::ExpandRelativePath("~/market/items/" . $this->Item->Name . "/wish.phnx")); ?>">Add to Wish List</a>
							<form action="<?php echo(System::ExpandRelativePath("~/account/trade.phnx")); ?>" method="POST">
								<input type="hidden" name="item_id" value="<?php echo($this->Item->ID); ?>" />
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
								<img src="<?php echo(System::ExpandRelativePath("~/market/items/" . $this->Item->Name . "/images/thumbnail.png")); ?>" alt="No image available" />
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
									$invcount = $CurrentUser->CountInventoryItems($this->Item);
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
								else
								{
								?>
									Please log in to purchase this item.
								<?php
								}
								?>
								</p>
								<p>
									This item has been purchased <?php
									
									$count = $this->Item->CountPurchased();
									echo($count);
									if ($count == 1)
									{
										echo (" time");
									}
									else
									{
										echo(" times");
									}
									?>.
								</p>
							</td>
						</tr>
						<tr>
							<td><hr /></td>
						</tr>
						<tr>
							<td><?php echo($this->Item->Description); ?></td>
						</tr>
					</table>
				</div>
			</div>
<?php
		}
	}
	
	if ($item != null)
	{
		$page = new PsychaticaMarketItemDetailPage();
		$page->Item = $item;
	}
	else
	{
		$page = new PsychaticaErrorPage();
		$page->Message = "The item does not exist";
	}
	$page->Render();
?>