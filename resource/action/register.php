<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	// Первичные проверки полей на заполнение данных
	if(!strlen(trim($_POST["username"]))) exit("Fill username field!");
	if(!strlen(trim($_POST["password"])) or !strlen(trim($_POST["repeat_password"]))) exit("Fill password fields!");
	if(!strlen(trim($_POST["email"]))) exit("Fill email field!");
	
	// Устранение возможных уязвимостей
	$_POST = defender($_POST);
	
	if($_POST["password"] != $_POST["repeat_password"]) exit("Passwords are not same!"); // Проверка совпадения двух паролей
	if(strlen($_POST["password"]) < 8) exit("Too short password's length!"); // Не даем ввести меньше 8 символов
	
	// Проверяем, не занят ли логин
	$res = query("SELECT id_user FROM user WHERE login='".$_POST["username"]."'");
	$row = mysqli_fetch_array($res);
	if($row["id_user"]) exit("This username already exists!");
	
	DB::insert("user", [
	 "Login" => $_POST["username"],
	 "Password" => md5($_POST["password"]), //todo нужен другой метод защиты пароля
	 "Email" => $_POST["email"],
	 "Time" => time()
	]);