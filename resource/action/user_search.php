<?php
	/*!
		\file
		\brief Поиск пользователей
	*/
	
	// Подключение ядра
	include($_SERVER["DOCUMENT_ROOT"]."/core.php");
	
	// Получение текущего пользователя
	$user_data = userData();
	$user_id = $user_data["id"];
	if(!$user_id) exit(["error" => "Ошибка авторизации!"]);
	
	// Подстрока для поиска по имени
	$substr = treat(strval(trim($_POST["substr"])));
	if(!strlen($substr)) exit(["error" => "Ошибка запроса!"]);
	$sql_substr = "%".$substr."%";
	
	$sel = DB::query("
		SELECT id_user, Firstname, Lastname, Login
		FROM user
		WHERE Firstname LIKE %s OR Lastname LIKE %s OR Login LIKE %s
	", [$sql_substr, $sql_substr, $sql_substr]);
	while($row = mysqli_fetch_array($sel))
		if(strlen($row["Firstname"]) > 0 and strlen($row["Lastname"]) > 0){ // Если есть имя и фамилия, то выводим их
			if(preg_match("/".$substr."/", $row["Firstname"]) or preg_match("/".$substr."/", $row["Lastname"]))
				$users[$row["id_user"]] = $row["Firstname"]." ".$row["Lastname"];
		} else $users[$row["id_user"]] = $row["Login"];
	
	echo json_encode($users);