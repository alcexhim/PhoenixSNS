<?php
	namespace PhoenixSNS\Objects;
	
	class Chance
	{
		public $ID;
		public $Name;
		public $Title;
		public $Description;
		
		public static function GetByAssoc($values)
		{
			$chance = new Chance();
			$chance->ID = $values["chance_id"];
			$chance->Name = $values["chance_name"];
			$chance->Title = $values["chance_title"];
			$chance->Description = $values["chance_description"];
			return $chance;
		}
		
		public static function Get($max = null, $all = false)
		{
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "market_chances";
			if (!$all) $query .= " WHERE chance_begin_date IS NOT NULL AND (chance_begin_date <= NOW() AND (chance_end_date IS NULL OR chance_end_date >= NOW()))";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			
			global $MySQL;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Chance::GetByAssoc($values);
			}
			return $retval;
		}
		
		public static function GetByID($id)
		{
			if ($id == null || !is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "market_chances WHERE chance_id = " . $id;
			$result = $MySQL->query($query);
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return Chance::GetByAssoc($values);
			}
			return null;
		}
		public static function GetByName($name)
		{
			if ($name == null) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "market_chances WHERE chance_name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			if ($result->num_rows > 0)
			{	
				$values = $result->fetch_assoc();
				return Chance::GetByAssoc($values);
			}
			return null;
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return Chance::GetByID($idOrName);
			return Chance::GetByName($idOrName);
		}
	}
	class ChanceRenderer
	{
		public $Name;
		public $Chance;
		
		public function __construct($name, $chance)
		{
			$this->Name = $name;
			$this->Chance = $chance;
		}
		
		public function Render()
		{
			$CurrentUser = User::GetCurrent();
?>
<div class="Chance" id="Chance_<?php echo($this->Name); ?>" style="background-image: url('<?php echo(System::ExpandRelativePath("~/images/chance/" . $this->Chance->ID . "/kiosk_background.png")); ?>');">
	<div class="ChanceGlobe" id="Chance_<?php echo($this->Name); ?>_globe">
		<div class="ChanceGlobeEggs" id="Chance_<?php echo($this->Name); ?>_globe_eggs"></div>
		<div class="ChanceGlobeStrip"></div>
		<div class="ChanceGlobeOverlay"></div>
	</div>
	<div class="ChanceControls">
		<a style="background-image: url('<?php echo(System::ExpandRelativePath("~/images/chance/" . $this->Chance->ID . "/kiosk_button.png")); ?>');" id="Chance_<?php echo($this->Name); ?>_spinbutton" class="ChanceButton" href="<?php echo(System::ExpandRelativePath("~/chance/" . $this->Chance->ID . "/spin")); ?>" onclick="Chance_<?php echo($this->Name); ?>.spin(); return false;">&nbsp;</a>
		<span class="ChancePrompt">CLICK THE KNOB</span>
	</div>
	<div class="ChanceDisabled" style="display: none;"></div>
	<?php
	if ($CurrentUser == null)
	{
	?>
	<div class="ChanceNotification" id="Chance_<?php echo($this->Name); ?>_bankrupt">
		You must be logged in<br />to spin
	</div>
	<?php
	}
	else if (MarketResource::GetByUser($CurrentUser, 1)->Count < 180)
	{
	?>
	<div class="ChanceNotification" id="Chance_<?php echo($this->Name); ?>_bankrupt">
		You do not have<br />enough money (<?php echo(MarketResource::GetByID(1)->Name); ?>)
	</div>
	<?php
	}
	?>
	<script type="text/javascript">
	var Chance_<?php echo($this->Name); ?> = new Gacha("<?php echo($this->Name); ?>");
	Chance_<?php echo($this->Name); ?>.completed = function()
	{
		window.location.href = "<?php echo(System::ExpandRelativePath("~/chance/" . $this->Name . "/spin")); ?>";
	};
	</script>
</div>
<?php
		}
	}
?>