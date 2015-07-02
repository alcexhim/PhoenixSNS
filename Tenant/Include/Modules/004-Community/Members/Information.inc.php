<div class="TabContainerContent" style="display: block;">
<?php
	/*
	<div class="ProfileHeader">
		<?php
		if ($action == "customize")
		{
		?>
			<fieldset>
				<legend>Header image</legend>
				<input type="radio" id="optHeaderTheme" selected="selected" /><label for="optHeaderTheme">Use from theme</label>
				<input type="radio" id="optHeaderFile" /><label for="optHeaderFile">Upload file</label>
				<div id="divHeaderTheme">
					<label for="cboHeaderTheme">Theme name:</label>
					<select id="cboHeaderTheme">
						<option value="">(none)</option>
					<?php
						$themes = mmo_get_themes();
						foreach ($themes as $theme)
						{
					?>
						<option value="<?php echo($theme->Name); ?>"><?php echo($theme->Title); ?></option>
					<?php
						}
					?>
					</select>
				</div>
				<div id="divFileTheme" style="display: none">
					<label for="txtHeaderFileName">File name:</label>
					<input type="file" id="txtHeaderFileName" />
				</div>
			</fieldset>
		<?php
		}
		else
		{
		?>
		<img src="<?php echo(System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/images/header")); ?>" />
		<?php
		}
		?>
	</div>
	*/
?>
	<div class="ProfileUserContentContainer">
		<div class="ProfileUserContent">
			<?php echo($thisuser->ProfileContent->Description); ?>
		</div>
	</div>
</div>