<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use WebFX\Controls\AdditionalDetailWidget;
	use WebFX\Controls\AdditionalDetailWidgetDisplayStyle;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewColumnCheckBox;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\Disclosure;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	use PhoenixSNS\TenantManager\MasterPages\NavigationButton;
	
	use PhoenixSNS\Objects\Tenant;
	
	class TenantMainPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->Title = "Tenant Management";
			$this->Subtitle = "Configure multiple tenants for your PhoenixSNS installation";
			
			$this->SidebarButtons[3]->Expanded = true;
		}
		
		protected function RenderContent()
		{
			$tenants = Tenant::Get();
		?>
		<p>There are <?php echo(count($tenants)); ?> tenants in total.</p>
		<?php
			$countActive = 0;
			$countExpired = 0;
			foreach ($tenants as $tenant)
			{
				if ($tenant->IsExpired())
				{
					$countExpired++;
				}
				else
				{
					$countActive++;
				}
			}
			
			$disclosure = new Disclosure();
			$disclosure->Title = "Active Tenants (" . $countActive . ")";
			$disclosure->Expanded = true;
			$disclosure->BeginContent();
			
			/*
			$lv = new ListView("lvInactive");
			$lv->Columns = array
			(
				new ListViewColumn("chTenantName", "Tenant Name"),
				new ListViewColumn("chTenantType", "Tenant Type"),
				new ListViewColumn("chDataCenters", "Data Centers"),
				new ListViewColumn("chPaymentPlan", "Payment Plan"),
				new ListViewColumn("chActivationDate", "Activation Date"),
				new ListViewColumn("chTerminationDate", "Termination Date"),
				new ListViewColumn("chDescription", "Description"),
				new ListViewColumn("chActions", "Actions")
			);
			*/
			
			$lv = new ListView();
			$lv->EnableRowCheckBoxes = true;
			$lv->Width = "100%";
			$lv->Columns = array
			(
				new ListViewColumn("lvcDescription", "Description"),
				new ListViewColumn("lvcTenantType", "Type"),
				new ListViewColumn("lvcPaymentPlan", "Payment Plan"),
				new ListViewColumn("lvcDataCenters", "Data Centers"),
				new ListViewColumn("lvcStartDate", "Start Date"),
				new ListViewColumn("lvcEndDate", "End Date")
			);
			
			foreach ($tenants as $tenant)
			{
				if ($tenant->IsExpired()) continue;
				
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcDescription", "<div class=\"Title\">" . $tenant->URL . "</div><div class=\"Content\">" . $tenant->Description . "</div>" . "<div class=\"Footer\" style=\"padding: 8px;\">" .
						"<a class=\"Button Default\" href=\"" . System::ExpandRelativePath("~/tenant/launch/" . $tenant->URL) . "\" target=\"_blank\">Launch</a>" .
						"<span class=\"Separator\" />" .
						"<a class=\"Button\" href=\"" . System::ExpandRelativePath("~/tenant/modify/" . $tenant->URL) . "\">Modify</a>" .
						"<a class=\"Button\" href=\"" . System::ExpandRelativePath("~/tenant/clone/" . $tenant->URL) . "\">Clone</a>" .
						"<a class=\"Button\" href=\"" . System::ExpandRelativePath("~/tenant/delete/" . $tenant->URL) . "\">Delete</a>" .
					"</div>", $tenant->Description . " " . $tenant->URL),
					new ListViewItemColumn("lvcTenantType", $tenant->Type->Title),
					new ListViewItemColumn("lvcPaymentPlan", $tenant->PaymentPlan->Title),
					new ListViewItemColumn("lvcDataCenters", null),
					new ListViewItemColumn("lvcStartDate", $tenant->BeginTimestamp == null ? "(none)" : $tenant->BeginTimestamp),
					new ListViewItemColumn("lvcEndDate", $tenant->EndTimestamp == null ? "(none)" : $tenant->EndTimestamp)
				));
				/*
				?>
					<div class="Footer">
						<a class="Button Default" href="<?php echo(System::ExpandRelativePath("~/tenant/launch/" . $tenant->URL)); ?>" target="_blank">Launch</a>
						<span class="Separator" />
						<a class="Button" onclick="return nav('/tenant/modify/<?php echo($tenant->URL); ?>');" href="<?php echo(System::ExpandRelativePath("~/tenant/modify/" . $tenant->URL)); ?>">Modify</a>
						<a class="Button" href="<?php echo(System::ExpandRelativePath("~/tenant/clone/" . $tenant->URL)); ?>">Clone</a>
						<a class="Button" href="<?php echo(System::ExpandRelativePath("~/tenant/delete/" . $tenant->URL)); ?>">Delete</a>
					</div>
				</div>
				<?php
				*/
			}
			
			$lv->Render();
			
			$disclosure->EndContent();
			
			$disclosure = new Disclosure();
			$disclosure->Title = "Inactive Tenants (" . $countExpired . ")";
			$disclosure->Expanded = true;
			$disclosure->BeginContent();
			
			$lv = new ListView();
			$lv->EnableRowCheckBoxes = true;
			$lv->Width = "100%";
			$lv->Columns = array
			(
				new ListViewColumn("lvcDescription", "Description"),
				new ListViewColumn("lvcTenantType", "Type"),
				new ListViewColumn("lvcPaymentPlan", "Payment Plan"),
				new ListViewColumn("lvcDataCenters", "Data Centers"),
				new ListViewColumn("lvcStartDate", "Start Date"),
				new ListViewColumn("lvcEndDate", "End Date")
			);
			
			foreach ($tenants as $tenant)
			{
				if (!$tenant->IsExpired()) continue;
				
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcDescription", "<div class=\"Title\">" . $tenant->URL . "</div><div class=\"Content\">" . $tenant->Description . "</div>", $tenant->Description . " " . $tenant->URL),
					new ListViewItemColumn("lvcTenantType", $tenant->Type->Title),
					new ListViewItemColumn("lvcPaymentPlan", $tenant->PaymentPlan->Title),
					new ListViewItemColumn("lvcDataCenters", null),
					new ListViewItemColumn("lvcStartDate", $tenant->BeginTimestamp == null ? "(none)" : $tenant->BeginTimestamp),
					new ListViewItemColumn("lvcEndDate", $tenant->EndTimestamp == null ? "(none)" : $tenant->EndTimestamp)
				));
			}
			
			$lv->Render();
			
			$disclosure->EndContent();
		}
	}
?>