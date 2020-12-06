<?php
	/*!
	  \file
	  \brief Обработка и отправка нового сообщения в чат
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Получение текущего пользователя
	$user_data = userData();
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