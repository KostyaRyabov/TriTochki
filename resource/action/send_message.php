<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	$_POST = defender($_POST);
	
	// Получение текущего пользователя
	$user = intval($_COOKIE["id"]);
	if(!$user) exit(json_encode(["error" => "Wrong user id!"]));
	
	// Получение чата, откуда отправлено сообщение
	$chat = intval($_POST["chat"]);
	if(!$chat) exit(json_encode(["error" => "Wrong chat id!"]));
	
	// Получение текста
	$text = strval($_POST["text"]);
	if(strlen($text) < 10) exit(json_encode(["error" => "Text too short!"]));
	
	$new_msg = [
	 "id_chat" => $chat,
	 "id_user" => $user,
	 "content" => $text,
	 "data_create" => date("Y-m-d H:i:s")
	];
	
	DB::insert("message", $new_msg);
	$id_message = DB::lastInsertId();
	DB::insert("message_status", ["id_message" => $id_message, "id_user" => $user, "id_read" => 0]);
	
	echo json_encode(["message_id" => $id_message, "date" => humanDate(date("Y-m-d H:i:s"))]);