<?php
	/*!
	  \file
	  \brief Установка нового пароля пользователя после запроса восстановления
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Инициализация и обработка полей
	$password = treat(trim($_POST["password"]));
	$repeat = treat(trim($_POST["repeat_password"]));
	$code = treat(trim(strval($_POST["code"])));
	
	// Первичные проверки полей на заполнение данных
	if(!strlen($password)) exit(json_encode(["error" => "Введите пароль!"]));
	if(!strlen($repeat)) exit(json_encode(["error" => "Введите пароль еще раз!"]));
	if(!strlen($code)) exit(json_encode(["error" => "Ошибка запроса..."]));
	
	// Не даем ввести меньше 8 символов
	if(strlen($password) < 8) exit(json_encode(["error" => "Длина пароля должна быть не менее 8-ми символов!"]));
	
	// Проверка совпадения двух паролей
	if($password != $repeat) exit(json_encode(["error" => "Пароли не совпадают!"]));
	
	// Обновляем пароль у пользователя, которому подходит код
	DB::update(
	 "user",
	 ["%s:Password" => password_hash($password, PASSWORD_DEFAULT), "%s:restore_code" => NULL],
	 ["restore_code=%s", [$code]]
	);