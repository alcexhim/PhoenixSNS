<?php
	use WebFX\System;

	use WebFX\Controls\BreadcrumbItem;
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	
	use PhoenixSNS\Objects\Group;
	
	use PhoenixSNS\Modules\Community\CommunityPage;
	
	class PsychaticaGroupCommunityPage extends CommunityPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->BreadcrumbItems = array
			(
				new BreadcrumbItem("~/community", "Community"),
				new BreadcrumbItem("~/community/groups", "Groups")
			);
			$this->Name = "groups";
			$this->Title = "Groups";
		}
		
		protected function RenderContent()
		{
?>
<div class="Panel">
	<?php
		$groups = Group::Get();
		$count = count($groups);
	?>
	<h3 class="PanelTitle">Groups (<?php echo($count); ?>)</h3>
	<div class="PanelContent">
		<div class="ProfilePage">
			<div class="ProfileTitle">
				<script type="text/javascript">
					function RefreshList()
					{
						$.ajax(
						{
							type: "GET",
							url: "<?php echo(System::$Configuration["Application.BasePath"]); ?>/ajax/search.php?type=group&query=" + txtFilter.value + "&order=" + orderByColumnName + "&all=0",
							dataType: "json",
							success: function(data)
							{
								if (data.result == "success")
								{
									var html = "";
									for (var i = 0; i < data.content.length; i++)
									{
										var group = data.content[i];
										html += "<a target=\"_blank\" href=\"/community/groups/" + group.name + "\" class=\"ButtonGroupButton\">";
										html += "<img class=\"ButtonGroupButtonImage\" src=\"/community/members/" + group.name + "/images/avatar/thumbnail.png\" />";
										html += "<span class=\"ButtonGroupButtonText\">" + group.title + "</span>";
										html += "</a>";
									}
									
									btngGroupList.innerHTML = html;
								}
								else
								{
									// dlgError.ShowDialog();
								}
							}
						});
					}
					
					var orderByColumnName = 'member_longname';
					function OrderBy(columnName)
					{
						orderByColumnName = columnName;
						
						var lnkOrderBy_member_longname = document.getElementById("lnkOrderBy_member_longname");
						var lnkOrderBy_member_date_registered = document.getElementById("lnkOrderBy_member_date_registered");
						if (orderByColumnName == "member_longname")
						{
							lnkOrderBy_member_longname.className = "Selected";
							lnkOrderBy_member_date_registered.className = "";
						}
						else if (orderByColumnName == "member_date_registered")
						{
							lnkOrderBy_member_longname.className = "";
							lnkOrderBy_member_date_registered.className = "Selected";
						}
						RefreshList();
					}
					
					var txtFilter = document.getElementById("txtFilter");
					var timeout = null;
					txtFilter.onkeyup = function()
					{
						if (timeout != null)
						{
							window.clearTimeout(timeout);
						}
						timeout = window.setTimeout(function()
						{
							var btngGroupList = document.getElementById("btngGroupList");
							RefreshList();
						}, 50);
					};
				</script>
				<span class="ProfileControlBox">
					<a href="<?php echo(System::ExpandRelativePath("~/community/groups/create.mmo")); ?>" onclick="DisplayCreateGroupDialog();">Create Group</a>
				</span>
			</div>
			<div class="ProfileContent">
				<?php
					$grpMembers = new ButtonGroup("grpMembers");
					foreach ($groups as $group)
					{
						$grpMembers->Items[] = new ButtonGroupButton(null, $group->Title, null, "~/community/groups/" . $group->Name . "/images/avatar/thumbnail.png", "~/community/groups/" . $group->Name, "GroupInformationDialog.ShowDialog(" . $group->ID . ");");
					}
					$grpMembers->Render();
				?>
			</div>
		</div>
	</div>
</div>
<?php
		}
	}
	
	$page = new PsychaticaGroupCommunityPage();
	$page->Render();
?>