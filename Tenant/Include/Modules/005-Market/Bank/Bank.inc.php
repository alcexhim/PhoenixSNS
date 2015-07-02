<?php
	if (!System::$Configuration["Bank.Enabled"])
	{
		System::Redirect("~/market");
		return;
	}
	
	$page = new PsychaticaWebPage("Bank");
	$page->BeginContent();
	?>
	<p>
		Welcome to the <?php echo(System::$Configuration["Application.Name"]); ?> Bank. Here you can deposit or withdraw Resources
		for a nominal fee. Resources will be stored in the Vault, where they will accrue interest at the rates described below. Items
		will be stored in the Safe Deposit Box.
	</p>
	<p>
		Please note that not all resources can be stored in the Bank. If the resource does not appear in the table below, then it cannot
		be stored in the bank.
	</p>
	<table style="width: 100%">
		<tr>
			<td style="width: 25%">
				<div class="ActionList">
					<a id="lnkInformation" href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/market/bank" onclick="SwitchTab('tabInformation', 'lnkInformation'); return false;" <?php if($path[1] == "") echo("class=\"Selected\""); ?>>Information</a>
					<a id="lnkDeposit" href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/market/bank/deposit.phnx" onclick="SwitchTab('tabDeposit', 'lnkDeposit'); return false;" <?php if($path[1] == "deposit.phnx") echo("class=\"Selected\""); ?>>Deposit</a>
					<a id="lnkWithdraw" href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/market/bank/withdraw.phnx" onclick="SwitchTab('tabWithdraw', 'lnkWithdraw'); return false;" <?php if($path[1] == "withdraw.phnx") echo("class=\"Selected\""); ?>>Withdraw</a>
				</div>
			</td>
			<td>
				<div class="TabContainerContentArea">
					<div class="TabContainerContent" id="tabInformation" <?php if ($path[1] == "") { echo("style=\"display: block;\""); } ?>>
						<?php include("bankinfotable.inc.php"); ?>
					</div>
					<div class="TabContainerContent" id="tabDeposit" <?php if ($path[1] == "deposit.phnx") { echo("style=\"display: block;\""); } ?>>
						<div class="Panel">
							<h3 class="PanelTitle">Deposit Resources</h3>
							<div class="PanelContent">
								<?php // if $resourceType->CanStoreInBank ?>
								<table style="width: 100%">
									<tr>
										<td style="width: 25%">Credits:</td>
										<td>
											<div class="TrackBar">
												<div class="Thumb">
													<div class="ThumbLine">&nbsp;</div>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<?php
							if (null /* $thisuser->SafeDepositBox */ == null)
							{
							?>
							<p>You do not have a Safe Deposit Box and cannot store items. <a href="#">Buy one</a> for 1000 credits.</p>
							<?php
							}
							else
							{
							?>
							<div class="Panel">
								<h3 class="PanelTitle">Deposit Items</h3>
								<div class="PanelContent">
									<?php // if $resourceType->CanStoreInBank ?>
									<table style="width: 100%">
										<tr>
											<td style="width: 25%">Credits:</td>
										</tr>
									</table>
								</div>
							</div>
							<?php
							}
						?>
					</div>
					<div class="TabContainerContent" id="tabWithdraw" <?php if ($path[1] == "withdraw.phnx") { echo("style=\"display: block;\""); } ?>>
						<div class="Panel">
							<h3 class="PanelTitle">Withdraw Resources</h3>
							<div class="PanelContent">
								<?php // if $resourceType->CanStoreInBank ?>
								<table style="width: 100%">
									<tr>
										<td style="width: 25%">Credits:</td>
										<td>
											<div class="TrackBar">
												<div class="Thumb">
													<div class="ThumbLine">&nbsp;</div>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<?php
							if (null /* $thisuser->SafeDepositBox */ == null)
							{
							?>
							<p>You do not have a Safe Deposit Box from which to withdraw items.</p>
							<?php
							}
							else
							{
							?>
							<div class="Panel">
								<h3 class="PanelTitle">Deposit Items</h3>
								<div class="PanelContent">
									<?php // if $resourceType->CanStoreInBank ?>
									<table style="width: 100%">
										<tr>
											<td style="width: 25%">Credits:</td>
										</tr>
									</table>
								</div>
							</div>
							<?php
							}
						?>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<?php
	$page->EndContent();
	return;
?>