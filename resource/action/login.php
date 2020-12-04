<?php
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	use JWT\JWT;
	
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
	$res = DB::query("SELECT * FROM user WHERE login=%s", [$username]);
	$row = mysqli_fetch_array($res);
	if(!$row["id_user"]) $errors["username"] = "Некорректный логин!";
	if(!password_verify($password, $row["Password"])) $errors["password"] = "Некорректный пароль!";
	if(count($errors)) exit(json_encode($errors));
	
	// Установка токена с нужными для дальнейшей работы параметрами
	$jwt_data = array(
	 "id" => $row["id_user"],
	 "firstName" => $row["Firstname"],
	 "lastName" => $row["Lastname"],
	 "login" => $row["Login"],
	 "email" => $row["Email"],
	 "sex" => $row["Sex"],
	 "myName" => strlen($row["Firstname"]) > 0 && strlen($row["Lastname"]) > 0 ? $row["Firstname"]." ".$row["Lastname"] : $row["Login"]
	);
	$jwt = JWT::encode($jwt_data, JWT::$private_key, 'RS256');
	
	setcookie("token", $jwt, time() + 3600, "/");