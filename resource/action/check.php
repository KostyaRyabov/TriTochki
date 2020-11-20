<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	if(!$_COOKIE["id"] > 0) exit(0);
	
	$id = intval($_COOKIE["id"]);
	$result = query("SELECT Firstname, Lastname, Login, Email, Sex FROM user WHERE id_user=".$id);
	$row = mysqli_fetch_array($result);
	
	$return = [
	 "firstName" => $row["Firstname"],
	 "lastName" => $row["Lastname"],
	 "login" => $row["Login"],
	 "email" => $row["Email"],
	 "sex" => $row["Sex"],
	 "myName" => strlen($row["Firstname"]) > 0 && strlen($row["Lastname"]) > 0 ? $row["Firstname"]." ".$row["Lastname"] : $row["Login"]
	];
	
	echo json_encode($return);