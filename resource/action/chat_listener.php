<?php
	/*!
	  \file
	  \brief Прослушивание новых сообщений в чате
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Ид текущего чата
	$chat_id = treat(intval($_POST["chat"]));
	if(!$chat_id) exit(json_encode(["error" => "Wrong chat id!"]));
	
	// Сперва проверка на новые сообщения, чтобы лишний раз не грузить базу
	$count = DB::query("SELECT id_message FROM message WHERE id_chat=%d", [$chat_id]);
	if(mysqli_num_rows($count) == $_POST["current_count"]) exit(json_encode(["error" => "Новых сообщений нет."]));
	
	// Получение текущего пользователя
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit(json_encode(["error" => "Ошибка авторизации!"]));
	
	// Проверка на вхождение пользователя в данный чат
	$checksel = DB::query("SELECT * FROM chat_users WHERE id_chat=%d AND id_user=%d", [$chat_id, $user_id]);
	if(!mysqli_num_rows($checksel)) exit(json_encode(["error" => "Ошибка авторизации!"]));
	
	// Выборка всех сообщений в чате
	$messagesel = DB::query("
		SELECT id_message, message.id_user, content, data_create, Firstname, Lastname, Login
		FROM message
		INNER JOIN user ON message.id_user=user.id_user
		WHERE id_chat=%d
	", [$chat_id]);
	while($message = mysqli_fetch_array($messagesel)){
		if(strlen($message["Firstname"]) > 0 and strlen($message["Lastname"]) > 0) // Если есть имя и фамилия, то выводим их
			$user_name = $message["Firstname"]." ".$message["Lastname"];
		else $user_name = $message["login"];
		
		$messages[$message["id_message"]] = [
		 "user" => ["id" => $message["id_user"], "name" => $user_name],
		 "text" => $message["content"],
		 "date" => humanDate($message["data_create"])
		];
	}
	
	echo json_encode(["messages" => $messages, "count" => count($messages)]);
