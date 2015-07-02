<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\System;
	
	use WebFX\Controls\AdditionalDetailWidget;
	use WebFX\Controls\AdditionalDetailWidgetDisplayStyle;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\Disclosure;
	
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	use PhoenixSNS\Objects\Tenant;
	
	class DashboardPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
			$this->Title = "Dashboard";
			$this->Subtitle = "Welcome to PhoenixSNS";
		}
		
		protected function RenderContent()
		{
			?>
			<div class="Jumbotron">
				<div class="Title">The new PhoenixSNS is here</div>
				<div class="Content">Upgrade to the latest version for important security updates and bug fixes</div>
				<div class="Buttons"><a href="#" class="Button Default">Download now</a> <a href="#" class="Button">Learn more</a></div>
			</div>
			<div class="PanelContainer ThreeColumn">
			<?php
				$countActive = Tenant::Count(true, false);
				$countInactive = Tenant::Count(false, true);
			?>
				<div class="Panel Primary">
					<div class="Header">
						<div class="Expand">
							<div>
								<div class="Icon"><i class="fa fa-building-o fa-5x">&nbsp;</i></div>
							</div>
							<div>
								<div class="PrimaryContent"><?php echo($countActive); ?></div>
								<div class="SecondaryContent">active tenant<?php echo(($countActive != 1) ? "s" : ""); ?></div>
							</div>
						</div>
					</div>
					<div class="Footer Dark Borderless">
						<a href="<?php echo(System::ExpandRelativePath("~/tenant")); ?>">Manage Tenants</a>
					</div>
				</div>
				<div class="Panel Warning">
					<div class="Header">
						<div class="Expand">
							<div>
								<div class="Icon"><i class="fa fa-warning fa-5x">&nbsp;</i></div>
							</div>
							<div>
								<div class="PrimaryContent"><?php echo($countInactive); ?></div>
								<div class="SecondaryContent">inactive or expired tenant<?php echo(($countInactive != 1) ? "s" : ""); ?></div>
							</div>
						</div>
					</div>
					<div class="Footer Dark Borderless">
						<a href="<?php echo(System::ExpandRelativePath("~/tenant")); ?>">Manage Tenants</a>
					</div>
				</div>
				<div class="Panel Success">
					<div class="Header">
						<div class="Expand">
							<div>
								<div class="Icon"><i class="fa fa-warning fa-5x">&nbsp;</i></div>
							</div>
							<div>
								<div class="PrimaryContent">0</div>
								<div class="SecondaryContent">issues to resolve</div>
							</div>
						</div>
					</div>
					<div class="Footer Dark Borderless">
						<a href="#">View Details</a>
					</div>
				</div>
			</div>
			<?php
		}
	}
?>