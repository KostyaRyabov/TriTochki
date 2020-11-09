<?php
	// Подключение базы и файла с функциями
	include($_SERVER["DOCUMENT_ROOT"]."/db.php");
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	
	// Первичные проверки полей на заполнение данных
	if(!strlen(trim($_POST["username"]))) exit("Fill username field!");
	if(!strlen(trim($_POST["password"]))) exit("Fill password field!");
	
	// Устранение возможных уязвимостей
	$_POST = defender($_POST);
	
	// Приведение логина к строчным буквам, чтобы не было повторов с разным типом букв
	$_POST["username"] = strtolower($_POST["username"]);
	
	if(strlen($_POST["password"]) < 8) exit("Too short password's length!"); // Не даем ввести меньше 8 символов
	
	// Проверка на корректность логина и пароля
	$res = query("SELECT id_user, Password FROM user WHERE login='".$_POST["username"]."'");
	$row = mysqli_fetch_array($res);
	if($row["Password"] != md5($_POST["password"])) exit("Wrong username or password!"); //todo такой же другой метод защиты пароля, как при регистрации
	
	// Установка куки пользователя
	setcookie("id", $row["id_user"], time() + 3600, "/"); //todo в этом месте поработать над безопасностью