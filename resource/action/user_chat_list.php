<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	// Получение текущего пользователя
	$id = intval($_COOKIE["id"]);
	if(!$id) exit("Wrong user id!");
	
	$chats = array(); // Чаты пользователя
	
	// Выборка чатов, в которых состоит пользователь
	$sel = query("
		SELECT chat.id_chat, Name
		FROM chat_users
		INNER JOIN chat ON chat_users.id_chat=chat.id_chat
		WHERE chat_users.id_user=".$id
	);
	while($row = mysqli_fetch_array($sel)) $chats[$row["id_chat"]] = $row["Name"];
	
	echo json_encode($chats);