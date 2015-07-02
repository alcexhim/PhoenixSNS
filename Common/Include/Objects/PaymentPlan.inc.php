<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	use WebFX\Enumeration;
	
	/**
	 * Interval describing how often a payment is made in a particular payment
	 * plan.
	 * @author Michael Becker
	 */
	abstract class PaymentPlanPaymentIntervalType extends Enumeration
	{
		/**
		 * Payment is made hourly.
		 * @var int
		 */
		const Hour = 1;
		/**
		 * Payment is made daily.
		 * @var int
		 */
		const Day = 2;
		/**
		 * Payment is made monthly.
		 * @var int
		 */
		const Month = 3;
		/**
		 * Payment is made annually.
		 * @var int
		 */
		const Year = 4;
	}
	
	/**
	 * A payment plan that can be associated with a tenant.
	 * @author Michael Becker
	 */
	class PaymentPlan
	{
		/**
		 * The unique, incremental ID number of this PaymentPlan
		 * @var int
		 */
		public $ID;
		/**
		 * The title of this PaymentPlan
		 * @var string
		 */
		public $Title;
		/**
		 * A short description for this PaymentPlan
		 * @var string
		 */
		public $Description;
		
		/**
		 * Counts the number of PaymentPlans stored on this server.
		 * @return int
		 */
		public static function Count()
		{
			global $MySQL;
			$query = "SELECT COUNT(paymentplan_ID) FROM " . System::$Configuration["Database.TablePrefix"] . "PaymentPlans";
			$result = $MySQL->query($query);
			if ($result === false) return 0;
			$values = $result->fetch_array();
			return $values[0];
		}
		
		/**
		 * Creates a new PaymentPlan object based on the given values from the database.
		 * @param array $values
		 * @return PaymentPlan based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new PaymentPlan();
			$item->ID = $values["paymentplan_ID"];
			$item->Title = $values["paymentplan_Title"];
			$item->Description = $values["paymentplan_Description"];
			return $item;
		}
		/**
		 * Retrieves all PaymentPlans.
		 * @param int $max The maximum number of PaymentPlans to return
		 * @return PaymentPlan[]
		 */
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PaymentPlans";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = PaymentPlan::GetByAssoc($values);
			}
			return $retval;
		}
		/**
		 * Retrieves a single PaymentPlan with the given ID.
		 * @param int $id The ID of the PaymentPlan to return
		 * @return NULL|PaymentPlan The PaymentPlan with the given ID, or NULL if no PaymentPlan with the given ID was found
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PaymentPlans WHERE paymentplan_ID = " . $id;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return PaymentPlan::GetByAssoc($values);
		}
		
		/**
		 * Gets the payments that are associated with this PaymentPlan.
		 * @return PaymentPlanPayment[]
		 */
		public function GetPayments()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "PaymentPlanPayments WHERE paymentplanpayment_PaymentPlanID = " . $this->ID;
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = PaymentPlanPayment::GetByAssoc($values);
			}
			return $retval;
		}

		/**
		 * Gets the JSON representation of this object for use in AJAX calls.
		 * @return string The JSON representation of this object.
		 */
		public function ToJSON()
		{
			$json = "";
			$json .= "{";
			$json .= "\"ID\":" . $this->ID . ",";
			$json .= "\"Title\":\"" . \JH\Utilities::JavaScriptDecode($this->Title, "\"") . "\",";
			$json .= "\"Description\":\"" . \JH\Utilities::JavaScriptDecode($this->Description, "\"") . "\"";
			$json .= "}";
			return $json;
		}
	}
	/**
	 * Represents a payment made to a particular payment plan.
	 * @author Michael Becker
	 */
	class PaymentPlanPayment
	{
		/**
		 * The PaymentPlan to which this PaymentPlanPayment belongs.
		 * @var PaymentPlan
		 */
		public $PaymentPlan;
		
		public $FixedAmount;
		public $VariableAmount;
		public $IntervalValue;
		public $IntervalType;
		
		public static function GetByAssoc($values)
		{
			$this->PaymentPlan = PaymentPlan::GetByID($values["paymentplanpayment_PaymentPlanID"]);
			$this->FixedAmount = $values["paymentplanpayment_FixedAmount"];
			$this->VariableAmount = $values["paymentplanpayment_VariableAmount"];
			$this->IntervalValue = $values["paymentplanpayment_IntervalValue"];
			$this->IntervalType = $values["paymentplanpayment_IntervalType"];
		}
	}
?>