<?php
	namespace PhoenixSNS\WebControls;
	
	use Phast\WebControl;
	
	class TicketDispenser extends WebControl
	{
		public $Name;
		public $Amount;
		
		public function __construct($name, $amount = 1)
		{
			$this->Name = $name;
			$this->Amount = $amount;
		}
		
		protected function RenderContent()
		{
			$CurrentUser = User::GetCurrent();
			$CurrentUser->SetRelativeResourceCount(MarketResource::GetByID(2), $this->Amount);
			
?>
<div class="TicketDispenser" id="TicketDispenser_tgs" style="width: 400px; margin-left: auto; margin-right: auto;">
	<div class="Prompt">
		Congratulations! You earned
	</div>
	<div class="Ticket">
		<span id="TicketDispenser_<?php echo($this->Name); ?>_value">0</span>
	</div>
	<div class="Prompt">
		Arcade Tickets!
	</div>
</div>
<script type="text/javascript">
var <?php echo($this->Name); ?> = new TicketDispenser("<?php echo($this->Name); ?>", <?php echo($this->Amount); ?>);
<?php echo($this->Name); ?>.Dispense();
</script>
<?php
		}
	}
?>