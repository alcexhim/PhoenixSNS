<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\TabContainer;
	use WebFX\Controls\TabPage;
	
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
	
	class TenantObjectManagementPage extends WebPage
	{
		public $CurrentTenant;
		public $CurrentObject;
		
		protected function Initialize()
		{
			if ($this->CurrentObject != null)
			{
				$this->Title = "Manage Tenant Object";
				$this->Subtitle = $this->CurrentObject->Name . " on " . $this->CurrentTenant->URL;
			}
			else
			{
				$this->Title = "Manage Tenant Objects";
				$this->Subtitle = $this->CurrentTenant->URL;
			}
			$this->SidebarButtons[3]->Expanded = true;
		}
		
		protected function RenderContent()
		{
			if ($this->CurrentObject != null)
			{
			?>
			<form method="POST">
			<?php
				$tbs = new TabContainer("tbsObject");
				//								ID						title			imageurl	targeturl	script	contentfunc
				$tbs->TabPages[] = new TabPage("tabGeneralInformation", "General Information", null, null, null, function()
				{
					?>
					<div class="FormView">
						<div class="Field">
							<label for="txtObjectName">Object <u>n</u>ame:</label>
							<input type="text" name="object_Name" id="txtObjectName" accesskey="n" value="<?php echo($this->CurrentObject->Name); ?>" />
						</div>
						<div class="Field">
							<label for="cboModuleID">Parent <u>m</u>odule:</label>
							<select id="cboModuleID" name="object_ModuleID" accesskey="m">
							</select>
						</div>
						<div class="Field">
							<label for="cboParentObjectID">Parent <u>o</u>bject:</label>
							<select id="cboParentObjectID" name="object_ParentObjectID" accesskey="o">
							</select>
						</div>
					</div>
					<?php
				});
				$tbs->TabPages[] = new TabPage("tabStaticProperties", "Static Properties", null, null, null, function()
				{
					?>
					<table class="ListView" style="width: 100%;">
						<tr id="Property_Header">
							<th style="width: 32px;"><a href="#" onclick="AddPropertyBelow('Header');" title="Add Below">[+]</a></th>
							<th style="width: 256px;">Property</th>
							<th style="width: 128px;">Data Type</th>
							<th>Default Value</th>
						</tr>
						<?php
							$properties = $this->CurrentObject->GetProperties();
							foreach ($properties as $property)
							{
								?>
								<tr id="StaticProperty_<?php echo($property->ID); ?>">
									<td style="width: 32px;"><a href="#" onclick="AddStaticPropertyBelow('<?php echo($property->ID); ?>');" title="Add Below">[+]</a></td>
									<td><?php echo($property->Name); ?></td>
									<td><?php echo($property->DataType == null ? "(undefined)" : "<a href=\"" . System::ExpandRelativePath("~/datatype/modify/" . $property->DataType->ID) . "\">" . $property->DataType->Name . "</a>"); ?></td>
									<td>
									<?php
									if ($property->DataType == null || $property->DataType->ColumnRendererCodeBlob == null)
									{
										?>
										<input style="width: 100%;" type="text" id="txtStaticProperty_<?php echo($property->ID); ?>" name="StaticProperty_<?php echo($property->ID); ?>" value="<?php echo($property->DefaultValue); ?>" />
										<?php
									}
									else
									{
										call_user_func($property->DataType->ColumnRendererCodeBlob, $property->Value);
									}
									?>
									</td>
								</tr>
								<?php
							}
						?>
					</table>
					<?php
				});
				$tbs->TabPages[] = new TabPage("tabInstanceProperties", "Instance Properties", null, null, null, function()
				{
					?>
					<script type="text/javascript">
					function AddInstancePropertyBelow(id)
					{
						var hdnNewPropertyCount = document.getElementById("hdnNewPropertyCount");
						hdnNewPropertyCount.value = parseInt(hdnNewPropertyCount.value) + 1;
						
						var parentRow = document.getElementById("InstanceProperty_" + id);
						var table = parentRow.parentElement;
						
						var tr = table.insertRow(parentRow.sectionRowIndex + 1);
						
						var tdAdd = tr.insertCell(-1);
						tdAdd.innerHTML = "<a href=\"#\" onclick=\"AddInstancePropertyBelow('');\" title=\"Add Below\">[+]</a>";
						
						var tdProperty = tr.insertCell(-1);
						tdProperty.innerHTML = "<input type=\"text\" name=\"InstanceProperty_" + hdnNewPropertyCount.value + "_Name\" />";
						
						var tdDataType = tr.insertCell(-1);
						tdDataType.innerHTML = "<input type=\"text\" name=\"InstanceProperty_" + hdnNewPropertyCount.value + "_DataTypeID\" />";
						
						var tdDefaultValue = tr.insertCell(-1);
						tdDefaultValue.innerHTML = "<input type=\"text\" name=\"InstanceProperty_" + hdnNewPropertyCount.value + "_DefaultValue\" />";
					}
					</script>
					<input type="hidden" id="hdnNewPropertyCount" name="InstanceProperty_NewPropertyCount" value="0" />
					<table class="ListView" style="width: 100%;">
						<tr id="InstanceProperty_Header">
							<th style="width: 32px;"><a href="#" onclick="AddInstancePropertyBelow('Header'); return false;" title="Add Below">[+]</a></th>
							<th style="width: 256px;">Property</th>
							<th style="width: 128px;">Data Type</th>
							<th>Default Value</th>
						</tr>
						<?php
							$properties = $this->CurrentObject->GetInstanceProperties();
							foreach ($properties as $property)
							{
								?>
								<tr id="InstanceProperty_<?php echo($property->ID); ?>">
									<td style="width: 32px;"><a href="#" onclick="AddInstancePropertyBelow('<?php echo($property->ID); ?>'); return false;" title="Add Below">[+]</a></td>
									<td><?php echo($property->Name); ?></td>
									<td><?php echo($property->DataType == null ? "(undefined)" : "<a href=\"" . System::ExpandRelativePath("~/datatype/modify/" . $property->DataType->ID) . "\">" . $property->DataType->Name . "</a>"); ?></td>
									<td>
									<?php
									$property->RenderColumn();
									?>
									</td>
								</tr>
								<?php
							}
						?>
					</table>
					<?php
				});
				$tbs->TabPages[] = new TabPage("tabStaticMethods", "Static Methods", null, null, null, function()
				{
					?>
					<table class="ListView" style="width: 100%;">
						<tr id="StaticMethod_Header">
							<th style="width: 32px;"><a href="#" onclick="AddStaticMethodBelow('Header');" title="Add Below">[+]</a></th>
							<th style="width: 256px;">Method</th>
							<th>Description</th>
							<th style="width: 128px;">Return Data Type</th>
						</tr>
						<?php
							$methods = $this->CurrentObject->GetMethods();
							foreach ($methods as $method)
							{
								?>
								<tr id="StaticMethod_<?php echo($method->ID); ?>">
									<td style="width: 32px;"><a href="#" onclick="AddStaticMethodBelow('<?php echo($method->ID); ?>');" title="Add Below">[+]</a></td>
									<td><a href="<?php echo(System::ExpandRelativePath("~/tenant/modify/" . $this->CurrentTenant->URL . "/objects/" . $this->CurrentObject->ID . "/methods/static/" . $method->ID)); ?>"><?php echo($method->Name); ?></a></td>
									<td><?php echo($method->Description); ?></td>
									<td><?php /* echo($method->DataType == null ? "(undefined)" : "<a href=\"#\">" . $method->DataType->Name . "</a>"); */ ?></td>
								</tr>
								<?php
							}
						?>
					</table>
					<?php
				});
				$tbs->TabPages[] = new TabPage("tabInstances", "Instances", null, "~/tenant/modify/" . $this->CurrentTenant->URL . "/objects/" . $this->CurrentObject->ID . "/instances", null, function()
				{
					$listview = new ListView("lvInstances");
					$listview->Width = "100%";
					
					$properties = $this->CurrentObject->GetInstanceProperties();
					foreach ($properties as $property)
					{
						if ($property->ColumnVisible)
						{
							$listview->Columns[] = new ListViewColumn("P" . $property->ID, $property->Name);
						}
					}
					
					$instances = $this->CurrentObject->GetInstances();
					foreach ($instances as $instance)
					{
						$cols = array();
						foreach ($properties as $property)
						{
							if ($property->ColumnVisible)
							{
								$cols[] = new ListViewItemColumn("P" . $property->ID, "", "", function($vals)
								{
									$inst = $vals[0];
									$prop = $vals[1];
									$prop->RenderColumn($inst->GetPropertyValue($prop));
								}, array($instance, $property));
							}
						}
						$lvi = new ListViewItem($cols);
						$listview->Items[] = $lvi;
					}
					
					$listview->Render();
				});
				$tbs->SelectedTab = $tbs->TabPages[0];
				$tbs->Render();
				?>
			
					<div class="Buttons">
						<input type="submit" class="Button Default" value="Save Changes" />
						<a class="Button" href="<?php echo(System::ExpandRelativePath("~/tenant/modify/" . $this->CurrentTenant->URL)); ?>">Discard Changes</a>
					</div>
				</form>
				<?php
			}
		}
	}
?>