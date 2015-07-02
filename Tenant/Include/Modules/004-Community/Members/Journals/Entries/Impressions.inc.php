<?php
	if ($journal->Creator->ID != $CurrentUser->ID)
	{
		System::Redirect("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries/" . $entry->Name);
		return;
	}
?>
<div class="ProfilePage">
	<div class="ProfileTitle">
		<span class="ProfileUserName"><?php echo($entry->Title); ?></span>
		<span class="ProfileControlBox"><a href="/community/members/<?php echo($thisuser->ShortName); ?>/journals/<?php echo($journal->Name); ?>/entries/<?php echo($entry->Name); ?>">Back to Journal Entry</a></span>
	</div>
	<div class="ProfileContentSection">
		<div class="Panel">
			<h3 class="PanelTitle">Impressions</h3>
			<div class="PanelContent">
				<table style="margin-left: auto; margin-right: auto;">
					<tr>
						<th>Member</th>
						<th>Times viewed</th>
					</tr>
					<?php
						$impressions = $entry->GetImpressions();
						foreach($impressions as $impression)
						{
					?>
					<tr>
						<td><a href="/community/members/<?php echo($impression->User->ShortName); ?>"><?php echo($impression->User->LongName); ?></a></td>
						<td><?php echo($impression->Count); ?></td>
					</tr>
					<?php
						}
					?>
				</table>
			</div>
		</div>
	</div>
</div>