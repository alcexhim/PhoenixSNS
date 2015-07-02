<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	
	// use PhoenixSNS\Objects\DataCenter;
	use PhoenixSNS\Objects\Module;
	use PhoenixSNS\Objects\PaymentPlan;
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantObject;
	use PhoenixSNS\Objects\TenantProperty;
	use PhoenixSNS\Objects\TenantPropertyValue;
	use PhoenixSNS\Objects\TenantStatus;
	use PhoenixSNS\Objects\TenantType;
	
	class TenantObjectInstanceBrowsePage extends WebPage
	{
		public $CurrentTenant;
		public $CurrentObject;
		
		protected function Initialize()
		{
			$this->Title = "Browse Instances of Tenant Object";
			$this->Subtitle = $this->CurrentObject->Name . " on " . $this->CurrentTenant->URL;
			$this->SidebarButtons[3]->Expanded = true;
		}
		
		protected function RenderContent()
		{
			if ($this->CurrentObject != null)
			{
			?>
			<table style="width: 100%;">
				<tr id="Property_Header">
				<?php
					$properties = $this->CurrentObject->GetInstanceProperties();
					foreach ($properties as $property)
					{
						if ($property->ColumnVisible)
						{
							echo("<th>" . $property->Name . "</th>");
						}
					}
				?>
				</tr>
				<?php
					$instances = $this->CurrentObject->GetInstances();
					foreach ($instances as $instance)
					{
						?>
						<tr id="Property_<?php echo($property->ID); ?>">
						<?php
							foreach ($properties as $property)
							{
								if ($property->ColumnVisible)
								{
									echo("<td>");
									$value = $instance->GetPropertyValue($property);
									$property->RenderColumn($value);
									echo("</td>");
								}
							}
						?>
						</tr>
						<?php
					}
				?>
			</table>
			<div class="Buttons">
				<input type="submit" class="Button Default" value="Save Changes" />
				<a class="Button" href="<?php echo(System::ExpandRelativePath("~/tenant/modify/" . $this->CurrentTenant->URL . "/objects/" . $this->CurrentObject->ID)); ?>">Discard Changes</a>
			</div>
			<?php
			}
		}
	}
?>