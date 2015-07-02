<?php
	namespace PhoenixSNS\Modules\Community\Pages;
	
	use PhoenixSNS\Objects\Page;
	
	use PhoenixSNS\Modules\Community\CommunityPage;
	use WebFX\Controls\BreadcrumbItem;
	
	use PsychaticaWebPage;
	
	$id = $path[1];
	$thispage = null;
	
	if (is_numeric($id))
	{
		$thispage = Page::GetByID($id);
	}
	else
	{
		$thispage = Page::GetByName($id);
	}
	
	if ($thispage == null)
	{
		$page = new PsychaticaErrorPage("Page not found");
		$page->Message = "That page does not exist in the system. It may have been deleted, or you may have typed the name incorrectly.";
		$page->ReturnButtonURL = "~/community/pages";
		$page->ReturnButtonText = "Return to Page List";
		$page->Render();
		return;
	}
	
	switch ($path[2])
	{
		case "images":
		{
			switch ($path[3])
			{
				case "avatar":
				{
					switch ($path[4])
					{
						case "thumbnail.png":
						{
							// paths are relative to the path of the including file
							header("Content-Type: image");
							$fileName = "images/icons/page.png";
							// header("Content-Type: " . mime_content_type($fileName));
							if (file_exists($fileName))
							{
								readfile($fileName);
							}
							return;
						}
					}
					break;
				}
			}
			break;
		}
		case "connect":
		{
			$thispage->AddMember($CurrentUser);
			System::Redirect("~/community/pages/" . $thispage->Name);
			return;
		}
		case "disconnect":
		{
			$thispage->RemoveMember($CurrentUser);
			System::Redirect("~/community/pages/" . $thispage->Name);
			return;
		}
		default:
		{
			$page = new PsychaticaWebPage($thispage->Title);
			$page->BeginContent();
			
			$actions = $thispage->GetActions();
			?>
			<div class="Panel">
				<h3 class="PanelTitle"><?php echo($thispage->Title); ?></h3>
				<div class="PanelContent">
					<div class="ProfilePage">
						<div class="ProfileTitle">
							<span class="ProfileUserName"><?php
								$count = $thispage->CountMembers();
								if ($count == 0)
								{
									echo("Nobody currently likes ");
								}
								else
								{
									echo($count);
									if ($count != 1)
									{
										echo(" people like ");
									}
									else
									{
										echo(" person likes ");
									}
								}
								echo("this");
							?></span>
							<span class="ProfileControlBox">
								<?php
									if ($thispage->HasMember($CurrentUser))
									{
									?>
									<a href="/community/pages/<?php echo($thispage->Name); ?>/disconnect">Unlike</a>
									<?php
									}
									else
									{
									?>
									<a href="/community/pages/<?php echo($thispage->Name); ?>/connect">Like <?php echo($thispage->Title); ?></a>
									<?php
									}
								?>
								<a href="#">Send Message</a>
								<?php
									if ($thispage->Creator->ID == $CurrentUser->ID)
									{
										?>
										<a href="/community/pages/<?php echo($thispage->Name); ?>/manage.mmo">Manage Page</a>
										<?php
									}
								?>
							</span>
						</div>
						<div class="ProfileContent">
							<table style="width: 100%">
								<tr>
									<td style="width: 25%">
										<div class="ActionList">
											<?php if ($path[2] == "") { ?>
											<span class="Selected">Information</span>
											<?php } else { ?>
											<a href="/community/pages/<?php echo($thispage->Name); ?>">Information</a>
											<?php } ?>
											
											<?php if ($path[2] == "posts") { ?>
											<span class="Selected">Posts by Page</span>
											<?php } else { ?>
											<a href="/community/pages/<?php echo($thispage->Name); ?>/posts">Posts by Page</a>
											<?php } ?>
											
											<?php if ($path[2] == "fans") { ?>
											<span class="Selected">Fans of this Page</span>
											<?php } else { ?>
											<a href="/community/pages/<?php echo($thispage->Name); ?>/fans">Fans of this Page</a>
											<?php } ?>
											
											<?php
											if (count($actions) > 0)
											{
											?>
												<hr />
												<?php
												foreach ($actions as $action)
												{
													?><a class="External" href="<?php echo($action->URL); ?>"><?php echo($action->Title); ?></a><?php
												}
												?>
											<?php
											}
											?>
										</div>
									</td>
									<td>
										<?php
										switch ($path[2])
										{
											case "posts":
											{
												?>
												<div class="Panel">
													<h3 class="PanelTitle">Posts by <?php echo($thispage->Title); ?></h3>
													<div class="PanelContent"><?php echo($thispage->Description); ?></div>
												</div>
												<?php
												break;
											}
											case "fans":
											{
												?>
												<div class="Panel">
													<h3 class="PanelTitle">Fans of <?php echo($thispage->Title); ?></h3>
													<div class="PanelContent">
														<div class="ButtonGroup ButtonGroupHorizontal">
															<?php
																$fans = $thispage->GetMembers();
																foreach ($fans as $fan)
																{
																	?>
																	<a class="ButtonGroupButton" href="/community/members/<?php echo($fan->ShortName); ?>" target="_blank">
																		<img class="ButtonGroupButtonImage" src="/community/members/<?php echo($fan->ShortName); ?>/images/avatar/thumbnail.png" />
																		<span class="ButtonGroupButtonText"><?php echo($fan->LongName); ?></span>
																	</a>
																	<?php
																}
															?>
														</div>
													</div>
												</div>
												<?php
												break;
											}
											default:
											{
												?>
												<div class="Panel">
													<h3 class="PanelTitle">About <?php echo($thispage->Title); ?></h3>
													<div class="PanelContent"><?php echo($thispage->Description); ?></div>
												</div>
												<?php
												break;
											}
										}
										?>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<?php
			$page->EndContent();
			return;
		}
	}
?>