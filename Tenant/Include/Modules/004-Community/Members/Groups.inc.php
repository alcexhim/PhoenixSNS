<?php
	use WebFramework\Controls\ButtonGroup;
	use WebFramework\Controls\ButtonGroupButton;
?>
<div class="ProfilePage">
	<div class="ProfileTitle">
		<table style="width: 100%">
			<tr>
				<td>
					<span class="ProfileUserName">
						<input type="text" name="filter" id="txtFilter" placeholder="Type to search the list" style="width: 100%" />
					</span>
				</td>
				<td style="width: 220px">
					Order by:
					<span class="ActionList">
						<a id="lnkOrderBy_group_creation_date" href="#" onclick="OrderBy('group_creation_date');">Date Created</a>
					</span>
				</td>
			</tr>
		</table>
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
	</div>
	<div class="ProfileContent">
		<?php
			$buttonGroup = new ButtonGroup("btngGroupList");
			
			$groups = Group::GetByUser($thisuser);
			foreach ($groups as $group)
			{
				$buttonGroup->Items[] = new ButtonGroupButton(null, $group->Title, null, System::ExpandRelativePath("~/community/groups/" . $group->Name . "/images/avatar/thumbnail.png"), "~/community/groups/" . $group->Name, "GroupInformationDialog.ShowDialog(" . $group->ID . "); return false;");
			}
			
			$buttonGroup->Render();
		?>
	</div>
</div>