<?php
	/*!
	  \file
	  \brief Получение информации о пользователе
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Проверка авторизации на всякий случай
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit(0);
	
	// Выборка конкретного пользователя
	$id = treat(intval($_POST["id"]));
	$result = DB::query("SELECT Firstname, Lastname, Login, Email, Sex FROM user WHERE id_user=%d", [$id]);
	$row = mysqli_fetch_array($result);
	
	// Проверяем, есть ли этот пользователь в списке контактов у текущего
	$contact = DB::query("SELECT id FROM user_contacts WHERE id_user=%d AND id_contact=%d", [$user_id, $id]);
	
	$return = [
	 "firstName" => $row["Firstname"],
	 "lastName" => $row["Lastname"],
	 "login" => $row["Login"],
	 "email" => $row["Email"],
	 "sex" => $row["Sex"],
	 "isContact" => mysqli_num_rows($contact) ? true : false
	];
	
	echo json_encode($return);