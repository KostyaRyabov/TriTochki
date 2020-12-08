<?php
	/*!
		\file
		\brief Удаление контакта из списка у текущего пользователя
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Получение текущего пользователя
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit("Ошибка авторизации!");
	
	// Контакт к удалению
	$contact = treat(intval($_POST["contact"]));
	if(!$contact) exit("Некорректные данные!");
	
	DB::query("DELETE FROM user_contacts WHERE id_user=%d AND id_contact=%d", [$user_id, $contact]);