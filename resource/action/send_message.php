<?php
	// Подключение необходимых файлов
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php");
	
	use JWT\JWT;
	
	// Получение текущего пользователя
	$jwt = strval(trim($_COOKIE["token"]));
	if(!strlen($jwt)) exit(json_encode(["error" => "Некорректный токен!"]));
	$user_data = JWT::decode($jwt, JWT::$public_key, array('RS256'));
	$user_data = (array)$user_data;
	$user = $user_data["id"];
	
	// Получение чата, откуда отправлено сообщение
	$chat = treat(intval($_POST["chat"]));
	if(!$chat) exit(json_encode(["error" => "Некорректный чат!"]));
	
	// Получение текста
	$text = treat(strval($_POST["text"]));
	
	$new_msg = [
	 "%d:id_chat" => $chat,
	 "%d:id_user" => $user,
	 "%s:content" => $text,
	 "%s:data_create" => date("Y-m-d H:i:s")
	];
	
	DB::insert("message", $new_msg);
	$id_message = DB::lastInsertId();
	DB::insert("message_status", ["%d:id_message" => $id_message, "%d:id_user" => $user, "%d:id_read" => 0]);
	
	echo json_encode(["message_id" => $id_message, "date" => humanDate(date("Y-m-d H:i:s"))]);