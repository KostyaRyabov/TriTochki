<?php
	// Подключение необходимых файлов
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php");
	
	use JWT\JWT;
	
	// Проверка авторизации на всякий случай
	$jwt = strval(trim($_COOKIE["token"]));
	if(!strlen($jwt)) exit(0);
	$user_data = JWT::decode($jwt, JWT::$public_key, array('RS256'));
	$user_data = (array)$user_data;
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