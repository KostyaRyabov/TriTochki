<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	if(!$_COOKIE["id"] > 0) exit(0);
	
	$id = intval($_COOKIE["id"]);
	$result = query("SELECT Firstname, Lastname, Login FROM user WHERE id_user=".$id);
	$row = mysqli_fetch_array($result);
	
	echo strlen($row["Firstname"]) > 0 && strlen($row["Lastname"]) > 0 ? $row["Firstname"]." ".$row["Lastname"] : $row["Login"];