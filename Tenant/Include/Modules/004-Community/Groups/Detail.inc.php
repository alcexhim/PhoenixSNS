<?php
	namespace PhoenixSNS\Modules\Community;
	
	use WebFX\System;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	use WebFX\Controls\BreadcrumbItem;
	use WebFX\Controls\Panel;
	
	use PhoenixSNS\Objects\Group;
	use PhoenixSNS\Objects\User;
	
	use PhoenixSNS\Pages\ErrorPage;
	
	$thisgroup = Group::GetByIDOrName($path[1]);
	
	if ($thisgroup == null)
	{
		$page = new ErrorPage("Group not found");
		$page->Message = "That group does not exist in the system. It may have been deleted, or you may have typed the name incorrectly.";
		$page->ReturnButtonURL = "~/community/groups";
		$page->ReturnButtonText = "Return to Group List";
		$page->Render();
		return;
	}
	
	class GroupDetailPage extends CommunityPage
	{
		public $Group;
		public $Path;
		
		public function __construct($group)
		{
			parent::__construct();
			
			$this->Title = $group->Title;
			$this->Group = $group;
		}
		protected function Initialize()
		{
			parent::Initialize();
			
			$this->BreadcrumbItems = array
			(
				new BreadcrumbItem("~/community", "Community"),
				new BreadcrumbItem("~/community/groups", "Groups"),
				new BreadcrumbItem("~/community/groups/" . $this->Group->Name, $this->Group->Title)
			);
		}
		
		protected function RenderContent()
		{
			$CurrentUser = User::GetCurrent();
?>
<div class="Card">
	<div class="Title">
		<i class="fa fa-users"></i> <span class="Text"><?php echo($this->Group->Title); ?></span>
	</div>
	<div class="Content">
		<table style="width: 100%">
			<tr>
				<td style="width: 128px;"><img src="<?php echo(System::ExpandRelativePath("~/community/groups/" . $this->Group->Name . "/images/avatar/thumbnail.png")); ?>" style="width: 112px; height: 112px;" /></td>
				<td><?php echo($this->Group->Description); ?></td>
			</tr>
		</table>
		<table style="width: 100%">
			<tr>
				<td style="width: 25%">Created on:</td>
				<td><?php echo($this->Group->DateCreated); ?></td>
			</tr>
		</table>
	</div>
	<div class="Actions Horizontal">
	<?php
	if ($CurrentUser != null)
	{
		if ($this->Group->HasMember($CurrentUser))
		{
		?>
		<a title="Invite Friends" href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $this->Group->Name . "/invite")); ?>" onclick="InviteFriendsDialog.Show(<?php echo($this->Group->ID); ?>); return false;"><i class="fa fa-share-square-o"></i> <span class="Text">Invite Friends</span></a>
		<a title="Leave Group" href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $this->Group->Name . "/disconnect")); ?>"><i class="fa fa-sign-out"></i> <span class="Text">Leave Group</span></a>
		<?php
		}
		else if ($this->Group->HasPermission($CurrentUser, 2) || $this->Group->HasPermission($CurrentUser, 3))
		{
		?>
		<a title="Join Group" href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $this->Group->Name . "/connect")); ?>"><i class="fa fa-sign-in"></i> <span class="Text">Join Group</span></a>
		<?php
		}
		if ($this->Group->HasPermission($CurrentUser, 4))
		{
		?>
		<a title="Change Group Settings" href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $this->Group->Name . "/settings")); ?>"><i class="fa fa-cog"></i> <span class="Text">Change Group Settings</span></a>
		<?php
		}
		if ($this->Group->Creator->ID == $CurrentUser->ID)
		{
		?>
		<a title="Delete Group" href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $this->Group->Name . "/delete.phnx")); ?>"><i class="fa fa-trash-o"></i> <span class="Text">Delete Group</span></a>
		<?php
		}
	}
	else
	{
		?><i class="fa fa-warning"></i> Please log in to join this group.<?php
	}
	?>
	</div>
</div>
<div class="Card">
	<div class="Title">
		<i class="fa fa-comments"></i> <span class="Text">Discussions (<?php echo($this->Group->CountTopics()); ?>)</span>
	</div>
	<div class="Content">
		<div class="ListBox">
<?php
			$topics = $this->Group->GetTopics(5);
			foreach ($topics as $topic)
			{
?>
				<a href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $this->Group->Name . "/topics/" . $topic->Name)); ?>">
					<div class="ListItemTitle"><?php echo($topic->Title); ?></div>
				</a>
<?php
			}
?>
		</div>
	</div>
	<div class="Actions Horizontal">
<?php
		if ($this->Group->HasMember($CurrentUser))
		{
?>
			<a href="<?php echo(System::ExpandRelativePath("~/community/groups/" . $this->Group->Name . "/topics/create.mmo")); ?>" title="Start a discussion"><i class="fa fa-pencil-square-o"></i> <span class="Text">Start a discussion</span></a>
<?php
		}
?>
	</div>
</div>
<div class="Card">
	<div class="Title">
		<i class="fa fa-shield"></i> <span class="Text">Members (<?php echo($this->Group->CountMembers()); ?>)</span>
	</div>
	<div class="Content">
		<div class="ButtonGroup ButtonGroupHorizontal">
<?php
		$members = $this->Group->GetMembers();
		foreach ($members as $member)
		{
?>
			<a class="ButtonGroupButton" href="<?php echo(System::ExpandRelativePath("~/community/members/" . $member->ShortName)); ?>" onclick="DisplayMemberInformation(<?php echo($member->ID); ?>); return false;">
				<img class="ButtonGroupButtonImage" src="<?php echo(System::ExpandRelativePath("~/community/members/" . $member->ShortName . "/images/avatar/thumbnail.png")); ?>" />
				<span class="ButtonGroupButtonText"><?php echo($member->ToString()); ?></span>
			</a>
<?php
		}
?>
		</div>
	</div>
</div>
<?php
		}
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
							$fileName = "images/icons/group.png";
							// header("Content-Type: " . mime_content_type("images/icons/group.png"));
							readfile($fileName);
							return;
						}
					}
					break;
				}
			}
			break;
		}
		case "delete.phnx":
		{
			require("delete.inc.php");
			return;
		}
		case "connect":
		{
			$this->Group->AddMember($CurrentUser);
			System::Redirect("~/community/groups/" . $this->Group->Name);
			return;
		}
		case "disconnect":
		{
			$this->Group->RemoveMember($CurrentUser);
			System::Redirect("~/community/groups/" . $this->Group->Name);
			return;
		}
		default:
		{
			if (count($path) > 3 && $path[3] != "")
			{
				switch($path[3])
				{
					case "create.mmo":
					{ // BEGIN create.mmo
						require("Topics/Create.inc.php");
						return;
					} // END create.mmo
					default:
					{
						require("Topics/Detail.inc.php");
						return;
					}
				}
			}
			else
			{
				$page = new GroupDetailPage($thisgroup);
				$page->Path = $path[2];
				$page->Render();
				return;
			}
		}
	}
?>