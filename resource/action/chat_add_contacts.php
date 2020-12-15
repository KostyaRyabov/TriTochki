<?php
	/*!
	  \file
	  \brief Добавление контактов в чат
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Проверка авторизации
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit("Неавторизованный пользователь!");
	
	// Идентификатор чата
	$chat_id = treat(intval($_POST["chat"]));
	if(!$chat_id) exit("Некорректно указан чат!");
	
	// Контакты к добавлению
	$contacts = $_POST["contacts"];
	
	// Проверка на соответствие прав пользователя в чате
	$checksel = DB::query("SELECT id_chat FROM chat WHERE id_chat=%d AND id_user=%d", [$chat_id, $user_id]);
	$check = mysqli_fetch_array($checksel);
	if(!$check["id_chat"]) exit("Ошибка запроса!");
	
	foreach($contacts as $uid){
		// Проверка контакта на вхождение в список друзей текущего пользователя и в добавляемый чат
		$check_contact = DB::query("SELECT id FROM user_contacts WHERE id_user=%d AND id_contact=%d", [$user_id, $uid]);
		$check_chat = DB::query("SELECT id_chat FROM chat_users WHERE id_chat=%d AND id_user=%d", [$chat_id, $uid]);
		if(!mysqli_num_rows($check_contact) or mysqli_num_rows($check_chat)) continue;
		else DB::insert("chat_users", ["%d:id_chat" => $chat_id, "%d:id_user" => $uid]);
	}