<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	$errors = array(); // Массив для сбора ошибок
	
	// Первичные проверки полей на заполнение данных
	if(!strlen(trim($_POST["username"]))) $errors["username"] = "Fill username field!";
	if(!strlen(trim($_POST["password"]))) $errors["password"] = "Fill password field!";
	
	// Устранение возможных уязвимостей
	$_POST = defender($_POST);
	
	// Приведение логина к строчным буквам, чтобы не было повторов с разным типом букв
	$_POST["username"] = strtolower($_POST["username"]);
	
	// Не даем ввести меньше 8 символов
	if(strlen($_POST["password"]) > 0 and strlen($_POST["password"]) < 8)
		$errors["password"] = "Too short password's length!";
	
	// Если уже есть ошибки, прерываем выполнение, чтобы не дергать базу лишний раз
	if(count($errors)) exit(json_encode($errors));
	
	// Проверка на корректность логина и пароля
	$res = query("SELECT id_user, Password FROM user WHERE login='".$_POST["username"]."'");
	$row = mysqli_fetch_array($res);
	if($row["Password"] != md5($_POST["password"])){ //todo такой же другой метод защиты пароля, как при регистрации
		$errors["username"] = "Maybe wrong username?";
		$errors["password"] = "Maybe wrong password?";
		exit(json_encode($errors));
	}
	
	// Установка куки пользователя
	setcookie("id", $row["id_user"], time() + 3600, "/"); //todo в этом месте поработать над безопасностью