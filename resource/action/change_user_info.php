<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	if(!$_COOKIE["id"] > 0) exit("Unknown user!");
	
	$_POST = defender($_POST);
	
	$id = intval($_COOKIE["id"]);
	$field = $_POST["field"];
	$data = $_POST["data"];
	
	// Приведение к названиям из базы
	if($field == "First_Name") $field = "Firstname";
	if($field == "Second_Name") $field = "Lastname";
	
	// Приведение логина и email к строчным буквам, чтобы не было повторов с разным типом букв
	if($field == "Login" or $field == "Email"){
		$field = strtolower($field);
		
		// Проверяем, не занят ли логин или email
		$res = query("SELECT ".$field." FROM user WHERE ".$field."='".$data."'");
		$row = mysqli_fetch_array($res);
		if(strlen($row[$field]) > 1) exit("This ".$field." already exists!");
	}
	
	DB::update("user", [$field => $data], "id_user=".$id);