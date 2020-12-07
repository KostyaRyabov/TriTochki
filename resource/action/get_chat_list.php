<?php
	/*!
	  \file
	  \brief Получение списка всех чатов сайта или конкретно для пользователя
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	if($_GET["for-user"] == 1){
		// Получение текущего пользователя
		$user_data = userData();
		$id = $user_data["id"];
		$where = "WHERE chat_users.id_user=%d";
	} else $where = "";
	
	$chats = array(); // Чаты пользователя
	
	// Выборка чатов, в которых состоит пользователь
	$sel = DB::query("
		SELECT chat.id_chat, Name
		FROM chat_users
		INNER JOIN chat ON chat_users.id_chat=chat.id_chat ".$where,
	 [$id]
	);
	while($row = mysqli_fetch_array($sel)) $chats[$row["id_chat"]] = $row["Name"];
	
	echo json_encode($chats);