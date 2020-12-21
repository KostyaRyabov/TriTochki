<?php
	/*!
	  \file
	  \brief Восстановление доступа пользователя с отправкой на электронную почту
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	$username = treat(strval(trim($_POST["username"])));
	
	// Берем пользователя из базы. Проверяем на существование
	$sel = DB::query("SELECT id_user, Email FROM user WHERE Login=%s", [$username]);
	$row = mysqli_fetch_array($sel);
	if(!$row["id_user"] or !$row["Email"]) exit(json_encode(["username" => "Данного пользователя не существует!"]));
	
	// Генерация кода восстановления с солью
	$salt = random_bytes(rand(8, 16));
	$code = str_shuffle(md5($salt.$row["id_user"].$row["Email"]));
	if(!strlen($code)) exit(json_encode(["username" => "Ошибка запроса..."]));
	
	DB::update("user", ["%s:restore_code" => $code], ["Login=%s", [$username]]);
	
	// Отправка сообщения на почту
	$headers = "Content-type: text/html; charset=utf-8 \r\n";
	$headers .= "From: TriTochki... <no-reply@tritochki.ru>\r\n";
	$message = "Здравствуйте! Вы запросили восстановление доступа к чату \"ТриТочки...\" Чтобы сменить пароль, проследуйте по ссылке: http://dev.russiabase.ru/Restore.html?code=".$code;
	mail($row["Email"], "Восстановление доступа", $message, $headers);