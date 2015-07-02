<?php
	use WebFramework\Controls\ListView;
	use WebFramework\Controls\ListViewColumn;
	use WebFramework\Controls\ListViewItem;
	use WebFramework\Controls\ListViewItemColumn;

	$journal = Journal::GetByIDOrName($path[3]);
	if ($journal == null)
	{
	?>
	<div class="Panel">
		<h3 class="PanelTitle">Error</h3>
		<div class="PanelContent">
			<p>
				The journal does not exist. It may have been deleted, or you may have spelled the name wrong.
			</p>
			<p style="text-align: center;"><a href="/community/members/<?php echo($thisuser->ShortName); ?>/journals">Return to <?php echo($thisuser->LongName); ?>'s Journals</a></p>
		</div>
	</div>
	<?php
		return;
	}
	
	if ($path[4] == "entries")
	{
		if (count($path) > 5)
		{
			switch ($path[5])
			{
				case "create.mmo":
				{
					require("Entries/Create.inc.php");
					return;
				}
			}
			$entry = JournalEntry::GetByIDOrName($path[5]);
		}
		if ($entry == null)
		{
			require("Entries/Browse.inc.php");
			return;
		}
		else
		{
			require("Entries/Detail.inc.php");
			return;
		}
	}
	else if ($path[4] == "entries.rss" || $path[4] == "entries.atom")
	{
		require("Entries/Feed.inc.php");
		return;
	}
?>
<div class="ProfilePage">
	<div class="ProfileTitle">
		<span class="ProfileUserName"><?php echo($journal->Title); ?></span>
		<span class="ProfileControlBox">
			<a class="FeedLink" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries.rss")); ?>" target="_blank">RSS</a>
			<a class="FeedLink" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries.atom")); ?>" target="_blank">Atom</a>
		<?php
			if ($journal->Creator->ID == $CurrentUser->ID)
			{
		?>
		<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/modify.mmo")); ?>">Modify</a>
		<a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/remove.mmo")); ?>">Remove</a>
		<?php
			}
		?></span>
	</div>
	<div class="ProfileContentSection">
		<table style="width: 100%">
			<tr>
				<td style="width: 128px;">
					<img src="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/images/thumbnail.png")); ?>" />
				</td>
				<td>
					<?php echo($journal->Description); ?>
				</td>
			</tr>
		</table>
		<div class="Panel">
			<?php 
				$entries = $journal->GetEntries();
			?>
			<h3 class="PanelTitle">Entries (<?php echo(count($entries)); ?>)<?php if ($journal->Creator->ID == $CurrentUser->ID) { ?><a class="PanelTitleMini" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries/create.mmo")); ?>">create entry</a><?php } ?></h3>
			<div class="PanelContent">
			<?php
				if (count($entries) == 0)
				{
			?><p>There are no entries in this journal.</p>
			<?php
				}
				else
				{
					$lvJournalEntries = new ListView("lvJournalEntries");
					$lvJournalEntries->Columns[] = new ListViewColumn("chTimestampCreated", "Created");
					$lvJournalEntries->Columns[] = new ListViewColumn("chTimestampModified", "Modified");
					$lvJournalEntries->Columns[] = new ListViewColumn("chTitle", "Title");
					
					foreach ($entries as $entry)
					{
						$lvi = new ListViewItem();
						$lvi->NavigateURL = "~/Community/Members/" . $thisuser->ShortName . "/Journals/" . $journal->Name . "/Entries/" . $entry->Name;
						$lvi->Columns[] = new ListViewItemColumn("chTimestampCreated", $entry->TimestampCreated);
						$lvi->Columns[] = new ListViewItemColumn("chTimestampModified", $entry->TimestampModified);
						$lvi->Columns[] = new ListViewItemColumn("chTitle", $entry->Title);
						$lvJournalEntries->Items[] = $lvi;
					}
					
					$lvJournalEntries->Render();
				}
			?>
			</div>
		</div>
	</div>
</div>