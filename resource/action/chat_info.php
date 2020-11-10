<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	$id = intval($_GET["id"]);
	if(!$id) exit("Wrong chat id!");
	
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
	
	$return = [
	 "name" => $row["Name"],
	 "date" => humanDate($row["date_create"]),
	 "users" => $users
	];
	
	echo json_encode($return);