<?php
	class PsychaticaForumCommunityPage extends PsychaticaCommunityPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->BreadcrumbItems = array
			(
				new WebBreadcrumbItem("~/community", "Community"),
				new WebBreadcrumbItem("~/community/forums", "Forums")
			);
			$this->Title = "Forums";
		}
		
		protected function RenderContent()
		{
?>
<div class="Panel">
	<h3 class="PanelTitle">Forums</h3>
	<div class="PanelContent">
		<div class="ProfilePage">
			<div class="ProfileTitle">
		<?php
			$forums = Forum::Get();
			$count = count($forums);
		?>
				<span class="ProfileUserName">
					<?php
						echo("There ");
						if ($count == 1) echo("is "); else echo("are ");
						echo($count);
						if ($count == 1) echo(" forum"); else echo(" forums");
						echo(".");
					?>
				</span>
				<span class="ProfileControlBox">
					<a href="<?php echo(System::ExpandRelativePath("~/community/forums/create.mmo")); ?>" onclick="ForumCreateDialog.Show();">Create Forum</a>
				</span>
			</div>
			<div class="ProfileContent">
			<?php
				$grpForums = new WebButtonGroupControl("grpForums");
				foreach ($forums as $item)
				{
					$grpForums->Items[] = new WebButtonGroupButton("~/community/forums/" . $item->Name, $item->Title, "~/community/forums/" . $item->Name . "/images/avatar/thumbnail.png", "ForumInformationDialog.ShowDialog(" . $item->ID . ");");
				}
				$grpForums->Render();
			?>
			</div>
		</div>
	</div>
</div>
<?php
		}
	}
	
	$page = new PsychaticaForumCommunityPage();
	$page->Render();
?>