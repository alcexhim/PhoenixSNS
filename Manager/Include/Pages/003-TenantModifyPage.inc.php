<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	use WebFX\Controls\AdditionalDetailWidget;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\FormView;
	use WebFX\Controls\FormViewItemBoolean;
	use WebFX\Controls\FormViewItemChoice;
	use WebFX\Controls\FormViewItemChoiceValue;
	use WebFX\Controls\FormViewItemDateTime;
	use WebFX\Controls\FormViewItemText;
	use WebFX\Controls\FormViewItemMemo;
	
	use WebFX\Controls\TabContainer;
	use WebFX\Controls\TabPage;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	
	use PhoenixSNS\Objects\Module;
	use PhoenixSNS\Objects\DataCenter;
	use PhoenixSNS\Objects\PaymentPlan;
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantObject;
	use PhoenixSNS\Objects\TenantProperty;
	use PhoenixSNS\Objects\TenantPropertyValue;
	use PhoenixSNS\Objects\TenantStatus;
	use PhoenixSNS\Objects\TenantType;
	
	\Enum::Create("PhoenixSNS\\TenantManager\\Pages\\TenantManagementPageMode", "Create", "Modify");
	
	class TenantManagementPage extends WebPage
	{
		public $Tenant;
		public $Mode;
		
		public function __construct()
		{
			parent::__construct();
			$this->Mode = TenantManagementPageMode::Create;
			
			$this->SidebarButtons[3]->Expanded = true;
		}
		
		protected function Initialize()
		{
			$this->Title = "Manage Tenant";
			$this->Subtitle = $this->Tenant->URL;
		}
		
		protected function RenderContent()
		{
			?>
			<form method="POST" onsubmit="return form_OnSubmit();">
				<?php
				$tbs = new TabContainer("tbsTabs");
				$tbs->TabPages[] = new TabPage("tabGeneralInformation", "General Information", null, null, null, function()
				{
					$fv = new FormView();
					$fv->Items[] = new FormViewItemText("txtTenantURL", "tenant_URL", "Name", $this->Tenant->URL);
					
					$fvTenantTypes = array();
					$tenanttypes = TenantType::Get();
					foreach ($tenanttypes as $tenanttype)
					{
						$fvTenantType = new FormViewItemChoiceValue($tenanttype->Title, $tenanttype->ID);
						$fvTenantType->Selected = ($this->Tenant != null && $this->Tenant->Type != null && $tenanttype->ID == $this->Tenant->Type->ID);
						$fvTenantTypes[] = $fvTenantType;
					}
					$fv->Items[] = new FormViewItemChoice("cboTenantType", "tenant_TypeID", "Type", null, $fvTenantTypes);
					
					$fv->Items[] = new FormViewItemBoolean("chkStatus", "tenant_Status", "Status", ($this->Tenant->Status == TenantStatus::Enabled));
					
					$fvDataCenters = array();
					$datacenters = DataCenter::Get();
					foreach ($datacenters as $datacenter)
					{
						$fvDataCenter = new FormViewItemChoiceValue($datacenter->Title, $datacenter->ID);
						$fvDataCenter->Selected = ($this->Tenant != null && $this->Tenant->DataCenters->Contains($datacenter));
						$fvDataCenters[] = $fvDataCenter;
					}
					$fv->Items[] = new FormViewItemChoice("cboDataCenter", "tenant_DataCenterIDs", "Data centers", null, $fvDataCenters, true);
					
					
					$fvPaymentPlans = array();
					$paymentplans = PaymentPlan::Get();
					foreach ($paymentplans as $paymentplan)
					{
						$fvPaymentPlan = new FormViewItemChoiceValue($paymentplan->Title, $paymentplan->ID);
						$fvPaymentPlan->Selected = ($this->Tenant != null && $this->Tenant->PaymentPlan != null && $paymentplan->ID == $this->Tenant->PaymentPlan->ID);
						$fvPaymentPlans[] = $fvPaymentPlan;
					}
					$fv->Items[] = new FormViewItemChoice("cboPaymentPlan", "tenant_PaymentPlanID", "Payment plan", null, $fvPaymentPlans);
					
					$fv->Items[] = new FormViewItemDateTime("txtActivationDate", "tenant_BeginTimestamp", "Activation date", ($this->Tenant != null ? $this->Tenant->BeginTimestamp : null), true);
					$fv->Items[] = new FormViewItemDateTime("txtTerminationDate", "tenant_EndTimestamp", "Termination date", ($this->Tenant != null ? $this->Tenant->EndTimestamp : null), true);
					
					$fv->Items[] = new FormViewItemMemo("txtDescription", "tenant_Description", "Description", $this->Tenant->Description);
					$fv->Render();
				});
				
				$path = System::GetVirtualPath();
				if ($path[1] != "clone")
				{
					$tbs->TabPages[] = new TabPage("tabCustomProperties", "Custom Properties", null, null, null, function()
					{
						$lv = new ListView();
						$lv->Width = "100%";
						$lv->EnableAddRemoveRows = true;
						$lv->Columns = array
						(
							new ListViewColumn("lvcProperty", "Property"),
							new ListViewColumn("lvcDescription", "Description"),
							new ListViewColumn("lvcValue", "Value")
						);
						
						$properties = $this->Tenant->GetProperties();
						foreach ($properties as $property)
						{
							$lv->Items[] = new ListViewItem(array
							(
								new ListViewItemColumn("lvcProperty", $property->Name),
								new ListViewItemColumn("lvcDescription", $property->Description),
								new ListViewItemColumn("lvcValue", null, null, function($property)
								{
									$property->DataType->RenderEditor($this->Tenant->GetPropertyValue($property), "Property_" . $property->ID);
								}, $property)
							));
						}
						
						$lv->Render();
					});
					
					$tbs->TabPages[] = new TabPage("tabEnabledModules", "Enabled Modules", null, null, null, function()
					{
						$lv = new ListView();
						$lv->Width = "100%";
						$lv->EnableRowCheckBoxes = true;
						$lv->Columns = array
						(
							new ListViewColumn("lvcModule", "Module"),
							new ListViewColumn("lvcDescription", "Description")
						);
				
						$modules = Module::Get(null);
						foreach ($modules as $module)
						{
							$item = new ListViewItem(array
							(
								new ListViewItemColumn("lvcModule", "<a href=\"" . System::ExpandRelativePath("~/tenant/modify/" . $this->Tenant->URL . "/modules/" . $module->ID . "\">" . $module->Title . "</a>", $module->Title)),
								new ListViewItemColumn("lvcDescription", $module->Description)
							));
							
							$item->Checked = $this->Tenant->HasModule($module);
							
							$lv->Items[] = $item;
						}
						
						$lv->Render();
					});
					
					$tbs->TabPages[] = new TabPage("tabGlobalObjects", "Global Objects", null, null, null, function()
					{
						$lv = new ListView();
						$lv->Width = "100%";
						$lv->EnableAddRemoveRows = true;
						$lv->Columns = array
						(
							new ListViewColumn("lvcObject", "Object"),
							new ListViewColumn("lvcDescription", "Description"),
							new ListViewColumn("lvcInstances", "Instances")
						);
						
						$objects = TenantObject::Get(null, $this->Tenant);
						foreach ($objects as $object)
						{
							$lv->Items[] = new ListViewItem(array
							(
								new ListViewItemColumn("lvcObject", $object->Name),
								new ListViewItemColumn("lvcDescription", $object->Description),
								new ListViewItemColumn("lvcInstances", "<a href=\"" . System::ExpandRelativePath("~/tenant/modify/" . $this->Tenant->URL . "/objects/" . $object->ID . "/instances") . "\">" . $object->CountInstances() . "</a>")
							));
						}
						
						$lv->Render();
					});
				}
				$tbs->SelectedTab = $tbs->TabPages[0];
				$tbs->Render();
				?>
				<div class="Buttons">
					<input class="Button Default" type="submit" value="Save Changes" />
					<a class="Button" onclick="return nav('/tenant');" href="<?php echo(System::ExpandRelativePath("~/tenant")); ?>">Cancel</a>
				</div>
			</form>
			<script type="text/javascript">
			var TenantManagementPageMode =
			{
				"Create": 1,
				"Modify": 2
			}
			<?php
			if ($this->Mode == TenantManagementPageMode::Create)
			{
				echo("var Mode = TenantManagementPageMode.Create;");
			}
			else if ($this->Mode == TenantManagementPageMode::Modify)
			{
				echo("var Mode = TenantManagementPageMode.Modify;");
			}
			?>
			function form_OnSubmit()
			{
				if (Mode == TenantManagementPageMode.Create)
				{
					var xhr = new XMLHttpRequest();
					var tenantName = document.getElementById('txtTenantURL').value;
					
					xhr.open("GET", "/API/Tenant.php?action=exists&q=" + tenantName, false);
					xhr.send(null);
					
					var obj = JSON.parse(xhr.responseText);
					if (obj.result == "success" && obj.exists)
					{
						Notification.Show("Tenant '" + tenantName + "' already exists", "Please specify a unique tenant name", "Error");
						return false;
					}
				}
				
				Notification.Show("Changes were saved successfully", "Taking you back to the Tenant Management page", "Success");
				return true;
			}
			</script>
			<?php
		}
	}
?>