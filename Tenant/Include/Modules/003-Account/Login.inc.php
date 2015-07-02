<?php
	$page = new PsychaticaWebPage("Login");
	$page->BeginContent();
?>
<form method="POST">
	<h1>Login to <?php echo(System::$Configuration["Application.Name"]); ?></h1>
	<table style="width: 40%; font-family: sans-serif; font-size: 10pt; margin-left: auto; margin-right: auto;">
		<tr>
			<td style="width: 80px; vertical-align: middle;">User ID:</td>
			<td><input type="text" name="member_username" style="font-size: 14pt; width: 100%;" id="txtUserName"<?php
				if ($_POST["member_username"] != null)
				{
					echo (" value=\"" . $_POST["member_username"] . "\"");
				}
				else if ($_GET["member_username"] != null)
				{
					echo (" value=\"" . $_GET["member_username"] . "\"");
				}
			?> /></td>
		</tr>
		<tr>
			<td style="vertical-align: middle;">Password:</td>
			<td><input type="password" name="member_password" style="font-size: 14pt; width: 100%;" id="txtPassword" /></td>
		</tr>
		<tr id="trScenarioStatic" style="display: none">
			<td>Scenario:</td>
			<td>
				<select>
					<?php
						
					?>
					<option>The Magister's Castle</option>
				</select>
			</td>
		</tr>
		<tr style="display: none" id="trScenarioDynamic">
			<td style="vertical-align: top;">Scenario:</td>
			<td>
				<div class="DropDown">
					<div class="DropDownValue"><a href="#">The Magister's Castle <span class="DropDownButton">v</span></div>
				</div>
				<div class="ListBox DropDownListBox">
					<a href="#">
						<div class="ListItemTitle">The Magister's Castle</div>
						<div class="ListItemDescription">Come explore the castle! With new places to explore and new quests to undertake every month, you will never be bored!</div>
					</a>
				</div>
			</td>
		</tr>
		<?php
			if ($resetRequired && !$loginFailed)
			{
		?>
		<tr>
			<td colspan="2" style="color: #FF0000;">
				Your password has expired and must be reset. Please enter a new password.
				<input type="hidden" name="resetComplete" value="reset" />
			</td>
		</tr>
		<?php
			}
			else if ($loginFailed)
			{
				if ($resetRequired || ($usercompare != null && $usercompare->ConsecutiveLoginAttempts > 2))
				{
		?>
		<tr>
			<td colspan="2" style="color: #FF0000;">
				You have entered the incorrect password more than 3 times.  For security,
				please contact a Psychatica staff member to have your account reset.
			</td>
		</tr>
		<?php
				}
				else
				{
		?>
		<tr>
			<td colspan="2" style="color: #FF0000;">
				Login failed! Please check that you entered the correct information.
			</td>
		</tr>
		<?php
				}
			}
		?>
		<tr>
			<td colspan="2" style="text-align: right;">
				<input type="submit" value="Sign In" />
			</td>
		</tr>
	</table>
	<script type="text/javascript">
		// document.getElementById("trScenarioStatic").style.display = "none";
		// document.getElementById("trScenarioDynamic").style.display = "table-row";
	</script>
</form>
<?php
	$page->EndContent();
?>