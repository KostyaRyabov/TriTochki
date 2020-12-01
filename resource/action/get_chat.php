<?php
	// Подключение необходимых файлов
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php");
	
	use JWT\JWT;
	
	// Ид текущего чата
	$id = treat(intval($_GET["id"]));
	if(!$id) exit("Wrong chat id!");
	
	// Получение текущего пользователя
	$jwt = strval(trim($_COOKIE["token"]));
	if(!strlen($jwt)) exit("Некорректный токен!");
	$user_data = JWT::decode($jwt, JWT::$public_key, array('RS256'));
	$user_data = (array)$user_data;
	$user_id = $user_data["id"];
	
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
	
	// Если пользователя нет в списке участников данного чата, выводим ошибку
	if(!$users[$user_id]) exit("Вы не авторизованы для данного чата!");
	
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
	 "messages" => $messages
	];
	
	echo json_encode($return);