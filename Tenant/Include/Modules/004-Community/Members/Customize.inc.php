<form action="<?php echo(\System::ExpandRelativePath("~/community/members/" . $id . "/customize/")); ?>" method="POST">
	<div class="Panel">	
		<h3 class="PanelTitle">Profile Settings</h3>
		<div class="Panel">
			<div class="TabContainer TabContainerVertical">
				<div class="TabContainerTabArea">
					<a id="aContent" class="TabContainerTab Selected" href="#" onclick="SwitchTab('tabContent', 'aContent');">Content</a>
					<a id="aAppearance" class="TabContainerTab" href="#" onclick="SwitchTab('tabAppearance', 'aAppearance');">Appearance</a>
				</div>
				<div class="TabContainerContentArea">
					<div class="TabContainerContent" id="tabContent" style="display: block;">
						<?php /*
						<label for="txtAbout" style="display: block;">About me:</label>
						*/ ?>
						<textarea id="txtAbout" name="pc" rows="5" style="width: 100%"><?php echo($thisuser->ProfileContent->Description); ?></textarea>
						<?php /*
						<table style="width: 100%">
							<tr>
								<td style="width: 50%">
									<label for="txtLikes" style="display: block;">Stuff I like:</label>
									<textarea id="txtLikes" name="likes" rows="3" style="width: 100%"><?php echo($thisuser->ProfileContent->Likes); ?></textarea>
								</td>
								<td style="width: 50%">
									<label for="txtLikes" style="display: block;">Stuff I dislike:</label>
									<textarea id="txtLikes" name="dislikes" rows="3" style="width: 100%"><?php echo($thisuser->ProfileContent->Dislikes); ?></textarea>
								</td>
							</tr>
						</table>
						*/ ?>
					</div>
					<div class="TabContainerContent" id="tabAppearance">
						<script type="text/javascript">
							function updateAppearancePanel()
							{
								var optAppearanceBuild = document.getElementById("optAppearanceBuild");
								var optAppearanceUpload = document.getElementById("optAppearanceUpload");
								var optAppearanceCustom = document.getElementById("optAppearanceCustom");
								
								if (optAppearanceBuild.checked)
								{
									appearanceBuild.style.display = "block";
									appearanceUpload.style.display = "none";
									appearanceCustom.style.display = "none";
								}
								else if (optAppearanceUpload.checked)
								{
									appearanceBuild.style.display = "none";
									appearanceUpload.style.display = "block";
									appearanceCustom.style.display = "none";
								}
								else if (optAppearanceCustom.checked)
								{
									appearanceBuild.style.display = "none";
									appearanceUpload.style.display = "none";
									appearanceCustom.style.display = "block";
								}
							}
						</script>
						<table style="width: 100%">
							<tr>
								<td style="width: 24px;"><input type="radio" name="appearance" value="build" id="optAppearanceBuild" onchange="updateAppearancePanel();" checked="checked" /></td>
								<td><label for="optAppearanceBuild" style="display: block;">Help me build a layout</label></td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="appearanceBuild" style="display: block;">
										<table style="width: 100%">
											<tr>
												<td style="width: 25%">Page background color:</td>
												<td>
													<div class="ColorPicker">
														<div class="DropDownList" style="width: 96px">
															<div class="DropDownListItemContent">
																<a href="#">
																	<span class="DropDownListItemIcon" style="width: 24px; height: 24px; background-color: #FFFFFF; display: inline-block;">&nbsp;</span>
																	<span class="DropDownListItemText">White</span>
																</a>
															</div>
															<div class="DropDownListItemContainer">
																<a href="#">
																	<span class="DropDownListItemIcon" style="width: 24px; height: 24px; background-color: #FF0000; display: inline-block;">&nbsp;</span>
																	<span class="DropDownListItemText">Red</span>
																</a>
																<a href="#">
																	<span class="DropDownListItemIcon" style="width: 24px; height: 24px; background-color: #00FF00; display: inline-block;">&nbsp;</span>
																	<span class="DropDownListItemText">Green</span>
																</a>
																<a href="#">
																	<span class="DropDownListItemIcon" style="width: 24px; height: 24px; background-color: #0000FF; display: inline-block;">&nbsp;</span>
																	<span class="DropDownListItemText">Blue</span>
																</a>
															</div>
														</div>
													</div>
												<td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td><input type="radio" name="appearance" value="upload" id="optAppearanceUpload" onchange="updateAppearancePanel();" /></td>
								<td><label for="optAppearanceUpload" style="display: block;">Upload a style sheet</label></td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="appearanceUpload" style="display: none;">
										<label for="txtStyleSheetFileName">Style sheet file<u>n</u>ame:</label> <input type="file" name="stylesheet_filename" id="txtStyleSheetFileName" accesskey="n" />
									</div>
								</td>
							</tr>
							<tr>
								<td><input type="radio" name="appearance" value="custom" id="optAppearanceCustom" onchange="updateAppearancePanel();" /></td>
								<td><label for="optAppearanceCustom" style="display: block;">Edit CSS directly</label></td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="appearanceCustom" style="display: none;">
										<textarea>body { }</textarea>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="PanelButtons" style="text-align: right;">
			<input type="submit" value="Save Changes" />
			<a class="Button" href="/community/members/<?php echo($thisuser->ShortName); ?>/">Cancel Editing</a>
		</div>
	</div>
</form>