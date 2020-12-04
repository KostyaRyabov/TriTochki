<?php
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Проверка и декодирование токена
	$return = userData();
	
	// В случае ошибки удаляем куку и выводим ошибку
	if($return === false or $return[0] === false){
		unset($_COOKIE["token"]);
		setcookie("token", "", time() - 3600, "/");
		exit(0);
	} else echo json_encode($return);