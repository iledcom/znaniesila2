<?php

	require('./includes/config.inc.php');
	require(MYSQL);
	$page_title = 'Регистрация';

	include('./includes/header.html');

	// Массив, предназначенный для хранения ошибок регистрации
	$reg_errors = array();

	// Ппроверка данных, передаваемых форме
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Проверка имени
		if (preg_match('/^([A-Z][a-z\-\']{1,50})|([А-ЯЁIЇҐЄ][а-яёіїґє\-\']{1,50})$/i', $_POST['first_name'])) {
			$fn = escape_data($_POST['first_name'], $dbc); // функция escape_data определяется в файле mysql.inc.php
		} else {
			$reg_errors['first_name'] = 'Пожалуйста, укажите свое имя!';
		}
		
		// Проверка фамилии
		if (preg_match('/^([A-Z][a-z\-\']{1,50})|([А-ЯЁIЇҐЄ][а-яёіїґє\-\']{1,50})$/i', $_POST['last_name'])) {
			$ln = escape_data($_POST['last_name'], $dbc);
		} else {
			$reg_errors['last_name'] = 'Пожалуйста, введите фамилию!';
		}
		
		// Проверка логина
		if (preg_match('/^([A-Z][a-z\-\']{1,50})|([А-ЯЁIЇҐЄ][а-яёіїґє\-\']{1,50})$/i', $_POST['username'])) {
			$u = escape_data($_POST['username'], $dbc);
		} else {
			$reg_errors['username'] = 'Пожалуйста, введите логин, используя только буквы и цифры!';
		}
		
		// Проверка адреса электронной почты
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === $_POST['email']) {
			$e = escape_data($_POST['email'], $dbc);
		} else {
			$reg_errors['email'] = 'Пожалуйста, укажите корректный адрес электронной почты!';
		}

		// Проверка введённого пароля и сравнение с подтвержденным паролем
		if (preg_match('/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,}$/', $_POST['pass1']) ) {
			if ($_POST['pass1'] === $_POST['pass2']) {
				$p = $_POST['pass1'];
			} else {
				$reg_errors['pass2'] = 'Подтвержденный пароль не совпадает с исходным!';
			}
		} else {
			$reg_errors['pass1'] = 'Пожалуйста, введите корректный пароль!';
		}
		
		if (empty($reg_errors)) { // если все хорошо...

			// Убедитесь, что адрес электронной почты и логин доступны
			$q = "SELECT email, username FROM users WHERE email='$e' OR username='$u'";
			$r = mysqli_query($dbc, $q);
		
			// Количество возвращенных строк
			$rows = mysqli_num_rows($r);
		
			if ($rows === 0) { // проблем не возникло
				
				// Добавление пользователя в базу данных...
				
				// Включение библиотеки password_compat, если нужно
				// include('./includes/lib/password.php');
				
				// Временно: установка срока действия, равного одному месяцу!
				
				 $q = "INSERT INTO users (username, email, pass, first_name, last_name, date_expires) VALUES ('$u', '$e', '"  .  password_hash($p, PASSWORD_BCRYPT) .  "', '$fn', '$ln', ADDDATE(NOW(), INTERVAL 1 MONTH) )";
				
				// Новый запрос, обновленный в главе 6 с целью интеграции с PayPal
				// Присвоить дате вчерашний день
				 /*
				$q = "INSERT INTO users (username, email, pass, first_name, last_name, date_expires) VALUES ('$u', '$e', '"  .  password_hash($p, PASSWORD_BCRYPT) .  "', '$fn', '$ln', SUBDATE(NOW(), INTERVAL 1 DAY) )";
				*/
				$r = mysqli_query($dbc, $q);

				if (mysqli_affected_rows($dbc) === 1) { // Если выполняется.
		
					// Получение ID пользователя
					// Сохранение ID нового пользователя в сеансе
					// Добавлено в главе 6
					 $uid = mysqli_insert_id($dbc);
					 $_SESSION['reg_user_id']  = $uid;		

					// Добавление благодарственного сообщения...

					// Исходное сообщение из главы 4
					echo '<div class="alert alert-success"><h3>Спасибо!</h3><p>Благодарим за регистрацию! Теперь вы сможете войти на сайт и получить доступ к его содержимому.</p></div>';

					// Измененное в главе 6 сообщение
					//echo '<div class="alert alert-success"><h3>Спасибо!</h3><p>Спасибо за регистрацию! Чтобы завершить этот процесс, щелкните на отображенной внизу кнопке, чтобы оплатить доступ к сайту с помощью PayPal. Стоимость доступа составляет 650 рублей в год (10 долларов США по текущему курсу). <strong>Примечание. После завершения платежа в PayPal щелкните на соответствующей кнопке, чтобы вернуться на сайт.</strong></p></div>';

					// Кнопка PayPal добавлена в главе 6
					 /*echo '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="AEM2YK8MV95BG">
						<input type="image" src="https://www.sandbox.paypal.com/ru_RU/RU/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal — более безопасный и легкий способ оплаты через Интернет!">
						<img alt="" border="0" src="https://www.sandbox.paypal.com/ru_RU/i/scr/pixel.gif" width="1" height="1">
						</form>';
						*/

					// Отправить отдельное сообщение электронной почты?
					$body = "Спасибо за регистрацию на <любом сайте>. Да. Да. Да.\n\n";
					mail($_POST['email'], 'Подтверждение регистрации', $body, 'От: admin@example.com');
		
					// Завершение страницы
					include('./includes/footer.html'); // включение HTML-футера
					exit(); // прекратить отображение страницы
					
				} else { // если при выполнении произошел сбой
					trigger_error('Регистрация не завершена из-за системной ошибки. Приносим извинения за доставленные неудобства. Эта проблема будет устранена в ближайшем будущем');
				}
				
				} else { // адрес электронной почты или имя пользователя недоступны
				
				if ($rows === 2) { // загружены адрес электронной почты и имя пользователя
		
					$reg_errors['email'] = 'Этот адрес электронной почты уже зарегистрирован. Если вы забыли пароль, щелкните на отображенной слева ссылке, чтобы получить новый пароль.';			
					$reg_errors['username'] = 'Это имя пользователя уже зарегистрировано. Попробуйте воспользоваться другим именем пользователя.';			

				} else { // могут загружаться одна или две записи

					// Получить строку
					$row = mysqli_fetch_array($r, MYSQLI_NUM);
							
					if( ($row[0] === $_POST['email']) && ($row[1] === $_POST['username'])) { // обе записи совпадают
						$reg_errors['email'] = 'Этот адрес электронной почты уже зарегистрирован. Если вы забыли пароль, воспользуйтесь находящейся слева ссылкой для восстановления пароля.';	
						$reg_errors['username'] = 'Это имя пользователя уже зарегистрировано вместе с этим адресом электронной почты. Если вы забыли пароль, воспользуйтесь находящейся слева ссылкой для восстановления пароля.';
					} elseif ($row[0] === $_POST['email']) { // соответствие с адресом электронной почты
						$reg_errors['email'] = 'Этот адрес электронной почты уже зарегистрирован. Если вы забыли пароль, воспользуйтесь находящейся слева ссылкой для восстановления пароля.';						
					} elseif ($row[1] === $_POST['username']) { // сравнение с именем пользователем
						$reg_errors['username'] = 'Это имя пользователя уже зарегистрировано. Пожалуйста, попробуйте другое имя.';			
					}
			
				} // завершение конструкции $rows === 2 ELSE
				
			} // завершение конструкции $rows === 0 IF
			
		} // завершение конструкции empty($reg_errors) IF

} // завершение условного выражения, примеяемого для передачи данных основной форме

