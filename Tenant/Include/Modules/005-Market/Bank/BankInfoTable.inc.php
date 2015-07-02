<?php $resources = MarketResource::Get(); ?>
<table style="width: 100%">
	<tr>
		<td style="width: 200px">Interest rate:</td>
		<td>
			<?php
			foreach ($resources as $resource)
			{
				if ($resource->BankInfo->Enabled)
				{
			?>
				<span class="ResourceDisplay">
					<img alt="<?php echo($resource->Title); ?>:" title="<?php echo($resource->Title); ?>" src="/images/resources/24x24/<?php echo($resource->Name); ?>.png" class="ResourceIcon"> <span class="ResourceValue"><?php echo($resource->BankInfo->InterestRate); ?>%</span>
				</span>
			<?php
				}
			}
			?>
		</td>
	</tr>
	<tr>
		<td>Interest period:</td>
		<td>
			<?php
			foreach ($resources as $resource)
			{
				if ($resource->BankInfo->Enabled)
				{
			?>
				<span class="ResourceDisplay">
					<img alt="<?php echo($resource->Title); ?>:" title="<?php echo($resource->Title); ?>" src="/images/resources/24x24/<?php echo($resource->Name); ?>.png" class="ResourceIcon"> <span class="ResourceValue"><?php echo($resource->BankInfo->InterestPeriod); ?> days</span>
				</span>
			<?php
				}
			}
			?>
		</td>
	</td>
	<tr>
		<td>Minimum deposit:</td>
		<td>
			<?php
			foreach ($resources as $resource)
			{
				if ($resource->BankInfo->Enabled)
				{
			?>
				<span class="ResourceDisplay">
					<img alt="<?php echo($resource->Title); ?>:" title="<?php echo($resource->Title); ?>" src="/images/resources/24x24/<?php echo($resource->Name); ?>.png" class="ResourceIcon"> <span class="ResourceValue"><?php echo($resource->BankInfo->MinimumDeposit); ?></span>
				</span>
			<?php
				}
			}
			?>
		</td>
	</tr>
	<tr>
		<td>Deposit fee:</td>
		<td>
			<?php
			foreach ($resources as $resource)
			{
				if ($resource->BankInfo->Enabled)
				{
			?>
				<span class="ResourceDisplay">
					<img alt="<?php echo($resource->Title); ?>:" title="<?php echo($resource->Title); ?>" src="/images/resources/24x24/<?php echo($resource->Name); ?>.png" class="ResourceIcon"> <span class="ResourceValue">
					<?php
						if ($resource->BankInfo->DepositFeePercentage != null)
						{
							echo($resource->BankInfo->DepositFeePercentage . "%");
						}
						else if ($resource->BankInfo->DepositFeeValue != null)
						{
							echo($resource->BankInfo->DepositFeeValue);
						}
					?>
					</span>
				</span>
			<?php
				}
			}
			?>
		</td>
	</tr>
	<tr>
		<td>Minimum withdrawal:</td>
		<td>
			<?php
			foreach ($resources as $resource)
			{
				if ($resource->BankInfo->Enabled)
				{
			?>
				<span class="ResourceDisplay">
					<img alt="<?php echo($resource->Title); ?>:" title="<?php echo($resource->Title); ?>" src="/images/resources/24x24/<?php echo($resource->Name); ?>.png" class="ResourceIcon"> <span class="ResourceValue"><?php echo($resource->BankInfo->MinimumWithdrawal); ?></span>
				</span>
			<?php
				}
			}
			?>
		</td>
	</tr>
	<tr>
		<td>Withdrawal fee:</td>
		<td>
			<?php
			foreach ($resources as $resource)
			{
				if ($resource->BankInfo->Enabled)
				{
			?>
				<span class="ResourceDisplay">
					<img alt="<?php echo($resource->Title); ?>:" title="<?php echo($resource->Title); ?>" src="/images/resources/24x24/<?php echo($resource->Name); ?>.png" class="ResourceIcon"> <span class="ResourceValue">
					<?php
						if ($resource->BankInfo->WithdrawalFeePercentage != null)
						{
							echo($resource->BankInfo->WithdrawalFeePercentage . "%");
						}
						else if ($resource->BankInfo->WithdrawalFeeValue != null)
						{
							echo($resource->BankInfo->WithdrawalFeeValue);
						}
					?></span>
				</span>
			<?php
				}
			}
			?>
		</td>
	</tr>
</table>