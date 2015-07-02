<?php
	class PsychaticaMarketBrandListPage extends PsychaticaMarketWebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "Brands | Marketplace";
			$this->BreadcrumbItems = array
			(
				new WebBreadcrumbItem("~/market", "Market"),
				new WebBreadcrumbItem("~/market/brands", "Brands")
			);
		}
		protected function RenderContent()
		{
?>
			<div class="Panel">
				<h3 class="PanelTitle">Brands</h3>
				<div class="PanelContent">
					Coming soon!
				</div>
			</div>
<?php
		}
	}
	
	$page = new PsychaticaMarketBrandListPage();
	$page->Render();
?>