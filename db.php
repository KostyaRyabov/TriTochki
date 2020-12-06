<?php
	/*!
	  \file
	  \brief Подключение и методы работы с базой данных
	*/
	
	class DB{
		public static $link;
		
		private static $host = "";
		private static $user = "";
		private static $pass = "";
		private static $db = "";
		
		/**
		 * Метод подключения к базе данных
		 */
		public static function connect(){
			if(empty(self::$link)){
				self::$link = @mysqli_connect(self::$host, self::$user, self::$pass, self::$db);
				mysqli_set_charset(self::$link, 'utf8');
			}
		}
		
		/**
		 * Запрос к базе данных с плейсхолдерами
		 *
		 * Запрос к базе данных с использованием плейсхолдеров в соответствии с функцией sprintf()
		 * Документация по sprintf(): {@link http://www.php.su/sprintf()}
		 *
		 * @param string $sql запрос к базе данных: SELECT field, ... FROM table (JOIN...) WHERE field1=%s AND|OR|... field2=%d
		 * @param array $args массив аргументов, последовательно применяемых к плейсхолдерам
		 * @return bool|mysqli_result
		 */
		public static function query($sql, $args = array()){
			// Если нет аргументов, проверяем, должны ли быть
			if(!count($args) and preg_match("/%/", $sql)) return false;
			
			// Преобразование строкового аргумента в строку
			$sql = str_replace("%s", "'%s'", $sql);
			
			// Если есть аргументы, форматируем строку. В противном случае просто устраняем пробелы
			if(count($args) > 0){
				foreach($args as $key => $arg) // Обработка аргументов
					$args[$key] = mysqli_real_escape_string(self::$link, $arg);
				$sql = vsprintf(trim($sql), $args);
			} else $sql = trim($sql);
			
			if(!$sql or !strlen($sql)) return false; // Финальная проверка запроса
			
			$result = mysqli_query(self::$link, $sql) or trigger_error("db: ".mysqli_error(self::$link)." in ".$sql);
			
			return $result;
		}
		
		/**
		 * Операция добавления значений в таблицу БД
		 *
		 * Операция добавления в таблицу значений с указанием плейсхолдеров
		 * Плейсхолдеры идут в столбцах с разделителем ":"
		 * Например, $insert[%s:столбец_таблицы] = значение
		 *
		 * @param string $table конечная таблица
		 * @param array $insert массив значений с ключами-именами столбцов и плейсхолдерами
		 */
		public static function insert($table, $insert){
			// Обработка полей и значений для добавления в базу данных
			foreach($insert as $field => $value){
				// Разбор плейсхолдера и столбца по разделителю
				$field = explode(":", $field);
				
				// Строка запроса будет иметь вид: INSERT INTO `table` (`field`,...) VALUES (%s,%d,...)
				$fields .= "`".trim($field[1])."`,";
				$values .= trim($field[0]).",";
				$args[] = trim($value); // Значения идут отдельно, как аргументы
			}
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);
			
			$sql = "INSERT INTO `".$table."` (".$fields.") VALUES (".$values.")";
			
			self::query($sql, $args);
		}
		
		/**
		 * Операция обновления в таблице БД
		 *
		 * Операция обновления в таблице $table значений с указанием плейсхолдеров
		 * Плейсхолдеры идут в столбцах с разделителем ":"
		 * Например, $update[%s:столбец_таблицы] = значение
		 * $where принимает массив, элементы которого:
		 *   0 => строка запроса в части условия, как в методе query, 1 => массив аргументов по порядку
		 *
		 * @param string $table конечная таблица
		 * @param array $update массив значений, как и в {@see DB::insert()}
		 * @param array $where массив условий, где: 0 => строка условия, 1 => массив аргументов
		 */
		public static function update($table, $update, $where){
			$sql = "UPDATE `".$table."` SET";
			
			foreach($update as $field => $value){
				// Разбор плейсхолдера и столбца по разделителю
				$field = explode(":", $field);
				
				// Строка запроса будет иметь вид: UPDATE `table` SET `field=%s`, ...
				$sql .= " `".trim($field[1])."`=".$field[0].",";
				$args[] = trim($value); // Значения идут отдельно, как аргументы
			}
			$sql = substr($sql, 0, -1);
			
			$sql .= " WHERE ".trim($where[0]); // Добавление условия с плейсхолдерами к строке запроса
			foreach($where[1] as $arg) $args[] = $arg; // Добавление аргументов из условия к остальным
			
			// Конечный вид строки запроса: UPDATE `table` SET `field=%s`, ... WHERE field1=%d ...
			DB::query($sql, $args);
		}
		
		/**
		 * Получение id с auto_increment после последней вставки
		 *
		 * @return int
		 */
		public static function lastInsertId(){
			return mysqli_insert_id(self::$link);
		}
	}
	
	DB::connect();
	
	/**
	 * Можно провести любой обычный SQL-запрос для удобства
	 *
	 * @param string $sql
	 * @return bool|mysqli_result
	 */
	function query($sql){
		$result = mysqli_query(DB::$link, $sql)
		or die(mysqli_error(DB::$link));
		
		return $result;
	}
   