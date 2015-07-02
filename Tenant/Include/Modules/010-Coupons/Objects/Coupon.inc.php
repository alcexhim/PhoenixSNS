<?php
	namespace PhoenixSNS\Modules\Coupons\Objects;
	
	class Coupon// extends DatabaseObject
	{
		// SELECT * FROM DB_PREFIX_Coupons
		protected $TableName = "Coupons";
		
		public $ID;
		public $Value;
		public $TimestampBegin;
		public $TimestampEnd;
		
		
	}
?>