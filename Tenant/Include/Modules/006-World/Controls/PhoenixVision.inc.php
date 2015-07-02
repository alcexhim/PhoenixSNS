<?php
	namespace PhoenixSNS\Controls;
	
	use WebFX\System;
	use WebFX\WebControl;
	use WebFX\WebScript;
	
	use WebFX\Controls\ProgressBar;
	use WebFX\Controls\Window;
	
	use PhoenixSNS\Modules\World\Objects\Place;
	use PhoenixSNS\Modules\World\Objects\PlaceHotspotTargetType;
	
	class PhoenixVision extends WebControl
	{
		public $CurrentPlace;
		
		public function __construct($id)
		{
			parent::__construct($id);
		}
		
		protected function RenderContent()
		{
			?><div class="PhoenixVision" data-typename="world" data-id="<?php echo($this->ID); ?>"<?php
				if ($this->CurrentPlace != null)
				{
					echo("data-place-id=\"" . $this->CurrentPlace->ID . "\"");
				}
			?>></div><?php
		}
		
		protected function RenderContent2()
		{
			?>
			<div class="PhoenixVision" id="PhoenixVision_<?php echo($this->ID); ?>">
				<div class="World" id="PhoenixVision_<?php echo($this->ID); ?>_Content" style="display: none; <?php
					if ($this->CurrentPlace != null)
					{
						echo("background-image: url('" . System::ExpandRelativePath("~/images/world/places/" . $this->CurrentPlace->ID . "/backgrnd.png") . "');");
					}
					else
					{
						echo("background-color: #000000;");
					}
				?>">
					<div class="WorldContent">
						<?php
						if ($this->CurrentPlace != null)
						{
							echo("<!-- ** CLIPPING REGIONS ** \n");
							$pcrs = $this->CurrentPlace->GetClippingRegions();
							foreach ($pcrs as $pcr)
							{
								echo("{\n");
								$points = $pcr->GetPoints();
								foreach ($points as $point)
								{
									echo("\t(" . $point->Left . ", " . $point->Top . "), \n");
								}
								echo("}, \n");
							}
							echo("-->");
							
							$hotspots = $this->CurrentPlace->GetHotspots();
							if (count($hotspots) > 0)
							{
						?>
						<div class="WorldHotspots">
							<?php
							foreach ($hotspots as $hotspot)
							{
								echo("<a class=\"WorldHotspotEntry\" ");
								if ($hotspot->TargetType == PlaceHotspotTargetType::URL || $hotspot->TargetType == PlaceHotspotTargetType::Script)
								{
									if ($hotspot->TargetURL == null)
									{
										echo("href=\"#\"");
									}
									else
									{
										echo("href=\"" . $hotspot->TargetURL . "\" target=\"_blank\"");
									}
									if ($hotspot->TargetScript != null)
									{
										echo(" onclick=\"" . $hotspot->TargetScript . "\"");
									}
								}
								else if ($hotspot->TargetType == PlaceHotspotTargetType::Place)
								{
									echo("href=\"" . System::ExpandRelativePath("~/world/places/" . $hotspot->TargetPlace->Name) . "\" onclick=\"" . $this->ID . ".SetCurrentPlace(Place.GetByID(" . $hotspot->TargetPlace->ID . "); return false;\"");
								}
								echo(" style=\"left: " . $hotspot->Left . "px; top: " . $hotspot->Top . "px; width: " . $hotspot->Width . "px; height: " . $hotspot->Height . "px;\"");
								echo(">");
								if ($hotspot->Title != null)
								{
									echo("<span class=\"Title\">" . $hotspot->Title . "</span>");
								}
								echo("</a>");
							}
							?>
						</div>
						<?php
							}
						?>
						<!-- chat log -->
						<?php
							$wndChatLog = new Window("wndChatLog_" . $this->ID);
							$wndChatLog->Title = "Messages";
							$wndChatLog->Height = "177px";
							$wndChatLog->Style = "z-index: 4;";
							$wndChatLog->BeginContent();
						?>
						<table class="ChatMessages" id="chatLog">
						</table>
						<?php
							$wndChatLog->EndContent();
						?>
						
						<div class="WorldPersonaContent" id="personas" style="position: relative;">
						</div>
						<?php
						}
						else
						{
						?>
						<div style="text-align: center; font-size: 18pt; font-weight: bold; color: #FF0000; padding-top: 240px;">place cannot be null</div>
						<?php
						}
						?>
					</div>
				</div>
				<div class="WorldProgress" id="PhoenixVision_<?php echo($this->ID); ?>_Progress" style="display: none; background-color: #000000; position: relative;">
					<div class="ProgressBarWrapper">
					<?php
						$pb1 = new ProgressBar("PhoenixVision_" . $this->ID . "_ProgressBar");
						$pb1->Text = "Please wait while the World loads...";
						$pb1->Render();
					?>
					</div>
					
					<script type="text/javascript">
					var <?php echo($this->ID); ?> = new PhoenixVision("<?php echo($this->ID); ?>");
					<?php echo($this->ID); ?>.BootstrapObjects =
					[
						<?php
						$i = 0;
						$places = Place::Get();
						foreach ($places as $place)
						{
						?>
						{
							"Place": <?php echo($place->ToJSON()); ?>,
							"ImageURL": "<?php echo(System::$Configuration["Application.BasePath"]); ?>/world/places/<?php echo($place->Name); ?>/images/preview.png",
							"Title": "Map objects"
						},<?php /* if ($i < count($places) - 1) { echo (", "); } $i++; */ ?>

						<?php
						}
						?>
					];
					<?php echo($this->ID); ?>.ProgressBar = PhoenixVision_<?php echo($this->ID); ?>_ProgressBar;
					<?php echo($this->ID); ?>.Refresh();
					</script>
				</div>
			</div>
<?php
		}
	}
?>