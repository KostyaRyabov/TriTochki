<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php");
	
	use JWT\JWT;
	
	// Получение текущего пользователя и остальных данных
	$jwt = strval(trim($_COOKIE["token"]));
	if(!strlen($jwt)) exit("Некорректный токен!");
	$user_data = JWT::decode($jwt, JWT::$public_key, array('RS256'));
	$user_data = (array)$user_data;
	$current = $user_data["id"];
	if(!$current) exit("Ошибка авторизации!");
	
	$user = treat(intval($_POST["user_id"]));
	$chat = treat(intval($_POST["chat_id"]));
	
	if(!$user or !$chat) exit("Некорректные данные!");
	
	// Проверка на соответствие пользователя его правам в чате
	$checksel = DB::query("SELECT id_chat FROM chat WHERE id_chat=%d AND id_user=%d", [$chat, $current]);
	$check = mysqli_fetch_array($checksel);
	if(!$check["id_chat"]) exit("Ошибка запроса!");
	
	// Удаление пользователя, если все без ошибок
	DB::query("DELETE FROM chat_users WHERE id_chat=%d AND id_user=%d", [$chat, $user]);