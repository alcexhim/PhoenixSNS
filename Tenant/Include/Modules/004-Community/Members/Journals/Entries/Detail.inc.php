<?php
	$entryUrl = System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries/" . $entry->Name);
	
	if (count($path) > 6 && $path[6] != "")
	{
		switch ($path[6])
		{
			case "Modify.mmo":
			{
				require("Modify.inc.php");
				return;
			}
			case "Remove.mmo":
			{
				require("Remove.inc.php");
				return;
			}
			case "Impressions.mmo":
			{
				require("Impressions.inc.php");
				return;
			}
		}
	}
	
	// update the Impressions for this journal entry
	if ($journal->Creator->ID != $CurrentUser->ID)
	{
		$entry->UpdateImpressions();
	}
?>
<div class="ProfilePage">
	<div class="ProfileTitle">
		<span class="ProfileUserName"><?php echo($entry->Title); ?></span>
		<span class="ProfileControlBox"><?php if ($journal->Creator->ID == $CurrentUser->ID) { ?><a href="<?php echo($entryUrl . "/impressions.mmo"); ?>"><?php echo($entry->CountImpressions()); ?> impressions</a><?php } ?><a href="/community/members/<?php echo($thisuser->ShortName); ?>/journals/<?php echo($journal->Name); ?>">Return to Journal</a> <?php if ($journal->Creator->ID == $CurrentUser->ID) { ?><a href="/community/members/<?php echo($thisuser->ShortName); ?>/journals/<?php echo($journal->Name); ?>/entries/<?php echo($entry->Name); ?>/modify.mmo">Modify</a> <a href="/community/members/<?php echo($thisuser->ShortName); ?>/journals/<?php echo($journal->Name); ?>/entries/<?php echo($entry->Name); ?>/remove.mmo">Remove</a><?php } ?></span>
	</div>
	<div class="ProfileContent">
		<p><?php echo($entry->Content); ?></p>
		<div class="Panel">
			<?php
			if ($path[6] == "comment")
			{
			?>
			<h3 class="PanelTitle">Leave a Comment</h3>
			<div class="PanelContent">
				<form action="comment" method="POST">
					<table style="width: 100%">
						<tr>
							<td style="width: 200px;">
								<label for="txtTitle"><u>T</u>itle (optional):</label>
							</td>
							<td>
								<input type="text" id="txtTitle" name="comment_title" style="width: 100%" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="txtContent"><u>C</u>ontent:</label>
							</td>
							<td>
								<textarea id="txtContent" name="comment_content" style="width: 100%" rows="5"></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: right;">
								<input type="submit" value="Save Changes" />
								<a class="Button" href="<?php echo($entryUrl); ?>">Cancel</a>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<?php
			}
			else
			{
				$comments = $entry->GetComments();
			?>
			<div class="ProfilePage">
				<div class="ProfileTitle">
					<span class="ProfileUserName">Comments (<?php echo(count($comments)); ?>)</span>
					<span class="ProfileControlBox">
						<a href="<?php echo($entryUrl . "/comment"); ?>">Leave a Comment</a>
					</span>
				</div>
				<div class="ProfileContent">
					<div class="CommentList">
				<?php
					foreach ($comments as $comment)
					{
						$comment->Render();
					}
				?>
					</div>
				</div>
			</div>
			<?php
			}
			?>
		</div>
	</div>
</div>