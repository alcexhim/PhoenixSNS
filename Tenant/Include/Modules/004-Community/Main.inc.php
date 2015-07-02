<?php
	namespace PhoenixSNS\Modules\Community;
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use WebFX\Controls\BreadcrumbItem;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	use WebFX\Controls\ButtonGroupButtonAlignment;
	
	use WebFX\Controls\TabStrip;
	use WebFX\Controls\TabStripTab;
	use WebFX\Controls\TabContainerTabPosition;
	
	use WebFX\Controls\Panel;
	
	use PhoenixSNS\Objects\User;
	use PhoenixSNS\Objects\Group;
	use PhoenixSNS\Objects\Page;
	
	use PhoenixSNS\MasterPages\WebPage;
	
	class CommunityPage extends WebPage
	{
		public $Name;
		
		protected function Initialize()
		{
			parent::Initialize();
			
			$path = System::GetVirtualPath();
			$pagename = $path[1];
			switch ($pagename)
			{
				case "members":
				{
					$this->BreadcrumbItems = array
					(
						new BreadcrumbItem("~/community", "Community"),
						new BreadcrumbItem("~/community/members", "Members")
					);
					break;
				}
				case "groups":
				{
					$this->BreadcrumbItems = array
					(
						new BreadcrumbItem("~/community", "Community"),
						new BreadcrumbItem("~/community/groups", "Groups")
					);
					break;
				}
				case "pages":
				{
					$this->BreadcrumbItems = array
					(
						new BreadcrumbItem("~/community", "Community"),
						new BreadcrumbItem("~/community/pages", "Pages")
					);
					break;
				}
				case "forums":
				{
					$this->BreadcrumbItems = array
					(
						new BreadcrumbItem("~/community", "Community"),
						new BreadcrumbItem("~/community/forums", "Forums")
					);
					break;
				}
			}
		}
		
		protected function BeforeContent()
		{
			parent::BeforeContent();
			
			$tbsCommunity = new TabStrip("tbsCommunity");
			$tbsCommunity->TabPosition = TabContainerTabPosition::Top;
			$tbsCommunity->Tabs[] = new TabStripTab("tabMembers", "<i class=\"fa fa-users\"></i> <span class=\"Text\">Members (" . User::Count() . ")</span>", "~/community/members", null, ($this->Name == "members"));
			if (System::GetConfigurationValue("Groups.Enabled", false))
			{
				$tbsCommunity->Tabs[] = new TabStripTab("tabGroups", "<i class=\"fa fa-comments\"></i> <span class=\"Text\">Groups (" . Group::Count() . ")</span>", "~/community/groups", null, ($this->Name == "groups"));
			}
			if (System::GetConfigurationValue("Pages.Enabled", false))
			{
				$tbsCommunity->Tabs[] = new TabStripTab("tabPages", "<i class=\"fa fa-flag\"></i> <span class=\"Text\">Pages (" . Page::Count() . ")</span>", "~/community/pages", null, ($this->Name == "pages"));
			}
			$tbsCommunity->Render();
			
			/*
	?>
	<table style="width: 100%">
		<tr>
			<td style="width: 128px; vertical-align: top;">
				<?php
					// $actionList = new PsychaticaActionList();
					// $actionList->Items = array
					// (
					//		new PsychaticaActionListItem("~/community/members", "Members (" . User::Count() . ")"),
					//		new PsychaticaActionListItem("~/community/groups", "Groups (" . User::Count() . ")"),
					//		new PsychaticaActionListItem("~/community/pages", "Pages (" . User::Count() . ")")
					// );
					// $actionList->Items[1]->Selected = true;
				?>
				
				<div class="ActionList">
					<?php
					// if (System::$Configuration["Members.Enabled"]) {
					if ($pagename == "members") {
						if ($outline) { ?> <a class="Selected" href="<?php echo(System::ExpandRelativePath("~/community/members")); ?>"> <?php }
						else { ?> <span class="Selected"> <?php }
					} else { ?><a href="<?php echo(System::ExpandRelativePath("~/community/members")); ?>"> <?php } ?>
					Members (<?php echo(User::Count()); ?>)
						<?php if ($pagename == "members" && !$outline) { ?> </span> <?php } else { ?> </a> <?php }
					//	}
					
					if (System::$Configuration["Groups.Enabled"]) {
					if ($pagename == "groups") {
						if ($outline) { ?> <a class="Selected" href="<?php echo(System::ExpandRelativePath("~/community/groups")); ?>"> <?php }
						else { ?> <span class="Selected"> <?php }
					} else { ?><a href="<?php echo(System::ExpandRelativePath("~/community/groups")); ?>"> <?php } ?>
					Groups (<?php echo(Group::Count()); ?>)
						<?php if ($pagename == "groups" && !$outline) { ?> </span> <?php } else { ?> </a> <?php }
					}
					
					if (System::$Configuration["Pages.Enabled"]) {
					if ($pagename == "pages") {
						if ($outline) { ?> <a class="Selected" href="<?php echo(System::ExpandRelativePath("~/community/pages")); ?>"> <?php }
						else { ?> <span class="Selected"> <?php }
					} else { ?><a href="<?php echo(System::ExpandRelativePath("~/community/pages")); ?>"> <?php } ?>
					Pages (<?php echo(Page::Count()); ?>)
						<?php if ($pagename == "pages" && !$outline) { ?> </span> <?php } else { ?> </a> <?php }
					}
					
					if (System::$Configuration["Forums.Enabled"]) {
					if ($pagename == "forums") {
						if ($outline) { ?> <a class="Selected" href="<?php echo(System::ExpandRelativePath("~/community/forums")); ?>"> <?php }
						else { ?> <span class="Selected"> <?php }
					} else { ?><a href="<?php echo(System::ExpandRelativePath("~/community/forums")); ?>"> <?php } ?>
					Forums (<?php echo(Forum::Count()); ?>)
						<?php if ($pagename == "forums" && !$outline) { ?> </span> <?php } else { ?> </a> <?php }
					}
					?>
				</div>
			</td>
			<td>
	<?php
			*/
		}
		protected function AfterRenderContent()
		{
	?>
			</td>
		</tr>
	</table>
	<?php
			parent::AfterRenderContent();
		}
	}
	
	
	System::$Modules[] = new Module("net.phoenixsns.Community", array
	(
		new ModulePage("community", function($path)
		{
			$CurrentUser = User::GetCurrent();
			$pageTitle = "";
			switch ($path[0])
			{
				case "members.atom":
				{
					?>
					
					<?php
					return true;
				}
				case "members.rss":
				{
					header("Content-Type: application/rss+xml");
					echo("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n");
					echo("<rss version=\"2.0\">\n");
					echo("\t<channel>\n");
					echo("\t\t<title>Members</title>\n");
					echo("\t\t<description>A list of all members in " . System::$Configuration["Application.Name"] . "</description>\n");
					echo("\t\t<link>" . System::ExpandRelativePath("~/") . "</link>\n");
					echo("\t\t<lastBuildDate>Mon, 06 Sep 2010 00:01:00 +0000 </lastBuildDate>\n");
					echo("\t\t<pubDate>Mon, 06 Sep 2009 16:20:00 +0000 </pubDate>\n");
					echo("\t\t<ttl>1800</ttl>\n");

					$members = User::Get();
					foreach($members as $member)
					{
						echo("\t\t<item>\n");
						echo("\t\t\t<title>" . $member->LongName . "</title>\n");
						echo("\t\t\t<description>" . \JH\Utilities::HtmlEncode($member->ProfileContent->Description) . "</description>\n");
						echo("\t\t\t<link>" . System::ExpandRelativePath("~/community/members/" . $member->ShortName) . "</link>\n");
						// <guid>unique string per item</guid>
						echo("\t\t\t<pubDate>Mon, 06 Sep 2009 16:20:00 +0000 </pubDate>\n");
						echo("\t\t</item>\n");
					}
					echo("\t</channel>\n");
					echo("</rss>");
					return true;
				}
				case "members":
				{
					require("Members/Main.inc.php");
					return true;
				}
				case "groups":
				{
					if (System::$Configuration["Groups.Enabled"])
					{
						require("Groups/Main.inc.php");
						return true;
					}
					else
					{
						System::Redirect("~/community");
						return true;
					}
				}
				case "forums":
				{
					if (System::$Configuration["Forums.Enabled"])
					{
						require("Forums/Main.inc.php");
						return true;
					}
					else
					{
						System::Redirect("~/community");
						return true;
					}
				}
				case "pages":
				{
					if (System::$Configuration["Pages.Enabled"])
					{
						require("Pages/Main.inc.php");
						return true;
					}
					else
					{
						System::Redirect("~/community");
						return true;
					}
				}
				default:
				{
					System::Redirect("~/community/members");
					return true;
				}
			}
			return true;
		})
	));
?>