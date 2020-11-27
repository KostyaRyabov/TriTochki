<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	if(!$_COOKIE["id"] > 0) exit(0); // Проверка авторизации на всякий случай
	
	$_POST = defender($_POST);
	
	$id = $_POST["id"];
	$result = query("SELECT Firstname, Lastname, Login, Email, Sex FROM user WHERE id_user=".$id);
	$row = mysqli_fetch_array($result);
	
	$return = [
	 "firstName" => $row["Firstname"],
	 "lastName" => $row["Lastname"],
	 "login" => $row["Login"],
	 "email" => $row["Email"],
	 "sex" => $row["Sex"]
	];
	
	echo json_encode($return);