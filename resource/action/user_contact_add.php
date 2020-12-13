<?php
	/*!
		\file
		\brief Добавление контакта к пользователю (создание пары id в базе)
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Получение текущего пользователя
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit("Ошибка авторизации!");
	
	// Контакт к добавлению
	$contact = treat(strval($_POST["contact"]));
	if(!$contact) exit("Некорректные данные!");
	
	// Получение идентификатора контакта по логину
	$idsel = DB::query("SELECT id_user FROM user WHERE Login=%s", [$contact]);
	$id = mysqli_fetch_array($idsel);
	if(!$id["id_user"]) exit("Некорректные данные!");
	
	// Проверка на существование контакта в списке друзей у текущего пользователя
	$checksel = DB::query("SELECT id FROM user_contacts WHERE id_user=%d AND id_contact=%d", [$user_id, $id["id_user"]]);
	$check = mysqli_fetch_array($checksel);
	if($check["id"]) exit("Контакт уже есть в списке!");
	
	DB::insert("user_contacts", ["%d:id_user" => $user_id, "%d:id_contact" => $id["id_user"]]);