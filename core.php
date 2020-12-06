<?php
	/*!
	  \file
	  \brief Ядро проекта, подключается во всех скриптах
	*/
	
	// Раскомментировать, если понадобится вывести ошибки
	//ini_set("display_errors", 1);
	
	include($_SERVER["DOCUMENT_ROOT"]."/JWT.php"); // Подключение методов работы с токенами
	
	include($_SERVER["DOCUMENT_ROOT"]."/db.php"); // Подключение и инициализация базы данных
	
	include($_SERVER["DOCUMENT_ROOT"]."/functions.php"); // Подключение некоторых функций