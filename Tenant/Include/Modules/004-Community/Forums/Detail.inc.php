<?php
	$forum = Forum::GetByIDOrName($path[1]);
	
	if ($forum == null)
	{
		$page = new PsychaticaErrorPage("Forum not found");
		$page->Message = "That forum does not exist in the system. It may have been deleted, or you may have typed the name incorrectly.";
		$page->ReturnButtonURL = "~/community/forums";
		$page->ReturnButtonText = "Return to Forum List";
		$page->Render();
		return;
	}
	
	switch ($path[2])
	{
		case "images":
		{
			switch ($path[3])
			{
				case "avatar":
				{
					switch ($path[4])
					{
						case "thumbnail.png":
						{
							// paths are relative to the path of the including file
							header("Content-Type: image");
							$fileName = "images/icons/forum.png";
							// header("Content-Type: " . mime_content_type("images/icons/forum.png"));
							readfile($fileName);
							return;
						}
					}
					break;
				}
			}
			break;
		}
		case "delete.phnx":
		{
			require("delete.inc.php");
			return;
		}
		default:
		{
			if (count($path) > 3 && $path[3] != "")
			{
				switch($path[3])
				{
					case "create.mmo":
					{ // BEGIN create.mmo
						require("topic/create.inc.php");
						return;
					} // END create.mmo
					default:
					{
						require("topic/detail.inc.php");
						return;
					}
				}
			}
			else
			{
				$page = new PsychaticaWebPage($forum->Title . " | Forums");
				$page->BeginContent();
?>
<div class="ProfilePage">
	<div class="ProfileTitle">
		<span class="ProfileUserName">
		<?php
		echo ($forum->Title);
		?>
		</span>
		<span class="ProfileControlBox">
			<?php
			if ($CurrentUser != null)
			{
				/*
				if ($forum->HasPermission($CurrentUser, 2) || $forum->HasPermission($CurrentUser, 3))
				{
				?>
				<a href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $forum->Name . "/connect")); ?>">Join Group</a>
				<?php
				}
				if ($forum->HasPermission($CurrentUser, 4))
				{
				?>
				<a href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $forum->Name . "/settings")); ?>">Change Group Settings</a>
				<?php
				}
				*/
				
				if ($forum->CreationUser->ID == $CurrentUser->ID)
				{
				?>
				<a href="<?php echo(System::ExpandRelativePath("~/community/forums/" . $forum->Name . "/delete.phnx")); ?>">Delete Forum</a>
				<?php
				}
			}
			?>
			<a href="<?php echo(System::ExpandRelativePath("~/community/forums")); ?>">Return to Forums</a>
		</span>
	</div>
	<div class="ProfileContentSection">
		<table style="width: 100%">
			<tr>
				<td style="width: 25%">
					<div class="ActionList">
						<?php if ($path[2] == "") { ?>
						<span class="Selected">Information</span>
						<?php } else { ?>
						<a href="<?php echo(System::ExpandRelativePath("~/community/forums/" . $forum->Name)); ?>">Information</a>
						<?php } ?>
						
						<?php if ($path[2] == "topics") { ?>
						<span class="Selected">Topics (<?php echo($forum->CountTopics()); ?>)</span>
						<?php } else { ?>
						<a href="<?php echo(System::ExpandRelativePath("~/community/forums/" . $forum->Name . "/topics")); ?>">Topics (<?php echo($forum->CountTopics()); ?>)</a>
						<?php } ?>
					</div>
				</td>
				<td>
					<?php
					switch ($path[2])
					{
						case "topics":
						{
					?>
					<div class="Panel">
						<h3 class="PanelTitle">Topics (<?php echo($forum->CountTopics()); ?>) <?php if ($forum->HasMember($CurrentUser)) { ?><a class="PanelTitleMini" href="/community/groups/<?php echo($forum->Name); ?>/topics/create.mmo">create topic</a><?php } ?></h3>
						<div class="PanelContent">
							<div class="ListBox">
							<?php
							$topics = $forum->GetTopics(5);
							foreach ($topics as $topic)
							{
							?>
								<a href="/community/groups/<?php echo($forum->Name); ?>/topics/<?php echo($topic->Name); ?>">
									<div class="ListItemTitle"><?php echo($topic->Title); ?></div>
								</a>
							<?php
							}
							?>
							</div>
						</div>
					</div>
						<?php
							break;
						}
						default:
						{
					?>
					<div class="Panel">
						<h3 class="PanelTitle">About this Forum</h3>
						<div class="PanelContent">
							<table style="width: 100%">
								<tr>
									<td style="width: 128px;"><img src="<?php echo(System::ExpandRelativePath("~/community/forums/" . $forum->Name . "/images/avatar/thumbnail.png")); ?>" style="width: 112px; height: 112px;" /></td>
									<td><?php echo($forum->Description); ?></td>
								</tr>
							</table>
							<table style="width: 100%">
								<tr>
									<td style="width: 25%">Created on:</td>
									<td><?php echo($forum->CreationTimestamp); ?></td>
								</tr>
								<tr>
									<td>Created by:</td>
									<td>
										<div class="ButtonGroup ButtonGroupHorizontal">
											<a class="ButtonGroupButton" target="_blank" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $forum->CreationUser->ShortName)); ?>">
												<img class="ButtonGroupButtonImage" src="<?php echo(System::ExpandRelativePath("~/community/members/" . $forum->CreationUser->ShortName . "/images/avatar/thumbnail.png")); ?>" />
												<span class="ButtonGroupButtonText"><?php echo($forum->CreationUser->LongName); ?></span>
											</a>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<?php
							break;
						}
					}
					?>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
				$page->EndContent();
				return;
			}
		}
	}
?>