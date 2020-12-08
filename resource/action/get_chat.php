<?php
	/*!
	  \file
	  \brief Получение всей информации о запрашиваемом чате
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Ид текущего чата
	$id = treat(intval($_GET["id"]));
	if(!$id) exit("Wrong chat id!");
	
	// Получение текущего пользователя
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit("Ошибка авторизации!");
	
	// Выборка нужного чата
	$sel = DB::query("SELECT id_user, Name, date_create FROM chat WHERE id_chat=%d", [$id]);
	$row = mysqli_fetch_array($sel);
	
	// Выборка всех участников чата
	$usersel = DB::query("
		SELECT user.id_user, Firstname, Lastname, login
		FROM chat_users
		INNER JOIN user ON chat_users.id_user=user.id_user
		WHERE id_chat=%d
	", [$id]);
	while($user = mysqli_fetch_array($usersel))
		if(strlen($user["Firstname"]) > 0 and strlen($user["Lastname"]) > 0) // Если есть имя и фамилия, то выводим их
			$users[$user["id_user"]] = $user["Firstname"]." ".$user["Lastname"];
		else $users[$user["id_user"]] = $user["login"];
	
	// Если скрипт запрашивается от имени хоста, добавляем заявки
	if($row["id_user"] == $user_id){
		$knockedsel = DB::query("
			SELECT user.id_user, Firstname, Lastname, login
			FROM chat_knocked
			INNER JOIN user ON chat_knocked.id_user=user.id_user
			WHERE id_chat=%d
		", [$id]);
		while($knocked = mysqli_fetch_array($knockedsel))
			if(strlen($knocked["Firstname"]) > 0 and strlen($knocked["Lastname"]) > 0)
				$knock[$knocked["id_user"]] = $knocked["Firstname"]." ".$knocked["Lastname"];
			else $knock[$knocked["id_user"]] = $knocked["login"];
	}
	
	// Допущен ли пользователь к данному чату
	$allowed = $users[$user_id] ? 1 : 0;
	// Если не допущен, выводим минимальный объем информации
	if(!$allowed){
		$return = [
		 "owner" => $row["id_user"],
		 "name" => $row["Name"],
		 "date" => humanDate($row["date_create"]),
		 "users" => $users,
		 "messages" => [],
		 "knocked" => [],
		 "allowed" => $allowed
		];
		exit(json_encode($return));
	}
	
	// Выборка всех сообщений в чате
	$messagesel = DB::query("
		SELECT message.id_message, message.id_user, content, data_create, id_read
		FROM message
		INNER JOIN message_status ON message.id_message=message_status.id_message
		WHERE id_chat=%d
	", [$id]);
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
	 "owner" => $row["id_user"],
	 "name" => $row["Name"],
	 "date" => humanDate($row["date_create"]),
	 "users" => $users,
	 "messages" => $messages ? $messages : [],
	 "knocked" => $knock ? $knock : [],
	 "allowed" => $allowed
	];
	
	echo json_encode($return);