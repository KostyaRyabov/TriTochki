<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php");
	
	use JWT\JWT;
	
	// Получение текущего пользователя
	$jwt = strval(trim($_COOKIE["token"]));
	if(!strlen($jwt)) exit("Некорректный токен!");
	$user_data = JWT::decode($jwt, JWT::$public_key, array('RS256'));
	$user_data = (array)$user_data;
	$user = $user_data["id"];
	if(!$user) exit("Ошибка авторизации!");
	
	// Проверка остальных параметров
	$id = treat(intval($_POST["id"]));
	if(!$id) exit("Некорректно указан чат!");
	$name = treat(strval($_POST["name"]));
	
	// Проверка на соответствие пользователя его правам в чате
	$checksel = DB::query("SELECT id_chat FROM chat WHERE id_chat=%d AND id_user=%d", [$id, $user]);
	$check = mysqli_fetch_array($checksel);
	if(!$check["id_chat"]) exit("Ошибка запроса!");
	
	DB::update("chat", ["%s:Name" => $name], ["id_chat=%d AND id_user=%d", [$id, $user]]);