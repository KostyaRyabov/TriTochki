<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php");
	
	use JWT\JWT;
	
	// Проверка авторизации
	$jwt = strval(trim($_COOKIE["token"]));
	if(!strlen($jwt)) exit("Некорректный токен!");
	$user_data = JWT::decode($jwt, JWT::$public_key, array('RS256'));
	$user_data = (array)$user_data;
	$id = $user_data["id"];
	if(!$id) exit("Неавторизованный пользователь!");
	
	// Получение остальных полей
	$field = treat(strval($_POST["field"]));
	$data = treat(strval($_POST["data"]));
	
	// Приведение к названиям из базы
	if($field == "First_Name") $field = "Firstname";
	if($field == "Second_Name") $field = "Lastname";
	
	// Белый список полей, которые можно изменять
	$allowed_fields = array("Firstname", "Lastname", "Login", "Email", "Sex");
	if($allowed_fields[$field] === false) exit("Некорректное поле!");
	
	// Приведение логина и email к строчным буквам, чтобы не было повторов с разным типом букв
	if($field == "Login" or $field == "Email"){
		$field = strtolower($field);
		
		// Проверяем, не занят ли логин или email
		$res = DB::query("SELECT ".$field." FROM user WHERE ".$field."=%s", [$data]);
		$row = mysqli_fetch_array($res);
		if(strlen($row[$field]) > 1) exit("Поле ".$field." не уникально!");
	}
	
	DB::update("user", ["%s:".$field => $data], ["id_user=%d", [$id]]);