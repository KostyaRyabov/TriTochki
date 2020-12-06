<?php
	/*!
	  \file
	  \brief Получение списка чатов пользователя
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Получение текущего пользователя
	$user_data = userData();
	$id = $user_data["id"];
	
	$chats = array(); // Чаты пользователя
	
	// Выборка чатов, в которых состоит пользователь
	$sel = DB::query("
		SELECT chat.id_chat, Name
		FROM chat_users
		INNER JOIN chat ON chat_users.id_chat=chat.id_chat
		WHERE chat_users.id_user=%d
	", [$id]);
	while($row = mysqli_fetch_array($sel)) $chats[$row["id_chat"]] = $row["Name"];
	
	echo json_encode($chats);