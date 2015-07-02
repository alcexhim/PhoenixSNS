<?php
	namespace PhoenixSNS\Modules\Market\Trade\Pages;
	
	use PsychaticaWebPage;
	use System;
	
	use WebFramework\Controls\ButtonGroup;
	use WebFramework\Controls\ButtonGroupButton;
	
	class MarketTradePage extends PsychaticaWebPage
	{
		protected function RenderContent()
		{
			?>
			<div id="MarketTradePersonalInventoryListView">
			<?php
				$btngItems = new ButtonGroup("btngItems");
				// $btngItems->EnableMultipleSelection = true;
			?>
			</div>
			<?php
		}
	}
?>