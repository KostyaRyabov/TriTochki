<?php
	/*!
	  \file
	  \brief Получение списка всех чатов сайта или конкретно для пользователя
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	$args = array(); // Параметры для условия
	
	// Если есть подстрока, ищем по ней название
	$substr = treat(strval(trim($_POST["substr"])));
	if(strlen($substr) > 0){
		$sql = "AND Name LIKE %s ";
		$args[] = "%".$substr."%";
	}
	
	if($_POST["for-user"] == 1){
		// Получение текущего пользователя
		$user_data = userData();
		$id = $user_data["id"];
		$sql .= "AND chat_users.id_user=%d ";
		$args[] = $id;
	} else $sql .= "GROUP BY Name";
	
	$chats = array(); // Список чатов
	
	// Выборка чатов
	$sel = DB::query("
		SELECT chat.id_chat, Name
		FROM chat
		LEFT JOIN chat_users ON chat.id_chat=chat_users.id_chat
		WHERE 1=1 ".$sql,
	 $args);
	while($row = mysqli_fetch_array($sel)) $chats[$row["id_chat"]] = $row["Name"];
	
	echo json_encode($chats);