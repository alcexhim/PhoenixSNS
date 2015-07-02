<?php
	namespace PhoenixSNS\Modules\Coupons\Pages;
	use WebFX\System;
	
	use PhoenixSNS\MasterPages\WebPage;
	use PhoenixSNS\Objects\User;
	
	class EnterCouponPage extends WebPage
	{
		protected function RenderContent()
		{
		?>
			<p>
				Welcome to <?php echo(System::$Configuration["Application.Name"]); ?>! Please enter the PIN you received
				to claim your reward. <a href="#">Where is my PIN?</a>
			</p>
			<form>
				<div class="FormContent">
					<table>
						<tr>
							<td><label for="txtPIN">Redeem <u>P</u>IN:</label></td>
							<td><input type="text" accesskey="P" /></td>
						</tr>
					</table>
				</div>
				<div class="FormButtons">
					<input type="submit" value="OK" />
				</div>
			</form>
		<?php
		}
	}
?>