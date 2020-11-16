<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	// Ид текущего чата
	$id = intval($_GET["id"]);
	if(!$id) exit("Wrong chat id!");
	
	// Получение текущего пользователя
	$user_id = intval($_COOKIE["id"]);
	if(!$user_id) exit("Wrong user id!");
	
	// Выборка нужного чата
	$sel = query("SELECT Name, date_create FROM chat WHERE id_chat=".$id);
	$row = mysqli_fetch_array($sel);
	
	// Выборка всех участников чата
	$usersel = query("
		SELECT user.id_user, Firstname, Lastname, login
		FROM chat_users
		INNER JOIN user ON chat_users.id_user=user.id_user
		WHERE id_chat=".$id
	);
	while($user = mysqli_fetch_array($usersel))
		if(strlen($user["Firstname"]) > 0 and strlen($user["Lastname"]) > 0) // Если есть имя и фамилия, то выводим их
			$users[$user["id_user"]] = $user["Firstname"]." ".$user["Lastname"];
		else $users[$user["id_user"]] = $user["login"];
	
	// Если пользователя нет в списке участников данного чата, выводим ошибку
	if(!$users[$user_id]) exit("You dont have permissions for this room!");
	
	// Выборка всех сообщений в чате
	$messagesel = query("
		SELECT message.id_message, message.id_user, content, data_create, id_read
		FROM message
		INNER JOIN message_status ON message.id_message=message_status.id_message
		WHERE id_chat=".$id
	);
	while($message = mysqli_fetch_array($messagesel))
		$messages[$message["id_message"]] = [
		 "user" => $message["id_user"],
		 "text" => $message["content"],
		 "date" => humanDate($message["data_create"]),
		 "is_read" => $message["id_read"]
		];
	
	/* Для вложенных массивов ключами являются id этих элементов.
	 * Если данные id встречаются в других массивах, возможен поиск по ним */
	$return = [
	 "name" => $row["Name"],
	 "date" => humanDate($row["date_create"]),
	 "users" => $users,
	 "messages" => $messages
	];
	
	echo json_encode($return);