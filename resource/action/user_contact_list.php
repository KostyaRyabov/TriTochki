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
	
	// Получение и сборка контактов
	$contactsel = DB::query("
		SELECT id_contact, Firstname, Lastname, Login
		FROM user_contacts
		INNER JOIN user ON user_contacts.id_contact=user.id_user
		WHERE user_contacts.id_user=%d
	", [$user_id]);
	while($contact = mysqli_fetch_array($contactsel))
		if(strlen($contact["Firstname"]) > 0 and strlen($contact["Lastname"]) > 0) // Если есть имя и фамилия, то выводим их
			$contacts[$contact["id_contact"]] = $contact["Firstname"]." ".$contact["Lastname"];
		else $contacts[$contact["id_contact"]] = $contact["login"];
	
	echo json_encode($contacts);