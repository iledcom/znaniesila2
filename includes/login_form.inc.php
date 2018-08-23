<?php

// Этот сценарий отображает форму входа
// Этот сценарий включен header.html, если пользователь не выполнил вход в форму
// Этот сценарий создан в главе 4

// Генерирование пустого массива ошибок, если он не был создан раньше
if (!isset($login_errors)) $login_errors = array();

// Подключение сценария form functions, определяющего функцию create_form_input()
 require('/includes/form_functions.inc.php');
?>
<form action="index.php" method="post" accept-charset="utf-8">
	<fieldset>
		<legend>Вход</legend>
		<?php 
		if (array_key_exists('login', $login_errors)) {
			//Функция array_key_exists() возвращает TRUE, если в массиве присутствует указанный ключ key. Параметр key может быть любым значением, которое подходит для индекса массива.
			echo '<div class="alert alert-danger">' . $login_errors['login'] . '</div>';
		}
		create_form_input('email', 'email', '', $login_errors, array('placeholder'=>'Адрес электронной почты')); 
		create_form_input('pass', 'password', '', $login_errors, array('placeholder'=>'Пароль')); 
		echo '<span class="help-block"><a href="forgot_password.php">Забыли пароль?</a></span>';
		?>
	<button type="submit" class="btn btn-default">Вход &rarr;</button>
	</fieldset>
</form>			