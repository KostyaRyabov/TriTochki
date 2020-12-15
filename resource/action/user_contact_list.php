<?php
	/*!
		\file
		\brief Получение списка контактов пользователя
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Получение текущего пользователя
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit("Ошибка авторизации!");
	
	// Флаг на обычный список или список для добавления в чат
	$flag = $_POST["flag"] == "delete" ? "delete" : "add";
	$chat_id = treat(intval($_POST["chat"]));
	
	// Получение и сборка контактов
	if($flag == "delete")
		$contactsel = DB::query("
			SELECT id_contact, Firstname, Lastname, Login
			FROM user_contacts
			INNER JOIN user ON user_contacts.id_contact=user.id_user
			WHERE user_contacts.id_user=%d
		", [$user_id]);
	else if($flag == "add" and $chat_id)
		$contactsel = DB::query("
			SELECT id_contact, Firstname, Lastname, Login
			FROM user_contacts
			INNER JOIN user ON user_contacts.id_contact=user.id_user
			WHERE user_contacts.id_user=%d AND user_contacts.id_contact NOT IN (SELECT id_user FROM chat_users WHERE id_chat=%d)
		", [$user_id, $chat_id]);
	else exit("Ошибка запроса...");
	
	while($contact = mysqli_fetch_array($contactsel))
		if(strlen($contact["Firstname"]) > 0 and strlen($contact["Lastname"]) > 0) // Если есть имя и фамилия, то выводим их
			$contacts[$contact["id_contact"]] = $contact["Firstname"]." ".$contact["Lastname"];
		else $contacts[$contact["id_contact"]] = $contact["Login"];
	
	echo json_encode($contacts);