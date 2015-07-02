<div class="Panel">
	<h3 class="PanelTitle"><?php echo($thisuser->LongName); ?>'s Journals<?php if ($thisuser->IsAuthenticated) { ?> <a class="PanelTitleMini" href="/community/members/<?php echo($thisuser->ShortName); ?>/journals/create.mmo">Create Journal</a><?php } ?></h3>
	<div class="PanelContent">
		<div class="ButtonGroup ButtonGroupHorizontal">
		<?php
			$journals = Journal::GetByUser($thisuser);
			foreach ($journals as $journal)
			{
		?>
		<a class="ButtonGroupButton" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name)); ?>">
			<img class="ButtonGroupButtonImage" src="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/images/thumbnail.png")); ?>" />
			<span class="ButtonGroupButtonText"><?php echo($journal->Title); ?></span>
		</a>
		<?php
			}
		?>
		</div>
	</div>
</div>