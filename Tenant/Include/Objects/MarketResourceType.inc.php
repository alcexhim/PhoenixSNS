<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	
	class MarketResourceBankInfo
	{
		public $InterestRate;
		public $InterestPeriod;
		public $MinimumDeposit;
		
		public $DepositFeePercentage;
		public $DepositFeeValue;
		
		public $MinimumWithdrawal;
		
		public $WithdrawalFeePercentage;
		public $WithdrawalFeeValue;
		
		public $Enabled;
		
		public static function GetByAssoc($values)
		{
			$bankinfo = new MarketResourceBankInfo();
			$bankinfo->Enabled = ($values["bankinfo_enabled"] == "1");
			$bankinfo->InterestRate = $values["bankinfo_interest_rate"];
			$bankinfo->InterestPeriod = $values["bankinfo_interest_period"];
			$bankinfo->MinimumDeposit = $values["bankinfo_minimum_deposit"];
			$bankinfo->DepositFeePercentage = $values["bankinfo_depositfee_percentage"];
			$bankinfo->DepositFeeValue = $values["bankinfo_depositfee_value"];
			$bankinfo->MinimumWithdrawal = $values["bankinfo_minimum_withdrawal"];
			$bankinfo->WithdrawalFeePercentage = $values["bankinfo_withdrawalfee_percentage"];
			$bankinfo->WithdrawalFeeValue = $values["bankinfo_withdrawalfee_value"];
			return $bankinfo;
		}
	}
	class MarketResourceType
	{
		public $ID;
		public $Name;
		public $TitleSingular;
		public $TitlePlural;
		public $BankInfo;
		
		public function ToString()
		{
			$result = "<span class=\"ResourceDisplay\">";
			$result .= "<img alt=\"" . $this->TitlePlural . ": \" title=\"" . $this->TitlePlural . "\" class=\"ResourceIcon\" src=\"" . System::$Configuration["Application.BasePath"] . "/images/resources/24x24/" . $this->Name . ".png\" /> <span class=\"ResourceValue\">" . $this->TitlePlural . "</span>";
			$result .= "</span>";
			return $result;
		}
		
		public static function Get()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketResourceTypes";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = MarketResourceType::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByAssoc($values)
		{
			$resource = new MarketResourceType();
			$resource->ID = $values["resourcetype_ID"];
			$resource->Name = $values["resourcetype_Name"];
			$resource->TitleSingular = $values["resourcetype_TitleSingular"];
			$resource->TitlePlural = $values["resourcetype_TitlePlural"];
			
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "MarketResourceBankDetails WHERE bankdetail_ResourceTypeID = " . $resource->ID;
			global $MySQL;
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			$resource->BankInfo = MarketResourceBankInfo::GetByAssoc($values);
			
			return $resource;
		}
		public static function GetByID($id)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketResourceTypes WHERE resourcetype_ID = " . $id;
			$result = $MySQL->query($query);
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return MarketResourceType::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "MarketResource WHERE resourcetype_Name = '" . $MySQL->real_escape_string($name) . "'";
			$result = $MySQL->query($query);
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return MarketResourceType::GetByAssoc($values);
		}
	}
?>