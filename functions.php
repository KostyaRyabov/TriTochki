<?php
	// Обработка переданных значений на xss-уязвимости и sql-инъекции
	function defender($method = array()){
		foreach($method as $key => $value){
			$value = preg_replace("/script/i", "", $value);
			$value = preg_replace("/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/ix", "", $value);
			$value = strip_tags($value);
			$value = htmlentities($value, ENT_QUOTES);
			
			$method[$key] = $value;
		}
		
		return $method;
	}