<?php
	DEFINE ('DB_USER', 'root');
	DEFINE ('DB_PASSWORD', '');
	DEFINE ('DB_HOST', 'localhost');
	DEFINE ('DB_NAME', 'znaniesila_db');

//Подключение к базе данных
	$dbc = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

//Установка кодировки

	mysqli_set_charset($dbc, 'utf8');

//функция, обеспечивающая безопасное использование данных в запросах:

	function escape_data ($data, $dbc) {
		return mysqli_real_escape_string ($dbc, trim($data));
		//trim — Удаляет пробелы (или другие символы) из начала и конца строки
	}