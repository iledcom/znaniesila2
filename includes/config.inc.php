<?php
	//Зададим значения констант live и contact_email.
	if(!defined('LIVE')) DEFINE('LIVE', false);
	DEFINE('CONTACT_EMAIL', 'korenev@iled.com.ua');
	DEFINE('BASE_URI', 'D:/OSPanel/domains/znaniesila/'); 
	DEFINE('BASE_URL', 'znaniesila/');
  DEFINE('MYSQL', BASE_URI . 'mysql.inc.php');
  DEFINE('PDFS_DIR', BASE_URI . 'pdfs/');

	// Запуск сеанса
  session_start();

  //функция обработки ошибок
  function my_error_handler($e_number, $e_message, $e_file, $e_line, $e_vars) {
  	// Создание сообщения об ошибке
  	$message = "Ошибка произошла в сценарии '$e_file', в строке $e_line:\n$e_message\n";

  	// Добавление обратной трассировки
  	$message .= "<pre>" .print_r(debug_backtrace(), 1) . "</pre>\n";

  	// Либо просто добавляется $e_vars в сообщение
		//	$message .= "<pre>" . print_r ($e_vars, 1) . "</pre>\n";

  	//Если сайт не функционирует, то в окне браузера будет выведено сообщение об ошибке

  	if (!LIVE) {
			echo '<div class="alert alert-danger">' . nl2br($message) . '</div>';
		} else {
			// Отправка сообщения об ошибке
			error_log($message, 1, CONTACT_EMAIL,'From:korenev@iled.com.ua');

		// Вывод сообщения об ошибке в окне браузера
			if ($e_number != E_NOTICE) {
				echo '<div class="alert аlеrt-danger">Произошла системная ошибка. Приносим извинения за доставленные неудобства.</div>';
			}

		} // завершение условного выражения $live IF-ELSE return true;
	} // завершение определения функции my_error_handler()


	// Использование собственного обработчика ошибок
	set_error_handler('my_error_handler'); 

	// ************ УПРАВЛЕНИЕ ОШИБКАМИ ************ //
// ****************************************** //

// ******************************************* //
// ************ ФУНКЦИЯ ПЕРЕНАПРАВЛЕНИЯ ************ //

// Эта функция перенаправляет некорректных пользователей
// Она принимает два аргумента: 
// - проверяется элемент сеанса
// - местоположение, в которое перенаправляется пользователь 
function redirect_invalid_user($check = 'user_id', $destination = 'index.php', $protocol = 'http://') {
	
	// Проверка элемента сеанса
	if (!isset($_SESSION[$check])) {
		$url = $protocol . BASE_URL . $destination; // определение URL-ссылки
		header("Location: $url");
		exit(); // выход из сценария
	}
	
} // Завершение описания функции redirect_invalid_user()

// ************ ФУНКЦИЯ ПЕРЕНАПРАВЛЕНИЯ ************ //
// ******************************************* //

// Пропуск закрывающего тега PHP во избежание ошибок 'headers are sent'!