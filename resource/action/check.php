<?php
	// Подлючение нужных файлов для корректной работы
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php");
	
	use JWT\JWT;
	
	// Проверка и декодирование токена
	$jwt = strval(trim($_COOKIE["token"]));
	if(!strlen($jwt)) exit(0);
	$return = JWT::decode($jwt, JWT::$public_key, array('RS256'));
	$return = (array)$return;
	
	// В случае ошибки удаляем куку и выводим ошибку
	if($return[0] === false){
		unset($_COOKIE["token"]);
		setcookie("token", "", time() - 3600, "/");
		exit(0);
	} else echo json_encode($return);