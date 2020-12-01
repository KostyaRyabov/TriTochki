<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php");
	
	use JWT\JWT;
	
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
	 "%s:Password" => password_hash($password, PASSWORD_DEFAULT),
	 "%s:Email" => $email,
	 "%d:Time" => time()
	]);
	
	// Получаем нового пользователя и устанавливаем токен
	$new_user = DB::lastInsertId();
	$res = DB::query("SELECT * FROM user WHERE id_user=%d", [$new_user]);
	$row = mysqli_fetch_array($res);
	
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