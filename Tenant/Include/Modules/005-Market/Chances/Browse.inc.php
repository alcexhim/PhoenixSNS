<?php
	class PsychaticaMarketChanceListPage extends PsychaticaMarketWebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "Chances | Marketplace";
			$this->BreadcrumbItems = array
			(
				new WebBreadcrumbItem("~/market", "Market"),
				new WebBreadcrumbItem("~/market/items", "Items")
			);
		}
		protected function RenderContent()
		{
?>
			<div class="Panel">
				<h3 class="PanelTitle">Chances</h3>
				<div class="PanelContent">
				<?php
					$chances = Chance::Get();
					if (count($chances) == 0)
					{
				?>
					There are no Chances available for you to play at the moment. Please check back soon!
				<?php
					}
					else
					{
						foreach ($chances as $chance)
						{
						}
					}
				?>
				</div>
			</div>
<?php
		}
	}
	
	$page = new PsychaticaMarketChanceListPage();
	$page->Render();
?>