// Подключается сценарий form functions, определяющий функцию create_form_input()
// Этот файл может быть включен с помощью заголовка

	// Подключается сценарий form functions, определяющий функцию create_form_input()
	// Этот файл может быть включен с помощью заголовка
	require_once('./includes/form_functions.inc.php');

?>


	<h1>Регистрация</h1>


	<form action="register.php" method="post" accept-charset="utf-8">
	<?php 
		create_form_input('first_name', 'text', 'Имя', $reg_errors); 
		create_form_input('last_name', 'text', 'Фамилия', $reg_errors); 
		create_form_input('username', 'text', 'Желательный логин', $reg_errors); 
		echo '<span class="help-block">Допускаются только буквы и цифры.</span>';
		create_form_input('email', 'email', 'Адрес электронной почты', $reg_errors); 
		create_form_input('pass1', 'password', 'Пароль', $reg_errors);
		echo '<span class="help-block">Минимальная длина - 6 символов, включает хотя бы одну букву верхнего регистра, одну букву нижнего регистра и одну цифру.</span>';
		create_form_input('pass2', 'password', 'Подтверждение пароля', $reg_errors); 
	?>
		<input type="submit" name="submit_button" value="Далее &rarr;" id="submit_button" class="btn btn-default" />
</form>

	<?php // включение HTML-футера
	 
	include('./includes/footer.html');

	?>