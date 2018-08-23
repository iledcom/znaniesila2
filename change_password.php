<?php

// С помощью этого сценария можно изменить существующий пароль
// Пользователи должны зарегистрироваться, чтобы получить доступ к этой странице
// Этот сценарий создан в главе 4

// Чтобы обрабатывать сообщения об ошибках, нужно вподключить файл конфигурации перед выполнением произвольного кода PHP
require('./includes/config.inc.php');
// Этот файл конфигурации также запускает сеанс

// Если пользователь не зарегистрирован, выполните перенаправление
redirect_invalid_user();

// Выполняется подключение к базе данных
require(MYSQL);

// Включение файла заголовка
$page_title = 'Изменение пароля';
include('./includes/header.html');

// Массив, предназначенный для хранения сообщений об ошибках
$pass_errors = array();

// Если имеет место запрос POST, обрабатывается передача данных формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			
	// Проверка существующего пароля
	if (!empty($_POST['current'])) {
		$current = $_POST['current'];
	} else {
		$pass_errors['current'] = 'Пожалуйста, введите текущий пароль!';
	}
	
	// Проверка пароля и сравнение с подтвержденным паролем
	if (preg_match('/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,}$/', $_POST['pass1']) ) {
		if ($_POST['pass1'] == $_POST['pass2']) {
			$p = $_POST['pass1'];
		} else {
			$pass_errors['pass2'] = 'Ваш пароль не соответствует подтвержденному паролю!';
		}
	} else {
		$pass_errors['pass1'] = 'Пожалуйста, введите корректный пароль!';
	}

	//Если при выполнении предыдущих пунктов ошибок не выявлено, то проверяется текущий пароль путем его сравнения с записями в базе данных.
	
	if (empty($pass_errors)) { // если все OK
	
		// Проверка текущего пароля
		$q = "SELECT pass FROM users WHERE id={$_SESSION['user_id']}";	
		$r = mysqli_query($dbc, $q);
		list($hash) = mysqli_fetch_array($r, MYSQLI_NUM);
		
		// Верификация пароля
		// Включение библиотеки password_compat, если требуется
		// include('./includes/lib/password.php');
		if (password_verify($current, $hash)) { // корректно

			// Обновите запись в базе данных новым сгенерированным паролем. Создание запроса.
			$q = "UPDATE users SET pass='"  .  password_hash($p, PASSWORD_BCRYPT) .  "' WHERE id={$_SESSION['user_id']} LIMIT 1";	
			if ($r = mysqli_query($dbc, $q)) { // если выполняется без сбоев

				// Отправка соответствующего сообщения электронной почты

				// Сообщение пользователю об изменении пароля
				echo '<h1>Ваш пароль был изменен.</h1>';
				include('./includes/footer.html'); // включение футера HTML
				exit();

			} else { // если произошел сбой при выполнении

				trigger_error('Пароль не был изменен из-за системной ошибки. Приносим извинения за доставленные неудобства.'); 

			}

		} else { // некорректный пароль
			$pass_errors['current'] = 'Ваш текущий пароль некорректен!';
		}

	} // завершение блока IF для empty($pass_errors)
	
} // завершение условного выражения, используемого при передаче данных формы

// Требуется подключение сценария form functions, определяющего функцию create_form_input()
require_once('./includes/form_functions.inc.php');
?><h1>Изменение пароля</h1>
<p>Измените пароль с помощью следующей формы.</p>
<form action="change_password.php" method="post" accept-charset="utf-8">
	<?php
	create_form_input('current', 'password', 'Текущий пароль', $pass_errors);
	create_form_input('pass1', 'password', 'Пароль', $pass_errors);
	echo '<span class="help-block">Пароль должен состоять не менее чем из 6 символов, включая, как минимум, один символ нижнего регистра, один символо верхнего регистра и одну цифру.</span>';
	create_form_input('pass2', 'password', 'Подтверждение пароля', $pass_errors); 
	?>
	<input type="submit" name="submit_button" value="Изменить &rarr;" id="submit_button" class="btn btn-default" />
</form>

<?php // включение HTML-футера
include('./includes/footer.html');
?>