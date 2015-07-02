<?php
	namespace PhoenixSNS\Modules\Admin\Pages;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	
	use PhoenixSNS\Modules\Admin\MasterPages\WebPage;
	use PhoenixSNS\Objects\User;
	
	class AdminMainPage extends WebPage
	{
		protected function RenderContent()
		{
			?>
			<div class="CardLayout">
				<div class="CardSet">
					<div class="Card">
						<div class="Title">Users</div>
						<div class="Content">
							<div><span class="Emphasis"><?php $count = User::Count(true); echo($count); ?></span> registered <?php echo(($count == 1) ? "user" : "users"); ?></div>
							<div>Most recently active users:</div>
							<?php
								$lvUsers = new ListView();
								$lvUsers->Columns = array
								(
									new ListViewColumn("lvcUserLink", "User"),
									new ListViewColumn("lvcUserLastActiveDate", "Last Active Date")
								);
								$lvUsers->Render();
							?>
						</div>
						<div class="Actions">
							<a href="#">Manage Users</a>
						</div>
					</div>
					<div class="Card">
						<div class="Title">Groups</div>
						<div class="Content">
						</div>
						<div class="Actions">
							<a href="#">Manage Groups</a>
						</div>
					</div>
					<div class="Card">
						<div class="Title">Market</div>
						<div class="Content">
						</div>
						<div class="Actions">
							<a href="#">Manage Items</a>
							<a href="#">Manage Resources</a>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
?>