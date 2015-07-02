<?php
	$page = new PsychaticaWebPage($chance->Title . " | Chances | Marketplace");
	$page->BeginContent();
?>
<div class="Panel">
	<h3 class="PanelTitle">Chance Details</h3>
	<div class="PanelContent">
		<?php
			$machines = $chance->GetMachines();
			foreach ($machines as $machine)
			{
				$kiosk = new RandomItemKiosk($machine->Name, $machine->Type->Name);
			}
		?>
	</div>
</div>
<?php
	$page->EndContent();
?>