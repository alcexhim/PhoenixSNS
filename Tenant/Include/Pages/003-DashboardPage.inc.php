<?php
	namespace PhoenixSNS\Pages;
	
	use WebFX\System;
	use WebFX\WebStyleSheet;
	
	use WebFX\Controls\Window;
	
	use PhoenixSNS\MasterPages\WebPage;
	
	use PhoenixSNS\Objects\MarketResourceType;
	use PhoenixSNS\Objects\MarketResourceTransaction;
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\User;
	
	class DashboardPage extends WebPage
	{
		protected function RenderContent()
		{
			$CurrentUser = User::GetCurrent();
			?>
			<div class="CardSet" style="width: 30%">
				<div class="Card">
					<div class="Title"><i class="fa fa-globe"></i><span class="Title">World</span></div>
					<div class="Content">
						<span class="Emphasis">No friends</span> are in the World at the moment
					</div>
					<div class="Actions Vertical">
						<a href="#">Invite a friend to a personal Place</a>
						<a href="#">Explore the World</a>
					</div>
				</div>
				<div class="Card">
					<div class="Title">
						<i class="fa fa-money"></i> <span class="Text">Resources</span>
					</div>
					<div class="Content" style="text-align: center;">
					<?php
						$resources = MarketResourceType::Get();
						foreach ($resources as $resource)
						{
							?>
							<div class="Resource">
								<div class="Icon"><img src="<?php echo(System::ExpandRelativePath("~/images/" . System::GetConfigurationValue("Application.DefaultResourceBundle") . "/Resources/24x24/" . $resource->ID . ".png")); ?>" title="<?php echo($resource->TitlePlural); ?>" /></div>
								<div class="Value"><?php echo(MarketResourceTransaction::GetAmountByUser($CurrentUser, $resource)); ?></div>
							</div>
							<?php
						}
					?>
					</div>
					<div class="Actions Vertical">
						<a href="#">Learn more about earning credits</a>
						<a href="#">Earn interest by putting your credits in the Bank</a>
						<a href="<?php echo(System::ExpandRelativePath("~/contribute/purchase")); ?>">Get more with PsychatiGold</a>
					</div>
				</div>
				<div class="Card">
					<div class="Title">
						Fashion
					</div>
					<div class="Content">
						<span class="Emphasis">No new items</span> available in the Market
					</div>
					<div class="Actions Vertical">
						<a href="#"><i class="fa fa-shopping-cart"></i> <span class="Text">Visit the Market to buy and sell items</span></a>
					</div>
				</div>
			</div>
			<div class="CardSet" style="width: 30%">
				<?php /*
				<div class="Card">
					<div class="Title"><i class="fa fa-globe"></i><span class="Title">Next scheduled maintenance</span></div>
					<div class="Content">
					<?php
						$et = Tenant::GetCurrent()->EndTimestamp;
						if ($et == null)
						{
							?><span class="Emphasis">To be determined</span><?php
						}
						else
						{
							?><span class="Emphasis"><?php echo($et); ?></span><?php
						}
					?>
					</div>
				</div>
				*/ ?>
				<div class="Card">
					<div class="Title"><i class="fa fa-information"></i><span class="Title">Fight the evil horde!</span></div>
					<div class="Content">
						<p>An invasion is taking place! Gather your wits and tactics to beat the enemy!</p>
						<p>14 friends play this</p>
					</div>
					<div class="Actions Vertical">
						<a href="#">Play War of Ages</a>
					</div>
				</div>
			</div>
			<div class="CardSet" style="width: 30%">
				<div class="Card">
					<div class="Title"><i class="fa fa-globe"></i><span class="Title"><a href="#">alcexhim</a> invited you to his Place!</span></div>
					<div class="Content">
						<span class="Emphasis">My Personal Room</span>
					</div>
					<div class="Actions Vertical">
						<a href="#">Go to alcexhim's Place</a>
					</div>
				</div>
			</div>
			<?php
			/*
			$stories = DashboardStory::Get();
			if (count($stories) > 0)
			{
				foreach ($stories as $story)
				{
					$card = new Card('card' . $story->ID, $story->Title, $story->Content, $story->ClassName, $story->IconName);
					$card->Actions = array
					(
						new CardAction(LanguageString::GetByName("unlike"), "#", null, 'thumbs-o-up', '_blank', 4),
						new CardAction(LanguageString::GetByName("comment"), null, null, "comment", "_blank"),
						new CardAction(LanguageString::GetByName("share"), null, null, "share", "_blank")
					);
					$card->Render();
				}
			}
			else
			{
				?><p>There's nothing here!</p><?php
			}
			*/
			
			/*
			(new Card('card1', '<a href="#">alcexhim</a> posted a video <span class="Location"> at <a href="/world/town-square">Town Square</a>',
			'Just chillin\' with the peeps.',
			null, "video-camera",
			array
			(
				new CardAction(LanguageString::GetByName("like"), "#", null, "thumbs-up", null),
			)))->Render();
			
			(new Card('card2', "Ready for the next big thing in virtual concert production?",
			'<a href="http://www.concertroid.com" target="_blank"><img class="Advertisement" style="width: 100%;" src="http://www.concertroid.com/images/logo.png" alt="http://www.concertroid.com/" /></a>',
			"Sponsored", "star",
			array
			(
				new CardAction("Like Concertroid on Psychatica", "#", null, 'thumbs-up', '_blank'),
				new CardAction("Visit Web Site", "http://www.concertroid.com", null, "external-link", "_blank")
			)))->Render();
			
			(new Card('card3', "Psychatica design updated",
			'The new design is based around the concept of &quot;cards&quot;, which can update automatically
			over time and present you with only the most important information and an intuitive way to act
			upon it.',
			"Official", "info",
			array
			(
				new CardAction(LanguageString::GetByName("unlike"), "#", null, 'thumbs-o-up', '_blank', 4),
				new CardAction(LanguageString::GetByName("comment"), null, null, "comment", "_blank"),
				new CardAction(LanguageString::GetByName("share"), null, null, "share", "_blank")
			)))->Render();
			
			(new Card('card4', "Psychatica fixes and updates",
			'The major issue with Journal creation has been fixed, and the
			Market now has some items for you to buy. Enjoy the new fashion
			items in the <a href="' . System::ExpandRelativePath("~/market") . '">Psychatica Market</a>!',
			"Official", "info",
			array
			(
				new CardAction(LanguageString::GetByName("like"), "#", null, 'thumbs-up', '_blank', 1),
				new CardAction(LanguageString::GetByName("comment"), null, null, "comment", "_blank"),
				new CardAction(LanguageString::GetByName("share"), null, null, "share", "_blank")
			)))->Render();
			
			(new Card('card4', "Psychatica updates",
			'The terminology &quot;User name&quot; for the private credential associated with your account has been changed to
			&quot;User ID&quot; to avoid confusion with the publicly-visible Short and Long names so that people are not encouraged
			to use the same User ID as their Short or Long name. It will eventually be made impossible to create a user account
			with the same User ID and Short/Long name.',
			"Official", "info",
			array
			(
				new CardAction(LanguageString::GetByName("like"), "#", null, 'thumbs-up', '_blank', 1),
				new CardAction(LanguageString::GetByName("comment"), null, null, "comment", "_blank"),
				new CardAction(LanguageString::GetByName("share"), null, null, "share", "_blank")
			)))->Render();
			
			(new Card('card4', "Psychatica updates",
			'The World button on the navigation bar now only displays when you are logged in. This is to avoid confusing users who don\'t
			realize that they must be logged in to use the World.',
			"Official", "info",
			array
			(
				new CardAction(LanguageString::GetByName("like"), "#", null, 'thumbs-up', '_blank', 1),
				new CardAction(LanguageString::GetByName("comment"), null, null, "comment", "_blank"),
				new CardAction(LanguageString::GetByName("share"), null, null, "share", "_blank")
			)))->Render();
			*/
	/*
	<div class="Card">
		<div class="Title"><span class="Text"><?php echo(LanguageString::GetByName("currentevents")); ?></span></div>
		<div class="Content">
			<div class="ListBox">
				<?php
				$events = Event::Get();
				foreach ($events as $event)
				{
				?>
				<a href="<?php echo(\System::ExpandRelativePath("~/Events/" . $event->Name)); ?>">
					<div class="ListItemTitle">
						<?php
							echo("<span class=\"EventType\" style=\"color: " . $event->Type->Color . "; border-color: " . $event->Type->Color . ";\">" . $event->Type->Title . "</span> ");
							echo($event->Title);
							echo("<span class=\"EventDate\">" . $event->BeginDate . " &mdash; " . $event->EndDate . "</span>");
						?>
					</div>
					<div class="ListItemDescription">
						<?php
							echo($event->Description);
						?>
					</div>
				</a>
				<?php
				}
				?>
			</div>
		</div>
	</div>
	*/
			/*
			$img = "~/images/wallpaper/bg1.jpg";
			(new Card('card4', "New wallpaper available",
			'<a style="text-align: center; display: block;" href="' . System::ExpandRelativePath($img) . '" target="_blank"><img src="' . System::ExpandRelativePath($img) . '" style="width: 300px; height: auto;" /></a>
			<span class="Description">
				Our Phoenix-human hybrid girl Phelicia loves to go sky-tumbling! She will keep you company on your desktop or laptop PC. Be on the lookout for more designs targeted at mobile devices!
			</span><br /><br />Designed by <a href="#">roololoo</a>',
			"Official", "picture-o",
			array
			(
				new CardAction(LanguageString::GetByName("like"), "#", null, 'thumbs-up', '_blank', 1),
				new CardAction(LanguageString::GetByName("comment"), null, null, "comment", "_blank"),
				new CardAction(LanguageString::GetByName("download"), null, null, "download", "_blank")
			)))->Render();
			*/
			
			/*
			<tr>
				<?php
				if (\System::$Configuration["Questions.Enabled"])
				{
				?>
				<td style="width: 50%; vertical-align: top;">
					<div class="Panel">
						<h3 class="PanelTitle"><?php echo(LanguageString::GetByName("questions")); ?></h3>
						<div class="PanelContent">
						<?php
							$questions = Question::Enumerate();
							if (count($questions) == 0)
							{
						?>
						<p>No questionnaires yet, why not <a href="/questions/create.mmo">create one</a>!</p>
						<?php
							}
						?>
						</div>
					</div>
				</td>
				<?php
				}
				if (\System::$Configuration["Suggestions.Enabled"])
				{
				?>
				<td style="vertical-align: top;">
					<div class="Panel">
						<h3 class="PanelTitle"><?php echo(LanguageString::GetByName("suggestions")); ?></h3>
						<div class="PanelContent">
							<p>
								Help us improve and make the site even more awesome! Tell us what kind of features, games,
								items, and other ideas you'd like to see in Psychatica! If we use your ideas, you might
								win prizes!
							</p>
							<?php
							if ($CurrentUser == null)
							{
							?>
							<p>
								If you are interested in contributing, please first log into Psychatica by the form on the
								right panel.
							</p>
							<?php
							}
							else
							{
								$form = new WebForm("modules/suggestionBoxReceive.php", "POST");
								$form->Begin();
								
								$cboSuggestionTypeID = new WebDropDownList("suggestion_type_id");
								$cboSuggestionTypeID->Width = "100%";
								
								$query = "SELECT * FROM phpmmo_suggestion_types";
								$result = mysql_query($query);
								$count = mysql_num_rows($result);
								for ($i = 0; $i < $count; $i++)
								{
									$values = mysql_fetch_assoc($result);
									$cboSuggestionTypeID->AddItem($values["suggestion_type_id"], $values["suggestion_type_title"]);
								}
								
								$cboSuggestionTypeID->Render();
								
								$txtSuggestionContent = new WebTextBox("suggestion_content");
								$txtSuggestionContent->RowCount = 3;
								$txtSuggestionContent->Width = "100%";
								$txtSuggestionContent->Multiline = true;
								$txtSuggestionContent->Render();
								
								$cmdSubmit = new WebButton();
								$cmdSubmit->Text = "Submit Request";
								$cmdSubmit->HorizontalAlign = "right";
								$cmdSubmit->Render();
								
								$form->End();
							}
							?>
						</div>
					</div>
				</td>
				<?php
				}
				?>
			</tr>
			*/
		}
	}
?>