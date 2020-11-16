<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	$errors = array(); // Массив для сбора ошибок
	
	// Первичные проверки полей на заполнение данных
	if(!strlen(trim($_POST["username"]))) $errors["username"] = "Fill username field!";
	if(!strlen(trim($_POST["password"]))) $errors["password"] = "Fill password field!";
	if(!strlen(trim($_POST["repeat_password"]))) $errors["repeat_password"] = "Repeat your password!";
	if(!strlen(trim($_POST["email"]))) $errors["email"] = "Fill email field!";
	
	// Сразу прекращаем выполнение, если что-то из обязательного отсутствует
	if(count($errors)) exit(json_encode($errors));
	
	// Устранение возможных уязвимостей
	$_POST = defender($_POST);
	
	// Приведение логина и email к строчным буквам, чтобы не было повторов с разным типом букв
	$_POST["username"] = strtolower($_POST["username"]);
	$_POST["email"] = strtolower($_POST["email"]);
	
	// Не даем ввести меньше 8 символов
	if(strlen($_POST["password"]) < 8) $errors["password"] = "Too short password's length!";
	if(count($errors)) exit(json_encode($errors));
	
	// Проверка совпадения двух паролей
	if($_POST["password"] != $_POST["repeat_password"]){
		$errors["password"] = "Passwords are not same!";
		$errors["repeat_password"] = "Passwords are not same!";
		exit(json_encode($errors));
	}
	
	// Проверяем, не занят ли логин или email
	$res = query("SELECT Login, Email FROM user WHERE Login='".$_POST["username"]."' OR Email='".$_POST["email"]."'");
	$row = mysqli_fetch_array($res);
	if($row["Login"] == $_POST["username"]) $errors["username"] = "This username already exists!";
	if($row["Email"] == $_POST["email"]) $errors["email"] = "This email already exists!";
	
	// Выводим ошибки по последним проверкам
	if(count($errors)) exit(json_encode($errors));
	
	DB::insert("user", [
	 "Login" => $_POST["username"],
	 "Password" => md5($_POST["password"]), //todo нужен другой метод защиты пароля
	 "Email" => $_POST["email"],
	 "Time" => time()
	]);
	
	// todo далее вход пользователя (после решения вопроса безопасности с cookie "id")