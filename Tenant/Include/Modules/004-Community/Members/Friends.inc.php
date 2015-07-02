<?php
	use WebFX\System;
	
	function RenderMemberTile($member)
	{
?>
<div class="Tile">
	<div class="TileImage">
		<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $member->ShortName)); ?>" onclick="DisplayMemberInformation(<?php echo($member->ID); ?>); return false;">
		<img class="ButtonGroupButtonImage" src="<?php echo(System::ExpandRelativePath("~/community/members/" . $member->ShortName . "/images/avatar/thumbnail.png")); ?>" />
		</a>
	</div>
	<div class="TileTitle">
		<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $member->ShortName)); ?>" onclick="DisplayMemberInformation(<?php echo($member->ID); ?>); return false;">
		<?php \PhoenixSNS\Objects\mmo_display_user_badges_by_user($member); echo($member->LongName); ?>
		</a>
	</div>
	<div class="TileContent">
		<div style="font-size: 0.8em;">Friends since</div>
		<div style="padding-left: 8px;"><?php echo($member->Timestamp); ?></div>
	</div>
</div>
<?php
	}

	$circles = $thisuser->GetFriendCircles();
	foreach ($circles as $circle)
	{
	?>
	<div class="Panel">
		<h3 class="PanelTitle"><?php echo($circle->Title); ?></h3>
		<div class="PanelContent">
		<?php
		$friendsInCircle = $circle->GetFriends();
		foreach ($friendsInCircle as $friend)
		{
			RenderMemberTile($friend->User);
		}
		?>
	</div>
	<?php
	}
?>
<div class="Panel">
	<h3 class="PanelTitle">Uncategorized</h3>
	<div class="PanelContent">
		<div class="TileView">
		<?php
		foreach ($friends as $friend)
		{
			RenderMemberTile($friend->User);
		}
		?>
		</div>
	</div>
</div>