<?php
	/*!
		\file
		\brief Заявка на вступление в чат
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Ид текущего чата
	$chat = treat(intval($_POST["chat"]));
	if(!$chat) exit("Wrong chat id!");
	
	// Получение текущего пользователя
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit("Ошибка авторизации!");
	
	// Если уже постучался, выводим ошибку
	$sel = DB::query("SELECT * FROM chat_knocked WHERE id_chat=%d AND id_user=%d", [$chat, $user_id]);
	$row = mysqli_fetch_array($sel);
	if($row["id_chat"]) exit("Вы уже подали заявку на вступление в этот чат!");
	
	DB::insert("chat_knocked", ["%d:id_chat" => $chat, "%d:id_user" => $user_id]);