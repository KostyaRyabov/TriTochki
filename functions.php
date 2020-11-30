<?php
	// Обработка переданного значения для предупреждения уязвимостей
	function treat($var){
		$var = preg_replace("/script/i", "", $var); // Любое упоминание о кастомном js-скрипте агрессивно удаляется
		$var = preg_replace("/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/ix", "", $var);
		$var = strip_tags($var); // Обработка HTML и PHP тэгов
		$var = htmlentities($var, ENT_QUOTES); // Преобразование спец.символов в HTML сущности
		
		return $var;
	}
	
	// Перевод даты и времени из mysql формата в человекопонятную
	function humanDate($dtm){
		$dtm = explode(" ", $dtm);
		
		$dt = explode("-", $dtm[0]);
		$tm = explode(":", $dtm[1]);
		
		return $dt[2].".".$dt[1].".".$dt[0]." в ".$tm[0].":".$tm[1];
	}