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
	
	// Приведение логина к строчным буквам, чтобы не было повторов с разным типом букв
	$_POST["username"] = strtolower($_POST["username"]);
	
	if($_POST["password"] != $_POST["repeat_password"]) exit("Passwords are not same!"); // Проверка совпадения двух паролей
	if(strlen($_POST["password"]) < 8) exit("Too short password's length!"); // Не даем ввести меньше 8 символов
	
	// Проверяем, не занят ли логин или email
	$res = query("SELECT Login, Email FROM user WHERE Login='".$_POST["username"]."' OR Email='".$_POST["email"]."'");
	$row = mysqli_fetch_array($res);
	if($row["Login"] == $_POST["username"]) exit("This username already exists!");
	if($row["Email"] == $_POST["email"]) exit("This email already exists!");
	
	DB::insert("user", [
	 "Login" => $_POST["username"],
	 "Password" => md5($_POST["password"]), //todo нужен другой метод защиты пароля
	 "Email" => $_POST["email"],
	 "Time" => time()
	]);