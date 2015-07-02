<?php
	namespace PhoenixSNS\Modules\Market\Trade;

	require("Pages/MarketTradePage.inc.php");
	
	use PsychaticaErrorPage;
	use PsychaticaMessagePage;
	use System;
	
	use PhoenixSNS\Modules\Market\Trade\Pages\MarketTradePage;
	
	$page = new MarketTradePage();
	$page->Render();
?>