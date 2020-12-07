<?php
	/*!
	  \file
	  \brief Создание нового чата
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	$name = treat(strval($_POST["name"])); // Название нового чата
	if(!$name) exit(json_encode(["error" => "Некорректное название!"]));
	
	// Получение текущего пользователя
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit(json_encode(["error" => "Ошибка авторизации!"]));
	
	$insert = [
	 "%d:id_user" => $user_id,
	 "%s:Name" => $name,
	 "%s:date_create" => date("Y-m-d H:i:s")
	];
	
	DB::insert("chat", $insert);
	$new_id = DB::lastInsertId();
	DB::insert("chat_users", ["%d:id_chat" => $new_id, "%d:id_user" => $user_id]);
	
	echo json_encode(["new_chat" => $new_id]);