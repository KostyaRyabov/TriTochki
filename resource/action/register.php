<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	// Инициализация и обработка полей
	$username = treat(trim($_POST["username"]));
	$password = treat(trim($_POST["password"]));
	$repeat = treat(trim($_POST["repeat_password"]));
	$email = treat(trim($_POST["email"]));
	
	$errors = array(); // Массив для сбора ошибок
	
	// Первичные проверки полей на заполнение данных
	if(!strlen($username)) $errors["username"] = "Введите логин!";
	if(!strlen($password)) $errors["password"] = "Введите пароль!";
	if(!strlen($repeat)) $errors["repeat_password"] = "Введите пароль еще раз!";
	if(!strlen($email)) $errors["email"] = "Введите email!";
	
	// Сразу прекращаем выполнение, если что-то из обязательного отсутствует
	if(count($errors)) exit(json_encode($errors));
	
	// Приведение логина и email к строчным буквам, чтобы не было повторов с разным типом букв
	$username = strtolower($username);
	$email = strtolower($email);
	
	// Не даем ввести меньше 8 символов
	if(strlen($password) < 8) $errors["password"] = "Длина пароля должна быть не менее 8-ми символов!";
	if(count($errors)) exit(json_encode($errors));
	
	// Проверка совпадения двух паролей
	if($password != $repeat){
		$errors["password"] = "Пароли не совпадают!";
		$errors["repeat_password"] = "Пароли не совпадают!";
		exit(json_encode($errors));
	}
	
	// Проверяем, не занят ли логин или email
	$res = DB::query("SELECT Login, Email FROM user WHERE Login=%s OR Email=%s", [$username, $email]);
	$row = mysqli_fetch_array($res);
	if($row["Login"] == $username) $errors["username"] = "Это имя пользователя уже занято!";
	if($row["Email"] == $email) $errors["email"] = "Этот email уже занят!";
	
	// Выводим ошибки по последним проверкам
	if(count($errors)) exit(json_encode($errors));
	
	DB::insert("user", [
	 "%s:Login" => $username,
	 "%s:Password" => md5($password), //todo нужен другой метод защиты пароля
	 "%s:Email" => $email,
	 "%d:Time" => time()
	]);
	
	// todo далее вход пользователя (после решения вопроса безопасности с cookie "id")