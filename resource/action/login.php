<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	// Инициализация и обработка полей
	$username = treat(trim($_POST["username"]));
	$password = treat(trim($_POST["password"]));
	$errors = array(); // Массив для сбора ошибок
	
	// Первичные проверки полей на заполнение данных
	if(!strlen($username)) $errors["username"] = "Введите логин!";
	if(!strlen($password)) $errors["password"] = "Введите пароль!";
	
	// Приведение логина к строчным буквам, чтобы не было повторов с разным типом букв
	$username = strtolower($username);
	
	// Не даем ввести меньше 8 символов
	if(strlen($password) > 0 and strlen($password) < 8)
		$errors["password"] = "Длина пароля должна быть не менее 8-ми символов!";
	
	// Если уже есть ошибки, прерываем выполнение, чтобы не дергать базу лишний раз
	if(count($errors)) exit(json_encode($errors));
	
	// Проверка на корректность логина и пароля
	$res = DB::query("SELECT id_user, Password FROM user WHERE login=%s", [$username]);
	$row = mysqli_fetch_array($res);
	if(!password_verify($password, $row["Password"])){
		$errors["username"] = "Некорректные данные для входа!";
		$errors["password"] = "Некорректные данные для входа!";
		exit(json_encode($errors));
	}
	
	// Установка куки пользователя
	setcookie("id", $row["id_user"], time() + 3600, "/"); //todo в этом месте поработать над безопасностью