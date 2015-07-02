<?php
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use WebFX\WebControl;
	
	use PhoenixSNS\MasterPages\WebPage;
	
	class PayPalForm extends WebControl
	{
		public $CurrencyCode;
		public $EncryptedData;
		
		public function __construct($id, $currencyCode, $encryptedData)
		{
			parent::__construct($id);
			$this->CurrencyCode = $currencyCode;
			$this->EncryptedData = $encryptedData;
		}
		
		protected function BeforeContent()
		{
			$paypalURL = "https://www.paypal.com/cgi-bin/webscr";
			echo("<form action=\"" . $paypalURL . "\" method=\"post\" target=\"_top\" id=\"" . $this->ID . "\">");
		}
		protected function AfterContent()
		{
			echo("<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\" />");
			echo("<input type=\"hidden\" name=\"currency_code\" />");
			echo("<input type=\"hidden\" name=\"encrypted\" />");
			/* echo("<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynow_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />"); */
			echo("<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\" />");
			
			echo("<script type=\"text/javascript\">");
			echo("var " . $this->ID . " = document.getElementById(\"" . $this->ID . "\");");
			echo($this->ID . "[\"currency_code\"].value = \"" . $this->CurrencyCode . "\";");
			echo($this->ID . "[\"encrypted\"].value = \"" . $this->EncryptedData . "\";");
		}
	}
	
	System::$Modules[] = new Module("net.phoenixsns.Contribute", array
	(
		new ModulePage("contribute", array
		(
			new ModulePage("", function($path)
			{
				$page = new WebPage();
				$page->BeginContent();
	?>
	<p>
		Contribute will be available soon. Until then, check out the <a href="https://www.facebook.com/groups/psychatica">Psychatica Development Group</a> on Facebook, where we do most of our collaboration and development. If you wish to be included in the process, we would appreciate a sample of your work so we can get an idea of what you do and best assign tasks that you might be interested in working on.
	</p>
	<p style="text-align: center;">
		<a href="<?php echo(System::ExpandRelativePath("~/")); ?>">Back to <?php echo(System::GetConfigurationValue("Application.Name")); ?></a>
	</p>
	<?php
				$page->EndContent();
			}),
			new ModulePage("purchase", array
			(
				new ModulePage("", function($path)
				{
					$page = new WebPage();
					$page->BeginContent();
			?>
					<div class="Card">
						<div class="Title">Buy PsychatiGOLD with PayPal&reg;</div>
						<div class="Content">
							<p>Thank you for choosing to support PhoenixSNS and <?php echo(System::GetConfigurationValue("Application.Name")); ?> development by purchasing PsychatiGold!</p>
							<p>PsychatiGold lets you get ahead of the game by purchasing a small, medium, or large pack of gold. Please choose the package you need:</p>
							<?php
								$paypalForm = new PayPalForm("frmPurchase", "USD", "-----BEGIN PKCS7-----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-----END PKCS7-----");
								$paypalForm->BeginContent();
							?>
								<table style="width: 100%;" border="1">
									<tr>
										<th style="width: 25%;">Amount</th>
										<th style="width: 25%;">What you pay (in USD)</th>
										<th style="width: 25%;">What you get (in PsychatiGold)</th>
										<th style="width: 25%;">Equivalent amount in PsychatiSilver</th>
									</tr>
									<tr>
										<td><input type="radio" name="os0" value="single" /> One Gold Coin</td>
										<td>$0.10</td>
										<td>1 piece</td>
										<td>3000 pieces</td>
									</tr>
									<tr>
										<td><input type="radio" name="os0" value="five" /> Five Gold Coins</td>
										<td>$0.50</td>
										<td>5 pieces</td>
										<td>15 000 pieces</td>
									</tr>
									<tr>
										<td><input type="radio" name="os0" value="small" /> Small Pouch of Gold</td>
										<td>$1.00</td>
										<td>10 pieces</td>
										<td>30 000 pieces</td>
									</tr>
									<tr>
										<td><input type="radio" name="os0" value="medium" /> Medium Sack of Gold</td>
										<td>$5.00</td>
										<td>50 pieces</td>
										<td>150 000 pieces</td>
									</tr>
									<tr>
										<td><input type="radio" name="os0" value="large" /> Large Crate of Gold</td>
										<td>$10.00</td>
										<td>100 pieces</td>
										<td>300 000 pieces</td>
									</tr>
								</table>
								<p>
									Of course, PsychatiGold may be converted to (a larger amount of) PsychatiSilver at any time, and by the same token, PsychatiSilver may
									be converted to (a smaller amount of) PsychatiGold.
								</p>
							<?php
								$paypalForm->EndContent();
							?>
							<p>
								Clicking the &quot;Purchase&quot; button will take you to a secure page on PayPal&reg;, where you will complete your payment with your
								preferred payment method. Sorry, we only provide conversion rates for USD currency at this time. Depending on your location you may still
								be able to complete a purchase.
							</p>
						</div>
						<div class="Actions Horizontal">
							<a id="cmdPurchase" href="#"><i class="fa fa-money"></i> <span class="Text">Purchase</span></a>
						</div>
					</div>
					<script type="text/javascript">
						window.addEventListener("load", function(e)
						{
							var cmdPurchase = document.getElementById("cmdPurchase");
							cmdPurchase.addEventListener("click", function(e)
							{
								frmPurchase.submit();
							});
						};
					</script>
			<?php
					$page->EndContent();
					return true;
				}),
				new ModulePage("complete", function($path)
				{
					$page = new \PsychaticaWebPage();
					$page->BeginContent();
					?>
					<div class="Card">
						<div class="Title"><i class="fa fa-smile-o"></i> <span class="Text">Thank you for your payment!</span></div>
						<div class="Content">
							<p>
								Your transaction has been completed, and a receipt for your purchase has been emailed to you.
								You may log into your PayPal&reg; account to view details of this transaction.
							</p>
							<p style="text-align: center;">
								<img src="<?php echo(System::ExpandRelativePath("~/images/coingold.png")); ?>" alt="PsychatiGold" /><br />
								<span style="font-size: 24pt; font-weight: bold;">50500</span><br />
								PsychatiGold coins have been added to your Wallet
							</p>
						</div>
						<div class="Actions Horizontal">
							<a href="#">Go to the Market</a>
						</div>
					</div>
					<?php
					$page->EndContent();
					return true;
				})
			))
		))
	));
?>