<?php
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Проверка авторизации на всякий случай
	$user_data = userData();
	$id = $user_data["id"];
	if(!$id) exit(0);
	
	// Выборка конкретного пользователя
	$id = treat(intval($_POST["id"]));
	$result = DB::query("SELECT Firstname, Lastname, Login, Email, Sex FROM user WHERE id_user=%d", [$id]);
	$row = mysqli_fetch_array($result);
	
	$return = [
	 "firstName" => $row["Firstname"],
	 "lastName" => $row["Lastname"],
	 "login" => $row["Login"],
	 "email" => $row["Email"],
	 "sex" => $row["Sex"]
	];
	
	echo json_encode($return);