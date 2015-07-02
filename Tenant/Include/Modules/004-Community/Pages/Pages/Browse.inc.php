<?php
	namespace PhoenixSNS\Modules\Community\Pages;
	
	use PhoenixSNS\Objects\Page;
	
	use PhoenixSNS\Modules\Community\CommunityPage;
	
	use WebFX\Controls\BreadcrumbItem;
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	
	use WebFX\System;
	
	class PageCommunityPage extends CommunityPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->BreadcrumbItems = array
			(
				new BreadcrumbItem("~/community", "Community"),
				new BreadcrumbItem("~/community/pages", "Pages")
			);
		}
		
		protected function RenderContent()
		{
			$pages = Page::Get();
			$count = count($pages);
?>
<div class="Panel">
	<h3 class="PanelTitle">Pages (<?php echo($count); ?>)</h3>
	<div class="PanelContent">
		<div class="ProfilePage">
			<div class="ProfileTitle">
				<span class="ProfileUserName">
					<?php
						echo("There ");
						if ($count == 1) echo("is "); else echo("are ");
						echo($count);
						if ($count == 1) echo(" page"); else echo(" pages");
						echo(".");
					?>
				</span>
				<span class="ProfileControlBox">
					<a href="<?php echo(System::ExpandRelativePath("~/community/pages/create.mmo")); ?>" onclick="DisplayCreatePageDialog();">Create Page</a>
				</span>
			</div>
			<div class="ProfileContent">
			<?php
				$grpPages = new ButtonGroup("grpPages");
				foreach ($pages as $item)
				{
					$grpPages->Items[] = new ButtonGroupButton(null, $item->Title, null, "~/community/pages/" . $item->Name . "/images/thumbnail.png", "~/community/pages/" . $item->Name, "PageInformationDialog.ShowDialog(" . $item->ID . ");");
				}
				$grpPages->Render();
			?>
			</div>
		</div>
	</div>
</div>
<?php
		}
	}
	
	$page = new PageCommunityPage();
	$page->Render();
?>