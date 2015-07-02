<?php
	if (!($thisgroup->HasMember($CurrentUser)))
	{
		System::Redirect("~/community/groups/" . $thisgroup->Name);
		return;
	}
	
	if ($_POST["attempt"] != null && $_POST["topic_name"] != null)
	{
		$validate_topic_name = GroupTopic::ValidateName($_POST["topic_name"]);
	}
	if ($validate_topic_name == null && $_POST["attempt"] != null && $_POST["topic_name"] != null && $_POST["topic_title"] != null)
	{
		$result = GroupTopic::Create($thisgroup, $_POST["topic_name"], $_POST["topic_title"], $_POST["topic_description"]);
		
		if (!$result)
		{
			$page = new PsychaticaErrorPage();
			$page->ErrorCode = mysql_errno();
			$page->ErrorDescription = mysql_error();
			$page->ReturnButtonURL = "~/community/groups/" . $thisgroup->Name . "/topics/create.mmo";
			$page->ReturnButtonText = "Return to Create Topic";
			$page->Render();
			return;
		}
		
		System::Redirect("~/community/groups/" . $thisgroup->Name . "/topics/" . $_POST["topic_name"]);
		return;
	}
	$page = new PsychaticaWebPage("Create Topic | " . $thisgroup->Title);
	$page->BeginContent();
?>
<div class="Panel">
	<h3 class="PanelTitle">Topic Properties</h3>
	<div class="PanelContent">
		<form action="create.mmo" method="POST">
			<p>
				Topic name and title must be 50 characters or less. Name must be alphanumeric
				characters only with no spaces.
			</p>
			<input type="hidden" name="attempt" value="1" />
			<table style="margin-left: auto; margin-right: auto;">
				<tr>
					<td><label for="txtName">Topic <u>n</u>ame (for URLs):</label></td>
					<td><input type="text" id="txtName" name="topic_name" accesskey="n" /></td>
				</tr>
				<?php
				if ($_POST["attempt"] != null && $_POST["topic_name"] == "")
				{
				?>
				<tr>
					<td colspan="2" class="InlineError">You must enter a topic name.</td>
				</tr>
				<?php
				}
				else if ($validate_topic_name != null)
				{
				?>
				<tr>
					<td colspan="2" class="InlineError"><?php echo($validate_topic_name); ?></td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td><label for="txtTitle">Topic <u>t</u>itle (for display):</label></td>
					<td><input type="text" id="txtTitle" name="topic_title" accesskey="t" /></td>
				</tr>
				<?php
				if ($_POST["attempt"] != null && $_POST["topic_title"] == "")
				{
				?>
				<tr>
					<td colspan="2" class="InlineError">You must enter a topic title.</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td><label for="txtDescription">Topic <u>d</u>escription:</label></td>
					<td><textarea id="txtDescription" name="topic_description" accesskey="d"><?php if ($_POST["topic_description"] != null) echo($topic_description); ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: right;">
						<input type="submit" value="Save Changes" />
						<a class="Button" href="/community/groups/<?php echo($thisgroup->Name); ?>">Cancel</a>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php
	$page->EndContent();
?>