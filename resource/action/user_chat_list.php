<?php
	// Подключение необходимых файлов
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php");
	
	use JWT\JWT;
	
	// Получение текущего пользователя
	$jwt = strval(trim($_COOKIE["token"]));
	if(!strlen($jwt)) exit("Некорректный токен!");
	$user_data = JWT::decode($jwt, JWT::$public_key, array('RS256'));
	$user_data = (array)$user_data;
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