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
	
	class TenantTypeWebPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
		}
	}
	class TenantTypeModifyPage extends TenantTypeWebPage
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
						echo("<input type=\"hidden\" name=\"tenanttype_ID\" value=\"" . $this->CurrentObject->ID . "\" />");
					}
				
					$fv = new FormView("fv");
					$fv->Items[] = new FormViewItemText("txtObjectName", "tenanttype_Title", "Title", $this->CurrentObject->Title);
					$fv->Items[0]->Required = true;
					
					$fv->Items[] = new FormViewItemMemo("txtObjectDescription", "tenanttype_Description", "Description", $this->CurrentObject->Description);
					$fv->Render();
				?>
				<div class="Buttons">
					<input class="Button Default" type="submit" value="Save Changes" />
					<a class="Button" href="<?php echo(System::ExpandRelativePath("~/tenant-types")); ?>">Cancel</a>
				</div>
			</form>
			<?php
		}
	}
	
	class TenantTypeBrowsePage extends TenantTypeWebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->HeaderButtons[] = new NavigationButton("~/tenant-types/modify", "Create Tenant Type", "fa-plus-circle", "return nav('/tenant-types/modify');", "Default");
		}
		protected function RenderContent()
		{
			$items = TenantType::Get();
			
			$lv = new ListView();
			$lv->Columns = array
			(
				new ListViewColumn("chTenantTypeTitle", "Title"),
				new ListViewColumn("chTenantTypeDescription", "Description")
			);
			
			foreach ($items as $item)
			{
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("chTenantTypeTitle", "<a href=\"" . System::ExpandRelativePath("~/tenant-types/modify/" . $item->ID) . "\">" . $item->Title. "</a>"),
					new ListViewItemColumn("chTenantTypeDescription", $item->Description)
				));
			}
			
			$lv->Render();
		}
	}
	
	System::$Modules[] = new \WebFX\Module("net.phoenixsns.TenantManager.TenantType", array
	(
		new ModulePage("tenant-types", array
		(
			new ModulePage("", function($page, $path)
			{
				$page = new TenantTypeBrowsePage();
				$page->Render();
				return true;
			}),
			new ModulePage("modify", function($page, $path)
			{
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					if (isset($_POST["tenanttype_ID"]))
					{
						$tenanttype = TenantType::GetByID($_POST["tenanttype_ID"]);
					}
					else
					{
						$tenanttype = new TenantType();
					}
					$tenanttype->Title = $_POST["tenanttype_Title"];
					$tenanttype->Description = $_POST["tenanttype_Description"];
					$tenanttype->Update();
					
					System::Redirect("~/tenant-types");
				}
				else
				{
					$page = new TenantTypeModifyPage();
					$page->CurrentObject = TenantType::GetByID($path[0]);
					$page->Render();
				}
				return true;
			})
		))
	));
?>