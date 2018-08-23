<?php

// Этот сценарий выбирает и отображает PDF-файлы
// Этот сценарий создан в главе 5

// Чтобы контролировать отображение сообщений об ошибках, перед выполнением кода PHP требуется конфигурирование среды
require('./includes/config.inc.php');
// Файл конфигурации также открывает сеанс

// Требуется подключение к базе данных
require(MYSQL);

// Предполагается некорректная информация
$valid = false;

// Верификация идентификатора PDF
if (isset($_GET['id']) && (strlen($_GET['id']) === 63) && (substr($_GET['id'], 0, 1) !== '.') ) {
	//strlen — Возвращает длину строки
	//Возвращает подстроку строки string, начинающейся с start символа по счету и длиной length символов.

	// Идентификация файла
	$file = PDFS_DIR . $_GET['id'];
	/*
	Идентификатор документа PDF извлекается сценарием из URL-ссылки. Этот идентификатор не обязательно должен быть
	целочисленным, но его длина должна составлять ровно 63 символа.
	*/

	// Проверить, что PDF существует и является файлом

	if (file_exists ($file) && (is_file($file)) ) {
		// Получение сведений
		$q = 'SELECT id, title, description, file_name FROM pdfs WHERE tmp_name="' . escape_data($_GET['id'], $dbc) . '"';
		$r = mysqli_query($dbc, $q);
		if (mysqli_num_rows($r) === 1) { // OK
	
			// Выборка информации
			$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
	
			// Показывает, что ссылка на файл корректна
			$valid = true;

			// Просмотреть PDF-файл может только пользователь с активной учетной записью
			if (isset($_SESSION['user_not_expired'])) {
				// Отправка информации о контенте
				header('Content-type:application/pdf'); 
				header('Content-Disposition:inline;filename="' . $row['file_name'] . '"'); 
				$fs = filesize($file);
				header("Content-Length:$fs\n");
				// Отправка файла
				readfile($file);
				exit();
			} else { // неактивная учетная запись
	
				// Взамен отображается HTML-страница
				$page_title = $row['title'];
				include('./includes/header.html');
				echo "<h1>$page_title</h1>";
	
				// Изменение сообщения на основе статуса пользователя
				if (isset($_SESSION['user_id'])) {
					echo '<div class="alert"><h4>Просроченная учетная запись</h4>Спасибо за интерес, проявленный к контенту сайта, но ваша учетная запись не активна. Пожалуйста, <a href="renew.php">обновите вашу учетную запись,</a> чтобы получить доступ к этому сайту.</div>';
				} else { // не был выполнен вход
					echo '<div class="alert">Спасибо за интерес, проявленный к контенту сайта. Чтобы получить доступ к сайту, нужно войти в качестве зарегистрированного пользователя.</div>';
				}
	
				// Завершение страницы
				echo '<div>' . htmlspecialchars($row['description']) . '</div>';
				include('./includes/footer.html');	
	
			} // завершение пользовательского блока IF-ELSE
					
		} // завершение условного блока mysqli_num_rows()

	} // завершение условного блока file_exists()
	
} // завершение условного блока $_GET['id']

// Если что-то не работает...
if (!$valid) {
	$page_title = 'Ошибка!';
	include('./includes/header.html');
	echo '<div class="alert alert-danger">Ошибка при доступе к странице.</div>';
	include('./includes/footer.html');	
}
?>
