<?php
	namespace PhoenixSNS\TenantManager\Pages;
	
	use WebFX\ModulePage;
	
	use WebFX\System;
	
	use WebFX\WebScript;
	use WebFX\WebStyleSheet;
	
	use WebFX\Controls\Disclosure;
	
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
	
	class DataTypeWebPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
		}
	}
	class DataTypeModifyPage extends DataTypeWebPage
	{
		public $CurrentDataType;
		
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
			if ($this->CurrentDataType != null)
			{
				$this->Title = "Modify Data Type";
				$this->Subtitle = $this->CurrentDataType->Name;
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
				switch (tbs.GetSelectedTabID())
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
					if ($this->CurrentDataType != null)
					{
						echo("<input type=\"hidden\" name=\"datatype_ID\" value=\"" . $this->CurrentDataType->ID . "\" />");
					}
					
					$tbs = new TabContainer("tbs");
					$tbs->OnClientTabChanged = "tbs_OnClientTabChanged();";
					$tbs->TabPages[] = new TabPage("tabGeneral", "General Information", null, null, null, function()
					{
						$fv = new FormView("fv");
						$fv->Width = "100%";
						$fv->Items[] = new FormViewItemText("txtDataTypeName", "datatype_Name", "Name", $this->CurrentDataType->Name);
						$fv->Items[0]->Required = true;
						
						$fv->Items[] = new FormViewItemMemo("txtDataTypeDescription", "datatype_Description", "Description", $this->CurrentDataType->Description);
						$fv->Render();
					});
					
					$tbs->TabPages[] = new TabPage("tabEncoder", "Encoder", null, null, null, function()
					{
						$ds = new Disclosure();
						$ds->Title = "Code";
						$ds->BeginContent();
						?>
						<textarea id="txtEncoderCodeBlob" name="datatype_EncoderCodeBlob" style="width: 100%;" rows="20"><?php echo($this->CurrentDataType->EncoderCodeBlob); ?></textarea>
						<?php
						$ds->EndContent();
						/*
						$ds = new Disclosure();
						$ds->Title = "Preview";
						$ds->BeginContent();
						
						?>
						<div class="FormView">
							<div class="Field">
								<label id="lblEncoderCodeBlobValue" for="txtEncoderCodeBlobValue">Value</label>
								<input type="text" onchange="__EncoderCodeBlob_RefreshResult();" />
							</div>
							<div class="Field">
								<label id="lblEncoderCodeBlobResult" for="txtEncoderCodeBlobResult">Result</label>
								<span id="txtEncoderCodeBlobResult">&nbsp;</span>
							</div>
						</div>
						<?php
						
						$ds->EndContent();
						*/
					});
					$tbs->TabPages[] = new TabPage("tabDecoder", "Decoder", null, null, null, function()
					{
						$ds = new Disclosure();
						$ds->Title = "Code";
						$ds->BeginContent();
						?>
						<textarea id="txtDecoderCodeBlob" name="datatype_DecoderCodeBlob" style="width: 100%;" rows="20"><?php echo($this->CurrentDataType->DecoderCodeBlob); ?></textarea>
						<?php
						$ds->EndContent();
					});
					$tbs->TabPages[] = new TabPage("tabColumnRenderer", "Column Renderer", null, null, null, function()
					{
						$ds = new Disclosure();
						$ds->Title = "Code";
						$ds->BeginContent();
						?>
						<textarea id="txtColumnRendererCodeBlob" name="datatype_ColumnRendererCodeBlob" style="width: 100%;" rows="20"><?php echo($this->CurrentDataType->ColumnRendererCodeBlob); ?></textarea>
						<?php
						$ds->EndContent();
						$ds = new Disclosure();
						$ds->Title = "Preview";
						$ds->BeginContent();
						$ds->EndContent();
					});
					$tbs->TabPages[] = new TabPage("tabEditorRenderer", "Editor Renderer", null, null, null, function()
					{
						$ds = new Disclosure();
						$ds->Title = "Code";
						$ds->BeginContent();
						?>
						<textarea id="txtEditorRendererCodeBlob" name="datatype_EditorRendererCodeBlob" style="width: 100%;" rows="20"><?php echo($this->CurrentDataType->EditorRendererCodeBlob); ?></textarea>
						<?php
						$ds->EndContent();
						$ds = new Disclosure();
						$ds->Title = "Preview";
						$ds->BeginContent();
						$ds->EndContent();
					});
					$tbs->SelectedTab = $tbs->TabPages[0];
					$tbs->Render();
				?>
				<div class="Buttons">
					<input class="Button Default" type="submit" value="Save Changes" />
					<a class="Button" href="<?php echo(System::ExpandRelativePath("~/data-types")); ?>">Cancel</a>
				</div>
			</form>
			<?php
		}
	}
	
	class DataTypeBrowsePage extends DataTypeWebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->HeaderButtons[] = new NavigationButton("~/data-types/modify", "Create Data Type", "fa-plus-circle", "return nav('/data-types/modify');", "Default");
		}
		protected function RenderContent()
		{
			$items = DataType::Get();
			
			$lv = new ListView();
			$lv->Columns = array
			(
				new ListViewColumn("chDataTypeName", "Name"),
				new ListViewColumn("chDataTypeDescription", "Description")
			);
			
			foreach ($items as $item)
			{
				$lv->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("chDataTypeName", "<a href=\"" . System::ExpandRelativePath("~/data-types/modify/" . $item->ID) . "\">" . $item->Name . "</a>"),
					new ListViewItemColumn("chDataTypeDescription", $item->Description)
				));
			}
			
			$lv->Render();
		}
	}
	
	System::$Modules[] = new \WebFX\Module("net.phoenixsns.TenantManager.DataType", array
	(
		new ModulePage("data-types", array
		(
			new ModulePage("", function($page, $path)
			{
				$page = new DataTypeBrowsePage();
				$page->Render();
				return true;
			}),
			new ModulePage("modify", function($page, $path)
			{
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					if (isset($_POST["datatype_ID"]))
					{
						$datatype = DataType::GetByID($_POST["datatype_ID"]);
					}
					else
					{
						$datatype = new DataType();
					}
					$datatype->Name = $_POST["datatype_Name"];
					$datatype->Description = $_POST["datatype_Description"];
					$datatype->EncoderCodeBlob = $_POST["datatype_EncoderCodeBlob"];
					$datatype->DecoderCodeBlob = $_POST["datatype_DecoderCodeBlob"];
					$datatype->ColumnRendererCodeBlob = $_POST["datatype_ColumnRendererCodeBlob"];
					$datatype->EditorRendererCodeBlob = $_POST["datatype_EditorRendererCodeBlob"];
					$datatype->Update();
					
					System::Redirect("~/data-types");
				}
				else
				{
					$page = new DataTypeModifyPage();
					$page->CurrentDataType = DataType::GetByID($path[0]);
					$page->Render();
				}
				return true;
			})
		))
	));
?>