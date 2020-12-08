<?php
	/*!
		\file
		\brief Принятие заявки на вступление в чат
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Ид текущего чата
	$chat = treat(intval($_POST["chat_id"]));
	if(!$chat) exit("Wrong chat id!");
	
	// Ид принимаемого пользователя
	$user = treat(intval($_POST["user_id"]));
	if(!$user) exit("Wrong user id!");
	
	// Получение текущего пользователя
	$user_data = userData();
	$current_user = $user_data["id"];
	if(!$current_user) exit("Ошибка авторизации!");
	
	$checksel = DB::query("SELECT id_user FROM chat WHERE id_chat=%d", [$chat]);
	$check = mysqli_fetch_array($checksel);
	if($check["id_user"] != $current_user) exit("Вы не авторизованы для данного действия!");
	
	DB::insert("chat_users", ["%d:id_chat" => $chat, "%d:id_user" => $user]);
	DB::query("DELETE FROM chat_knocked WHERE id_chat=%d AND id_user=%d", [$chat, $user]);