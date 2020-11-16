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
	
	// Перевод даты и времени из mysql формата в человекопонятную
	function humanDate($dtm){
		$dtm = explode(" ", $dtm);
		
		$dt = explode("-", $dtm[0]);
		$tm = explode(":", $dtm[1]);
		
		return $dt[2].".".$dt[1].".".$dt[0]." в ".$tm[0].":".$tm[1];
	}