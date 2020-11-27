<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	if(!isset($_POST["user_id"]) or !isset($_POST["chat_id"])) exit("Wrong data!");
	
	$current = intval($_COOKIE["id"]);
	$user = intval($_POST["user_id"]);
	$chat = intval($_POST["chat_id"]);
	
	// Проверка на соответствие пользователя его правам в чате
	$checksel = query("SELECT id_chat FROM chat WHERE id_chat=".$chat." AND id_user=".$current);
	$check = mysqli_fetch_array($checksel);
	if(!$check["id_chat"]) exit("Wrong data!");
	
	// Удаление пользователя, если все без ошибок
	query("DELETE FROM chat_users WHERE id_chat=".$chat." AND id_user=".$user);