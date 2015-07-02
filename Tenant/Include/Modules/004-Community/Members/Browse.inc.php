<?php
	namespace PhoenixSNS\Modules\Community;
	
	use WebFX\System;
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	use WebFX\Controls\BreadcrumbItem;
	
	use WebFX\Controls\TabContainerTabPosition;
	use WebFX\Controls\TabStrip;
	use WebFX\Controls\TabStripTab;
	
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantObject;
	
	class PsychaticaMemberCommunityPage extends CommunityPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->BreadcrumbItems = array
			(
				new BreadcrumbItem("~/community", "Community"),
				new BreadcrumbItem("~/community/members", "Members")
			);
			$this->Name = "members";
			$this->Title = "Members";
		}
		
		protected function RenderContent()
		{
			$CurrentTenant = Tenant::GetCurrent();
			$objUser = $CurrentTenant->GetObject("User");
			$CurrentUser = $objUser->GetMethod("GetCurrentUser")->Execute();
			
			$members = $objUser->GetInstances();
			$count = count($members);
			
			foreach ($members as $member)
			{
				$description = $member->GetPropertyValue("Description");		// SingleInstance<Gender>
				
				$gender = $member->GetPropertyValue("Gender");					// SingleInstance<Gender>
				if ($gender != null) $gender = $gender->ToString();
				
				$age = null;
				$birthdate = $member->GetPropertyValue("BirthDate"); 			// DateTime
				if ($birthdate != null)
				{
					// TODO: calculate age from birth date
					$age = $birthdate;
				}
				
				$location = $member->GetPropertyValue("Location"); 			// SingleInstance<Place>
				if ($location != null) $location = $location->ToString();
				
?>
<div class="Card MemberCard">
	<div class="Title">
		<table style="width: 100%;">
			<tr>
				<td rowspan="2" style="width: 96px;"><img src="<?php echo(System::ExpandRelativePath("~/community/members/" . $member->GetPropertyValue("URL") . "/images/avatar/thumbnail.png")); ?>" alt="<?php echo($member->GetPropertyValue("DisplayName")); ?>" title="<?php echo($member->GetPropertyValue("DisplayName")); ?>" /></td>
				<td class="MemberName"><?php echo($member->GetPropertyValue("DisplayName")); ?></td>
			</tr>
			<tr>
				<td><?php
				if ($gender != null) echo($gender . " - ");
				if ($age != null) echo($age . " - ");
				if ($location != null) echo ($location->ToString());
				?></td>
			</tr>
		</table>
	</div>
	<?php
	if ($description != null)
	{
		?><div class="Content"><?php echo($description); ?></div><?php
	}
	?>
	<div class="Actions Horizontal">
		<div class="Left" style="background-color: #3366FF;">
			<div style="padding-left: 16px; color: #FFFFFF; font-weight: bold;">Member</div>
		</div>
		<div class="Right">
		<?php
			if ($CurrentUser != null)
			{
				$hasfriend = false;
				/*
				$hasfriend = $CurrentUser->GetMethod("HasFriend")->Execute(array
				(
					new TenantQueryParameter("member", $member)
				));
				*/
				if ($hasfriend)
				{
					?>
					<strong>Friends</strong>
					<?php
				}
				else
				{
					?>
					<a href="#" title="Add Friend"><i class="fa fa-plus"></i> <span class="Text">Add Friend</span></a>
					<?php
				}
			?>
			<a href="#" title="Poke"><i class="fa fa-hand-o-right"></i> <span class="Text">Poke</span></a>
			<a href="#" title="View Profile"><i class="fa fa-eye"></i> <span class="Text">View Profile</span></a>
			<?php
			}
		?>
		</div>
	</div>
</div>
<?php
			}
?>
</div>
<?php
		}
	}
	
	$page = new PsychaticaMemberCommunityPage();
	$page->Render();
?>