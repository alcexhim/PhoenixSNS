<?php
	use WebFX\System;
	use WebFX\Controls\BreadcrumbItem;
	
	use PhoenixSNS\Modules\Market\PsychaticaMarketWebPage;
	
	class PsychaticaMarketItemListPage extends PsychaticaMarketWebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "Items | Market";
			$this->BreadcrumbItems = array
			(
				new BreadcrumbItem("~/market", "Market"),
				new BreadcrumbItem("~/market/items", "Items")
			);
		}
		protected function RenderContent()
		{
?>
			<h3 class="PanelTitle">Items</h3>
			<div class="PanelContent">
				<?php
				$view = "button";	
				if ($view == "button") {
				?>
				
				<div class="ButtonGroup ButtonGroupHorizontal">
				<?php
					$entries = ItemMarketEntry::Get();
					foreach ($entries as $entry)
					{
						$item = $entry->Item;
					?>
						<a class="ButtonGroupButton" href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name)); ?>">
							<img class="ButtonGroupButtonImage" src="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name . "/images/thumbnail.png")); ?>" alt="No image available" style="width: auto;" />
							<span class="ButtonGroupButtonText"><?php echo($item->Title); ?></span>
						</a>
					<?php
					}
				?>
				</div>
				
				<?php
			}
			else if ($view == "tile")
			{
?>
				<div class="TileView">
				<?php
					$entries = ItemMarketEntry::Get();
					foreach ($entries as $entry)
					{
						$item = $entry->Item;
					?>
					<div class="Tile">	
						<div class="TileImage">
							<img src="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name . "/images/thumbnail.png")); ?>" alt="No image available" style="height: 112px;" />
						</div>
						<div class="TileTitle" style="font-size: 1.2em;"><?php echo($item->Title); ?></div>
						<div class="TileContent">
							<div class="ActionList">
								<a href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name . "/purchase")); ?>"> Purchase </a>
							</div>
						</div>
						<div class="TileActionList">
							<span class="ActionList">
								<a href="<?php echo(System::ExpandRelativePath("~/market/items/" . $item->Name)); ?>"> v </a>
							</span>
						</div>
					</div>
					<?php
					}
				?>
				</div>
				
<?php
			}
?>
			</div>
<?php
		}
	}
	
	$page = new PsychaticaMarketItemListPage();
	$page->Render();
?>