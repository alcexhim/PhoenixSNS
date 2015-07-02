<?php
	$page = new PsychaticaWebPage("Requests/Notifications");
	$page->BeginContent();
?>
<table style="width: 100%">
	<tr>
		<td style="width: 50%">
			<div class="Panel">
				<h3 class="PanelTitle">Friend Requests</h3>
				<div class="PanelContent">
					<div class="ButtonGroup ButtonGroupHorizontal">
					<?php
						$requests = mmo_get_user_friend_requests();
						foreach ($requests as $member)
						{
					?>
					<a class="ButtonGroupButton" href="/community/members/<?php echo($member->ShortName); ?>" onclick="DisplayMemberInformation(<?php echo($member->ID); ?>); return false;">
						<img class="ButtonGroupButtonImage" src="/community/members/<?php echo($member->ShortName); ?>/images/avatar/thumbnail.png" />
						<span class="ButtonGroupButtonText"><?php echo($member->LongName); ?></span>
					</a>
					<?php
						}
					?>
					</div>
				</div>
			</div>
		</td>
		<td rowspan="2">
			<div class="Panel">
				<h3 class="PanelTitle">Notifications</h3>
				<div class="PanelContent">
					<div>
					<?php
						$notifications = array();
						foreach ($notifications as $notification)
						{
					?>
							<div>
								<?php if ($notification->Sender != null) { ?><a href="/community/members/<?php echo($notification->Sender->ShortName); ?>" target="_blank"><?php echo($notification->Sender->LongName); ?></a><?php } ?>
								<?php echo($notification->Description); ?>
							</div>
					<?php
						}
					?>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td style="width: 50%">
			<div class="Panel">
				<h3 class="PanelTitle">Trade Requests</h3>
				<div class="PanelContent">
				</div>
			</div>
		</td>
	</tr>
</table>
<?php
	$page->EndContent();
?>