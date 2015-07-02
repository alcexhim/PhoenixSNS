<?php
	namespace PhoenixSNS\Pages;
	
	use WebFX\System;
	use WebFX\WebStyleSheet;
	
	use WebFX\Controls\Window;
	
	use PhoenixSNS\MasterPages\WebPage;
	use PhoenixSNS\Objects\LanguageString;
	
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantQueryParameter;
	
	class MainPage extends WebPage
	{
		public function __construct()
		{
			parent::__construct();
			$this->DisplayChrome = false;
		}
		protected function RenderContent()
		{
			$CurrentTenant = Tenant::GetCurrent();
			
			$objPage = $CurrentTenant->GetObject("Page");
			$instGuestMainPage = $objPage->GetInstance(array
			(
				new TenantQueryParameter("Name", "GuestMainPage")
			));
			
			$sects = $instGuestMainPage->GetPropertyValue("Sections")->GetInstances();
			foreach ($sects as $sect)
			{
				echo("<div class=\"PageSection_" . $sect->GetPropertyValue("Name") . "\">");
				eval("?>" . $sect->GetPropertyValue("Content"));
				echo("</div>");
			}
			
			/*
			$posts = DashboardPost::Get();
			if (count($posts) > 0)
			{
				foreach ($posts as $post)
				{
					$posttype = $post->Type;
					$posticon = null;
					$postclass = null;
					$actions = $post->GetActions();
					if ($posttype != null)
					{
						$posticon = $posttype->IconName;
						$postclass = $posttype->CssClass;
					}
					?>
					<div class="Card<?php if ($postclass != null) echo(" " . $postclass); ?>">
						<div class="Title"><?php if ($posticon != null) echo("<i class=\"fa fa-" . $posticon . "\"></i>"); ?> <span class="Text"><?php echo($post->Title); ?></span></div>
						<div class="Content"><?php echo($post->Content); ?></div>
						<div class="Actions Horizontal">
						<?php
						if (count($actions) > 0)
						{
						?><div class="Group"><?php
							foreach($actions as $action)
							{
								echo("<a href=\"");
								if ($action->URL != null)
								{
									echo(System::ExpandRelativePath($action->URL));
								}
								else
								{
									echo("#");
								}
								echo("\"");
								if ($action->TargetName != null)
								{
									echo(" target=\"" . $action->TargetName . "\"");
								}
								if ($action->Script != null)
								{
									echo(" onclick=\"" . $action->Script . "; return false;\"");
								}
								if ($action->IconName != null)
								{
									echo("<i class=\"fa fa-" . $action->IconName . "\"></i> ");
								}
								echo("<span class=\"Text\">" . $action->Title . "</span>");
								echo("</a>");
							}
						?></div><?php
						}
						?>
						<div class="Group">
						<?php
							if ($post->AllowLike)
							{
						?>
						<a href="#" class="LikeButton"><i class="fa fa-thumbs-up"></i> <span class="Text"><?php echo(LanguageString::GetByName("like")); ?></span></a>
						<?php
							}
							if ($post->AllowComment)
							{
						?>
						<a href="#" class="LikeButton"><i class="fa fa-comment"></i> <span class="Text"><?php echo(LanguageString::GetByName("comment")); ?></span></a>
						<?php
							}
							if ($post->AllowShare)
							{
						?>
						<a href="#" class="LikeButton"><i class="fa fa-share"></i> <span class="Text"><?php echo(LanguageString::GetByName("share")); ?></span></a>
						<?php
							}
						?>
						</div>
						</div>
					</div>
					<?php
				}
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
			*/
		/*
		?>
			<div class="Card Official">
				<div class="Title"><i class="fa fa-info"></i> <span class="Text">A few words about the new <?php echo(System::GetConfigurationValue("Application.Name")); ?> design</span></div>
				<div class="Content">
					<ul>
						<li>
							The new design is based around the concept of &quot;cards&quot;, which can update automatically over time and present
							you with only the most important information and an intuitive way to act upon it.
						</li>
						<li>
							The major issue with Journal creation has been fixed, and the Market now has some items for you to buy. Enjoy the new
							fashion items in the <a href="<?php echo(System::ExpandRelativePath("~/market")); ?>" target="_blank"><?php echo(System::GetConfigurationValue("Application.Name")); ?> Market</a>!
						</li>
						<li>
							The terminology &quot;User name&quot; for the private credential associated with your account has been changed to
							&quot;User ID&quot; to avoid confusion with the publicly-visible Short and Long names so that people are not encouraged
							to use the same User ID as their Short or Long name. It will eventually be made impossible to create a user account with
							the same User ID and Short/Long name.
						</li>
						<li>
							The World button on the navigation bar now only displays when you are logged in. This is to avoid confusing users who
							don't realize that they must be logged in to use the World.
						</li>
					</ul>
				</div>
				<div class="Actions Horizontal">
					<a href="#"><i class="fa fa-thumbs-up"></i> <span class="Text"><?php echo(LanguageString::GetByName("like")); ?></a>
					<a href="#"><i class="fa fa-comment"></i> <span class="Text"><?php echo(LanguageString::GetByName("comment")); ?></a>
					<a href="#"><i class="fa fa-download"></i> <span class="Text"><?php echo(LanguageString::GetByName("download")); ?></a>
				</div>
			</div>
		<div class="CardSet">
			<div class="Card Official">
				<div class="Title"><i class="fa fa-picture-o"></i> <span class="Text">New wallpaper available</span></div>
				<div class="Content">
					<?php
						$img = "~/images/wallpaper/bg1.jpg";
					?>
					<a style="text-align: center; display: block;" href="<?php echo(System::ExpandRelativePath($img)); ?>" target="_blank"><img src="<?php echo(System::ExpandRelativePath($img)); ?>" style="width: 300px; height: auto;" /></a>
					<span class="Description">
						Our Phoenix-human hybrid girl Phelicia loves to go sky-tumbling! She will keep you company on your desktop or laptop PC. Be on the lookout for more designs targeted at mobile devices!
					</span>
					<br /><br />
					Designed by <a href="#">roololoo</a>.
				</div>
				<div class="Actions Horizontal">
					<a href="#"><i class="fa fa-thumbs-up"></i> <span class="Text"><?php echo(LanguageString::GetByName("like")); ?></a>
					<a href="#"><i class="fa fa-comment"></i> <span class="Text"><?php echo(LanguageString::GetByName("comment")); ?></a>
					<a href="#"><i class="fa fa-download"></i> <span class="Text"><?php echo(LanguageString::GetByName("download")); ?></a>
				</div>
			</div>
		</div>
		<div class="CardSet" style="width: 50%;">
			<div class="Card Official">
				<div class="Title"><i class="fa fa-picture-o"></i> <span class="Text">Top 'o th' mornin' to ya!</span></div>
				<div class="Content">
					Come join <?php echo(System::GetConfigurationValue("Application.Name")); ?> for a fun-filled St. Patrick's Day celebration!
				</div>
				<div class="Actions Horizontal">
					<a href="#"><i class="fa fa-thumbs-up"></i> <span class="Text"><?php echo(LanguageString::GetByName("like")); ?></a>
					<a href="#"><i class="fa fa-comment"></i> <span class="Text"><?php echo(LanguageString::GetByName("comment")); ?></a>
					<a href="#"><i class="fa fa-share"></i> <span class="Text"><?php echo(LanguageString::GetByName("share")); ?></a>
				</div>
			</div>
			<div class="Card Sponsored">
				<div class="Title"><i class="fa fa-star"></i> <span class="Text">Ready for the next big thing in virtual concert production?</span></div>
				<div class="Content">
					<a href="http://www.concertroid.com" target="_blank">
						<img class="Advertisement" style="width: 100%;" src="http://www.concertroid.com/images/logo.png" alt="http://www.concertroid.com/" />
					</a>
				</div>
				<div class="Actions Horizontal">
					<a href="#"><i class="fa fa-thumbs-up"></i> <span class="Text">Like Concertroid on <?php echo(System::GetConfigurationValue("Application.Name")); ?></span></a>
					<a href="http://www.concertroid.com/" target="_blank"><i class="fa fa-external-link"></i> <span class="Text">Visit Web Site</span></a>
				</div>
			</div>
		</div>
		<?php
		*/
		}
	}
?>