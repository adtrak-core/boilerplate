<?php

	class MBP_Sensible_To_Google_Arbitrary {
		
		private $base;
		private $units;
		private $nanos;
		private $negative = false;
		
		public function __construct($value){
			$this->base = (float)$value;
		}
		
		public function format(){
			$value = number_format($this->base, 2, '.', '');
			
			list($this->units, $decimal) = explode('.', $value);
			
			$this->units = (int)$this->units;
			$decimal = (int)$decimal;

			if($this->units < 0){
				$decimal = -$decimal;
				$this->negative = true;
			}
			
			$this->nanos = $decimal * pow(10, 7);
		}
		
		public function isNegative(){
			return $this->negative;
		}
		
		public function getUnits(){
			return $this->units;
		}
		
		public function getNanos(){
			return $this->nanos;
		}
	}