<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\ModulePage;
	
	use WebFX\System;
	
	use WebFX\WebScript;
	use WebFX\WebStyleSheet;
	
	use WebFX\Controls\FormView;
	use WebFX\Controls\FormViewItem;
	use WebFX\Controls\FormViewItemText;
	use WebFX\Controls\FormViewItemMemo;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\TabContainer;
	use WebFX\Controls\TabPage;
	
	use PhoenixSNS\TenantManager\MasterPages\NavigationButton;
	use PhoenixSNS\TenantManager\MasterPages\WebPage;
	
	use PhoenixSNS\Objects\DataType;
	use PhoenixSNS\Objects\Module;
	use PhoenixSNS\Objects\PaymentPlan;
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantObject;
	use PhoenixSNS\Objects\TenantProperty;
	use PhoenixSNS\Objects\TenantPropertyValue;
	use PhoenixSNS\Objects\TenantStatus;
	use PhoenixSNS\Objects\TenantType;
	
	class PaymentPlanWebPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
		}
	}
	class PaymentPlanModifyPage extends PaymentPlanWebPage
	{
		public $CurrentObject;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->StyleSheets[] = new WebStyleSheet("http://static.alcehosting.net/dropins/CodeMirror/StyleSheets/CodeMirror.css");
			$this->Scripts[] = new WebScript("http://static.alcehosting.net/dropins/CodeMirror/Scripts/Addons/Edit/MatchBrackets.js");
			
			$this->Scripts[] = new WebScript("http://static.alcehosting.net/dropins/CodeMirror/Scripts/Modes/clike/clike.js");
			$this->Scripts[] = new WebScript("http://static.alcehosting.net/dropins/CodeMirror/Scripts/Modes/php/php.js");
		}
		
		protected function Initialize()
		{
			if ($this->CurrentObject != null)
			{
				$this->Title = "Modify Payment Plan";
				$this->Subtitle = $this->CurrentObject->Name;
			}
			else
			{
				$this->Title = "Manage Data Types";
			}
		}
		
		protected function RenderContent()
		{
			?>
			<style type="text/css">
			.CodeMirror
			{
				border: dotted 1px #AAAAAA;
				line-height: 18px;
			}
			.CodeMirror .CodeMirror-linenumbers
			{
				width: 48px;
			}
			</style>
			<script type="text/javascript">
			// TODO: make this work with AJAX
			
			var CodeMirrorDefaultParameters = 
			{
				lineNumbers: true,
				matchBrackets: true,
				mode: "text/x-php",
				indentUnit: 4,
				indentWithTabs: true
			};
			
			function InitializeCodeMirror(id)
			{
				var txt = document.getElementById(id);
				if (txt.CodeMirrorInitialized) return;
				
				var edt = CodeMirror.fromTextArea(txt, CodeMirrorDefaultParameters);
				txt.CodeMirrorInitialized = true;
			}
			function tbs_OnClientTabChanged()
			{
				switch (tbs.SelectedTabID)
				{
					case "tabEncoder":
					{
						InitializeCodeMirror("txtEncoderCodeBlob");
						break;
					}
					case "tabDecoder":
					{
						InitializeCodeMirror("txtDecoderCodeBlob");
						break;
					}
					case "tabColumnRenderer":
					{
						InitializeCodeMirror("txtColumnRendererCodeBlob");
						break;
					}
					case "tabEditorRenderer":
					{
						InitializeCodeMirror("txtEditorRendererCodeBlob");
						break;
					}
				}
			}
			</script>
			<form method="POST">
				<?php
					if ($this->CurrentObject != null)
					{
						echo("<input type=\"hidden\" name=\"paymentplan_ID\" value=\"" . $this->CurrentObject->ID . "\" />");
					}
				
					$fv = new FormView("fv");
					$fv->Items[] = new FormViewItemText("txtObjectName", "paymentplan_Title", "Title", $this->CurrentObject->Title);
					$fv->Items[0]->Required = true;
					
					$fv->Items[] = new FormViewItemMemo("txtObjectDescription", "paymentplan_Description", "Description", $this->CurrentObject->Description);
					$fv->Render();
				?>
				<div class="Buttons">
					<input class="Button Default" type="submit" value="Save Changes" />
					<a class="Button" href="<?php echo(System::ExpandRelativePath("~/payment-plans")); ?>">Cancel</a>
				</div>
			</form>
			<?php
		}
	}
	
	class PaymentPlanBrowsePage extends PaymentPlanWebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->HeaderButtons[] = new NavigationButton("~/payment-plans/modify", "Create Payment Plan", "fa-plus-circle", "return nav('/payment-plans/modify');", "Default");
		}
		protected function RenderContent()
		{
			$items = PaymentPlan::Get();
			
			$lv = new ListView();
			$lv->Columns = array
			(
				new ListViewColumn("chPaymentPlanTitle", "Title"),
				new ListViewColumn("chPaymentPlanDescription", "Description")
			);
			
			foreach ($items as $item)
			{
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("chPaymentPlanTitle", "<a href=\"" . System::ExpandRelativePath("~/payment-plans/modify/" . $item->ID) . "\">" . $item->Title. "</a>"),
					new ListViewItemColumn("chPaymentPlanDescription", $item->Description)
				));
			}
			
			$lv->Render();
		}
	}
	
	System::$Modules[] = new \WebFX\Module("net.phoenixsns.TenantManager.PaymentPlan", array
	(
		new ModulePage("payment-plans", array
		(
			new ModulePage("", function($page, $path)
			{
				$page = new PaymentPlanBrowsePage();
				$page->Render();
				return true;
			}),
			new ModulePage("modify", function($page, $path)
			{
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					if (isset($_POST["paymentplan_ID"]))
					{
						$paymentplan = PaymentPlan::GetByID($_POST["paymentplan_ID"]);
					}
					else
					{
						$paymentplan = new PaymentPlan();
					}
					$paymentplan->Title = $_POST["paymentplan_Title"];
					$paymentplan->Description = $_POST["paymentplan_Description"];
					$paymentplan->Update();
					
					System::Redirect("~/payment-plans");
				}
				else
				{
					$page = new PaymentPlanModifyPage();
					$page->CurrentObject = PaymentPlan::GetByID($path[0]);
					$page->Render();
				}
				return true;
			})
		))
	));
?>