<?php
	use WebFX\System;
	use PhoenixSNS\Objects\Message;
	
	if (count($path) > 2 && $path[2] != "")
	{
		$message = Message::GetByID($path[2]);
		$message->SetStatus(1);
		
		$page = new PsychaticaWebPage($message->Title . " | Received Messages | Message Center");
		$page->BeginContent();
		?>
		<div class="Panel">
			<h3 class="PanelTitle">Read Message</h3>
			<div class="PanelContent">
				<div class="ProfilePage">
					<div class="ProfileTitle">
						<span class="ProfileUserName"><?php echo($message->Title); ?></span>
						<span class="ActionList">
							<a href="/account/messages/inbox/<?php echo($message->ID); ?>/reply.mmo">Reply</a>
							<form action="/account/messages/create.mmo" method="POST" style="display: inline;">
								<input type="hidden" name="message_content" value="<?php echo($message->Content); ?>" />
								<input class="LinkButton" type="submit" value="Forward" />
							</form>
							<a href="#">Delete</a>
						</span>
					</div>
					<div class="ProfilePageContent">
					<?php
						echo($message->Content);
					?>
					</div>
				</div> 
			</div>
		</div>
		<div class="Panel">
			<h3 class="PanelTitle">Attachments</h3>
			<div class="PanelContent">
				<div class="ProfilePage">
					<div class="ProfileTitle">
						<span class="ProfileUserName">0 attachments</span>
						<span class="ActionList">
							<a href="#">Download All</a>
						</span>
					</div>
					<div class="ProfilePageContent">
					</div>
				</div> 
			</div>
		</div>
		<?php
		$page->EndContent();
		return;
	}
	
	
	$page = new PsychaticaWebPage("Received Messages | Message Center");
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
						<a href="/account/messages/inbox">Inbox</a>
						<?php } ?>
						<?php if ($path[1] == "outbox") { ?>
						<span class="Selected">Sent Messages</span>
						<?php } else { ?>
						<a href="/account/messages/outbox">Sent Messages</a>
						<?php } ?>
						<?php if ($path[1] == "create.mmo") { ?>
						<span class="Selected">Create Message</span>
						<?php } else { ?>
						<a href="/account/messages/create.mmo">Create Message</a>
						<?php } ?>
					</div>
				</td>
				<td style="vertical-align: top;">
					<table style="width: 100%">
						<tr>
							<th>From</th>
							<th>Subject</th>
							<th>Date Received</th>
						</tr>
						<?php
							$messages = Message::GetByReceiver($CurrentUser);
							foreach ($messages as $message)
							{
						?>
						<tr>
							<td>
								<div class="ButtonGroup ButtonGroupHorizontal">
									<a class="ButtonGroupButton" href="/community/members/<?php echo($message->Sender->ShortName); ?>">
										<img class="ButtonGroupButtonImage" src="/community/members/<?php echo($message->Sender->ShortName); ?>/images/avatar" />
										<span class="ButtonGroupButtonText"><?php echo($message->Sender->LongName); ?></span>
									</a>
								</div>
							</td>
							<td>
								<a href="/account/messages/inbox/<?php echo($message->ID); ?>"<?php if ($message->Status == 0) echo(" class=\"MessageUnread\""); ?>><?php echo($message->Title); ?></a>
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