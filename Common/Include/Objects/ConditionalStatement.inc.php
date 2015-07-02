<?php
	namespace PhoenixSNS\Objects;
	
	\Enum::Create("PhoenixSNS\\Objects\\ConditionalStatementCombination", "Conjunction", "Disjunction");
	\Enum::Create("PhoenixSNS\\Objects\\ConditionalStatementComparison", "Equals", "Contains", "StartsWith", "EndsWith");
	
	class ConditionalStatementGroup
	{
		public $Combination;
		public $Statements;
		
		public function __construct($combination = null, $statements = null)
		{
			if ($combination == null) $combination = ConditionalStatementCombination::Conjunction;
			if ($statements == null) $statements = array();
			
			$this->Combination = $combination;
			$this->Statements = $statements;
		}
		
		public function Evaluate($callbackParams)
		{
			$retval = false;
			foreach ($this->Statements as $stmt)
			{
				$value = $stmt->Evaluate($callbackParams);
				switch ($this->Combination)
				{
					case ConditionalStatementCombination::Conjunction:
					{
						$retval &= $value;
						break;
					}
					case ConditionalStatementCombination::Disjunction:
					{
						$retval |= $value;
						break;
					}
				}
			}
			return $retval;
		}
	}
	class ConditionalStatement
	{
		public $ValueFunction;
		public $CompareTo;
		public $Comparison;
		public $InitialParameters;
		public $CaseInsensitive;
		
		public function __construct($valueFunction, $compareTo, $comparison, $initialParams, $caseInsensitive = false)
		{
			$this->ValueFunction = $valueFunction;
			$this->CompareTo = $compareTo;
			$this->Comparison = $comparison;
			$this->InitialParameters = $initialParams;
			$this->CaseInsensitive = $caseInsensitive;
		}
		
		public function Evaluate($callbackParams)
		{
			$value = call_user_func($this->ValueFunction, $this->InitialParameters, $callbackParams);
			
			$haystack = $value;
			$needle = $this->CompareTo;
					
			switch ($this->Comparison)
			{
				case ConditionalStatementComparison::Equals:
				{
					return ($haystack == $needle);
				}
				case ConditionalStatementComparison::Contains:
				{
					if ($this->CaseInsensitive)
					{
						$i = stripos($haystack, $needle);
					}
					else
					{
						$i = strpos($haystack, $needle);
					}
					return ($i !== 0);
				}
				case ConditionalStatementComparison::StartsWith:
				{
					if ($this->CaseInsensitive)
					{
						$length = strlen($needle);
						return (strtolower(substr($haystack, 0, $length)) === strtolower($needle));
					}
					else
					{
						$length = strlen($needle);
						return (substr($haystack, 0, $length) === $needle);
					}
				}
				case ConditionalStatementComparison::EndsWith:
				{
					if ($this->CaseInsensitive)
					{
						$length = strlen($needle);
						if ($length == 0) return true;

						return (strtolower(substr($haystack, -$length)) === strtolower($needle));
					}
					else
					{
						$length = strlen($needle);
						if ($length == 0) return true;

						return (substr($haystack, -$length) === $needle);
					}
				}
			}
		}
	}
?>