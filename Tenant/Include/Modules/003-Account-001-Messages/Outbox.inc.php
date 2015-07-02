<?php
	use WebFX\System;
	
	if (count($path) > 2 && $path[2] != "")
	{
		$message = Message::GetByID($path[2]);
		
		$page = new PsychaticaWebPage($message->Title . " | Sent Messages | Message Center");
		$page->BeginContent();
		?>
		<div class="ProfilePage">
			<div class="ProfileTitle">
				<span class="ProfileUserName"><?php echo($message->Title); ?></span>
				<span class="ActionList">
					<a href="#">Reply</a>
					<a href="#">Forward</a>
					<a href="#">Delete</a>
				</span>
			</div>
			<div class="ProfilePageContent">
			<?php
				echo($message->Content);
			?>
			</div>
		</div> 
		<?php
		$page->EndContent();
		return;
	}
	
	$page = new PsychaticaWebPage("Sent Messages | Message Center");
	$page->BeginContent();
?>
<div class="Panel">
	<h3 class="PanelTitle">Private Messages</h3>
	<div class="PanelContent">
		<table style="width: 100%">
			<tr>
				<td style="width: 25%; vertical-align: top;">
					<div class="ActionList">
						<?php if ($path[1] == "inbox") { ?>
						<span class="Selected">Inbox</span>
						<?php } else { ?>
						<a href="<?php echo(System::ExpandRelativePath("~/account/messages/inbox")); ?>">Inbox</a>
						<?php } ?>
						<?php if ($path[1] == "outbox") { ?>
						<span class="Selected">Sent Messages</span>
						<?php } else { ?>
						<a href="<?php echo(System::ExpandRelativePath("~/account/messages/outbox")); ?>">Sent Messages</a>
						<?php } ?>
						<?php if ($path[1] == "create") { ?>
						<span class="Selected">Create Message</span>
						<?php } else { ?>
						<a href="<?php echo(System::ExpandRelativePath("~/account/messages/create")); ?>">Create Message</a>
						<?php } ?>
					</div>
				</td>
				<td style="vertical-align: top;">
					<table style="width: 100%">
						<tr>
							<th>Recipient</th>
							<th>Subject</th>
							<th>Date Sent</th>
						</tr>
						<?php
							$messages = Message::GetBySender($CurrentUser);
							foreach ($messages as $message)
							{
						?>
						<tr>
							<td>
								<div class="ButtonGroup ButtonGroupHorizontal">
									<a class="ButtonGroupButton" class="ProfileIcon" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $message->Receiver->ShortName)); ?>">
										<img class="ProfileIconImage" src="<?php echo(System::ExpandRelativePath("~/community/members/" . $message->Receiver->ShortName . "/images/avatar")); ?>" />
										<span class="ProfileIconText"><?php echo($message->Receiver->LongName); ?></span>
									</a>
								</div>
							</td>
							<td>
								<a href="<?php echo(System::ExpandRelativePath("~/account/messages/outbox/" . $message->ID)); ?>"><?php echo($message->Title); ?></a>
							</td>
							<td>
								<?php echo($message->Timestamp); ?>
							</td>
						</tr>
						<?php
							}
						?>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
	$page->EndContent();
?>