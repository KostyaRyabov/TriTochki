<?php
	/*!
	  \file
	  \brief Выход пользователя из чата
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Получение текущего пользователя
	$user_data = userData();
	$user = $user_data["id"];
	if(!$user) exit("Некорректная авторизация!");
	
	// Получение и обработка идентификатора чата
	$chat = treat(intval($_POST["chat"]));
	if(!$chat) exit("Некорректно!");
	
	// Обновление списка чатов пользователя
	$sel = DB::query("DELETE FROM chat_users WHERE id_chat=%d AND id_user=%d", [$chat, $user]);