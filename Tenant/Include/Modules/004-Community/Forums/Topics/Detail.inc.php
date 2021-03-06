<?php
$topic = GroupTopic::GetByIDOrName($thisgroup, $path[3]);

if (count($path) > 4 && $path[4] == "comment.mmo")
{
	if ($_POST["comment_content"] !== null)
	{
		$topic->AddComment($_POST["comment_title"], $_POST["comment_content"]);
		System::Redirect("~/community/groups/" . $thisgroup->Name . "/topics/" . $topic->Name);
		return;
	}
}

$page = new PsychaticaWebPage($topic->Title . " | " . $thisgroup->Title);
$page->BeginContent();
?>
<div class="ProfilePage">
	<div class="ProfileTitle">
		<span class="ProfileUserName">
		<?php
		echo ($thisgroup->Title);
		?>
		</span>
		<span class="ProfileControlBox">
			<a href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $thisgroup->Name . "/topics")); ?>">Topic List</a>
			<?php
			if ($CurrentUser != null)
			{
				if ($thisgroup->HasMember($CurrentUser))
				{
				?>
				<span class="Disabled">Invite Friends</span> <?php /* <a href="/community/groups/<?php echo($thisgroup->Name); ?>/invite">Invite Friends</a> */ ?>
				<a href="/community/groups/<?php echo($thisgroup->Name); ?>/disconnect">Leave Group</a>
				<?php
				}
				else
				{
				?>
					<a href="/community/groups/<?php echo($thisgroup->Name); ?>/connect">Join Group</a>
				<?php
				}
			}
			?>
		</span>
	</div>
	<div class="ProfileContentSection">
		<table style="width: 100%">
			<tr>
				<td style="width: 25%">
					<div class="ActionList">
						<a href="/community/groups/<?php echo($thisgroup->Name); ?>">Information</a>
						<a class="Selected" href="/community/groups/<?php echo($thisgroup->Name); ?>/topics">Topics (<?php echo($thisgroup->CountTopics()); ?>)</a>
						<a href="/community/groups/<?php echo($thisgroup->Name); ?>/members">Members (<?php echo($thisgroup->CountMembers()); ?>)</a>
					</div>
				</td>
				<td>
					<table style="width: 100%">
						<tr>
							<td>
								<div class="Panel">
									<h3 class="PanelTitle"><?php echo($topic->Title); ?></h3>
									<div class="PanelContent">
										<?php echo($topic->Description); ?>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="Panel">
									<?php
									if (count($path) > 6 && $path[6] == "delete.mmo")
									{
										if ($_POST["delete_confirm"] === null)
										{
									?>
									<div class="Window">
										<div class="WindowTitle">Are you sure you want to delete this comment?</div>
										<div class="WindowContent">
											The content of this comment cannot be recovered once deleted.
										</div>
										<div class="WindowButtons">
											<form method="POST" action="delete.mmo">
												<input type="hidden" name="delete_confirm" value="1" />
												<input type="submit" value="Delete Comment" />
											</form>
											<a class="Button" href="/community/groups/<?php echo($thisgroup->Name); ?>/topics/<?php echo($topic->Name); ?>">Cancel</a>
										</div>
									</div>
									<?php
										}
										else
										{
											$id = $path[5];
											$topic->RemoveComment($id);
											System::Redirect("~/community/groups/" . $thisgroup->Name . "/topics/" . $topic->Name);
											return;
										}
									}
									else if ((count($path) > 4 && $path[4] == "comment.mmo") || (count($path) > 6 && $path[6] == "edit.mmo"))
									{
									?>
									<h3 class="PanelTitle">Leave a comment</h3>
									<div class="PanelContent">
										<form action="/community/groups/<?php echo($thisgroup->Name); ?>/topics/<?php echo($topic->Name); ?>/comment.mmo" method="POST">
											<table style="width: 100%">
												<tr>
													<td colspan="2">
														<input name="comment_title" type="text" placeholder="Title (optional)" style="width: 100%" />
													</td>
												</tr>
												<tr>
													<td><textarea name="comment_content" style="width: 500px" rows="5"></textarea></td>
													<td style="width: 100%">
														<fieldset>
															<legend>Attachments</legend>
															<div class="ListBox">
															<? /*	links go here like so...
																<a href="#" class="ListItem">
																	<span class="ListItemTitle">test.png</span>
																</a>
															*/ ?>
															</div>
															<hr />
															<div style="text-align: right;">
																<a href="#">Add...</a>
															</div>
														</fieldset>
													</td>
												</tr>
												<tr>
													<td colspan="2" style="text-align: right;">
														<input type="submit" value="Leave Comment" />
														<a class="Button" href="/community/groups/<?php echo($thisgroup->Name); ?>/topics/<?php echo($topic->Name); ?>">Cancel</a>
													</td>
												</tr>
											</table>
										</form>
									</div>
									<?php
									}
									else
									{
									?>
									<h3 class="PanelTitle">Comments (<?php echo($topic->CountComments()); ?>) <a class="PanelTitleMini" href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $thisgroup->Name . "/topics/" . $topic->Name . "/comment.mmo")); ?>">Leave a comment</a></h3>
									<div class="PanelContent">
										<table class="CommentListBox" style="width: 100%">
						<?php
									$comments = $topic->GetComments(5);
									foreach ($comments as $comment)
									{
										$comment->Render("~/community/groups/WelcomeToPsychatica/topics/welcome/comments");
									}
						?>
										</table>
									</div>
						<?php
								}
						?>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
	$page->EndContent();
?>