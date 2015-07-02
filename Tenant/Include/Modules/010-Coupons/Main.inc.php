<?php
	namespace PhoenixSNS\Modules\Coupons;
	
	require("Objects/Coupon.inc.php");
	
	require("Pages/EnterCouponPage.inc.php");
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use User;
	
	use PhoenixSNS\Modules\Coupons\Objects\Coupon;
	
	use PhoenixSNS\Modules\Coupons\Pages\EnterCouponPage;
	
	use PhoenixSNS\MasterPages\ErrorPage;
	
	System::$Modules[] = new Module("net.phoenixsns.Coupon", array
	(
		new ModulePage("coupon", function($path)
		{
			$CurrentUser = User::GetCurrent();
			switch ($path[0])
			{
				case "redeem":
				{
					if ($path[1] == "")
					{
						$page = new EnterCouponPage();
						$page->Render();
					}
					else
					{
						$coupon = Coupon::GetByValue($path[1]);
					}
					break;
				}
				default:
				{
					System::Redirect("~/coupon/redeem");
					break;
				}
			}
			return true;
		})
	));
?>