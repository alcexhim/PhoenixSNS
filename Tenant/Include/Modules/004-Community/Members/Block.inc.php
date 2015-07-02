<?php
	if ($CurrentUser == null)
	{
		$page = new PsychaticaErrorPage();
		$page->Message = "You must be logged in to use this feature.";
		$page->Render();
		return;
	}
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		global $MySQL;
		$thisuser->Block($_POST["block_reason"], ($_POST["block_reason_hide"] == "1"));
		if ($_POST["report"] == "1")
		{
			$thisuser->Report();
		}
		
		if ($MySQL->errno != 0)
		{
			$page = new PsychaticaErrorPage();
			$page->ErrorCode = mysql_errno();
			$page->ErrorDescription = mysql_error();
			$page->Render();
			return;
		}
		System::Redirect("~/community/members/" . $thisuser->ShortName);
	}
	else
	{
		$page = new PsychaticaWebPage();
		$page->Title = "Block User";
		$page->BeginContent();
?>
<div class="Panel">
	<h3 class="PanelTitle">Block <?php echo($thisuser->LongName); ?></h3>
	<div class="PanelContent">
		<form action="block" method="POST">
			<p>
				Are you sure you wish to block <?php echo($thisuser->LongName); ?>? If you do this, this person will be
				removed from your Friends List and you will be unable to see their profile. They will be invisible in
				the World and will not be able to message you. <em>Please note that, from their point of view, the same
				thing will happen with you.</em>
			</p>
			<p>
				<table style="margin-left: auto; margin-right: auto; width: 75%;">
					<tr>
						<td style="width: 128px;">Reason blocked:</td>
						<td><input type="text" name="block_reason" id="txtBlockReason" style="width: 100%" /></td>
					</tr>
					<tr>
						<td colspan="2">
							<input id="chkBlockReasonHide" type="checkbox" name="block_reason_hide" value="1" />
							<label for="chkBlockReasonHide">Hide the reason from the user</label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input id="chkReport" type="checkbox" name="report" value="1" checked="false" />
							<label for="chkReport">Report this user for a violation</label>
						</td>
					</tr>
				</table>
			</p>
			<p style="text-align: center;">
				<input type="submit" value="Block User" />
				<a class="Button" href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($thisuser->ShortName); ?>">Cancel</a>
			</p>
		</form>
	</div>
</div>
<?php
		$page->EndContent();
		return;
	}
?>