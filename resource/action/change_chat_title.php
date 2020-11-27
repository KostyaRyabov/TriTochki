<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	if(!$_COOKIE["id"]) exit("Unknown user!");
	if(!isset($_POST["id"])) exit("Unknown chat!");
	
	$id = intval($_POST["id"]);
	$user = intval($_COOKIE["id"]);
	$name = strval($_POST["name"]);
	
	// Проверка на соответствие пользователя его правам в чате
	$checksel = query("SELECT id_chat FROM chat WHERE id_chat=".$id." AND id_user=".$user);
	$check = mysqli_fetch_array($checksel);
	if(!$check["id_chat"]) exit("Wrong data!");
	
	DB::update("chat", ["Name" => $name], "id_chat=".$id." AND id_user=".$user);