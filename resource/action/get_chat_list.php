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
		$sql = "WHERE chat_users.id_user=%d";
	} else $sql = "GROUP BY Name";
	
	$chats = array(); // Список чатов
	
	// Выборка чатов
	$sel = DB::query("
		SELECT chat.id_chat, Name
		FROM chat_users
		INNER JOIN chat ON chat_users.id_chat=chat.id_chat ".$sql, [$id]
	);
	while($row = mysqli_fetch_array($sel)) $chats[$row["id_chat"]] = $row["Name"];
	
	echo json_encode($chats);