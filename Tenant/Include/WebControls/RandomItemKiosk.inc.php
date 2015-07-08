<?php
	namespace PhoenixSNS\WebControls;
	
	class RandomItemKiosk
	{
		public $Name;
		public $Type;
		
		public $ReturnURL;
		
		public function __construct($name, $type)
		{
			$this->Name = $name;
			$this->Type = $type;
		}
		
		public function Render()
		{
?>
<div id="RandomItemKiosk_<?php echo($this->Name); ?>" class="RandomItemKiosk">
	<div id="RandomItemKiosk_<?php echo($this->Name); ?>_globe" class="RandomItemKioskGlobe">
		<div id="RandomItemKiosk_<?php echo($this->Name); ?>_globe_eggs" class="RandomItemKioskGlobeEggs"></div>
		<div class="RandomItemKioskGlobeStrip"></div>
		<div class="RandomItemKioskGlobeOverlay"></div>
	</div>
	<div class="RandomItemKioskControls">
		<a onclick="<?php echo($this->Name); ?>.spin(); return false;" href="?RandomItemKiosk_spin=<?php echo($this->Name); ?>" class="RandomItemKioskButton" id="RandomItemKiosk_<?php echo($this->Name); ?>_spinbutton">&nbsp;</a>
		<span class="RandomItemKioskPrompt">CLICK THE KNOB</span>
	</div>
	<?php
	if ($bankrupt || $expired)
	{
	?>
	<div class="RandomItemKioskDisabled"></div>
	<?php
	}
	
	if ($expired)
	{
	?>
	<div id="RandomItemKiosk_<?php echo($this->Name); ?>_expired" class="RandomItemKioskNotification">
		This Chance has expired
	</div>
	<?php
	}
	else if ($bankrupt)
	{
	?>
	<div id="RandomItemKiosk_<?php echo($this->Name); ?>_bankrupt" class="RandomItemKioskNotification">
		You do not have<br>enough coins
	</div>
	<?php
	}
	?>
</div>
<?php
		}
	}
?>