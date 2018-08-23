<?php

// Отображение доступных PDF-файлов
// Этот сценарий создан в главе 5

// Перед выполнением PHP-кода требуется подключение файла конфигурации
require('./includes/config.inc.php');
// Файл конфигурации также начинает сеанс

// Выполняется подключение к базе данных
require(MYSQL);

// Включение заголовочного файла
$page_title = 'PDF-файлы';
include('./includes/header.html');

// Вывод заголовка страницы
echo '<h1>PDF-справочники</h1>';

// Вывод сообщения при отсутствии активного пользователя
if (isset($_SESSION['user_id']) && !isset($_SESSION['user_not_expired'])) {
	echo '<div class="alert"><h4>Просроченная учетная запись</h4>Спасибо за интерес, проявленный к контенту сайта, но ваша учетная запись неактивна. Пожалуйста, <a href="renew.php">обновите вашу учетную запись</a>, чтобы получить возможность просмотра PDF-файлов.</div>';
} elseif (!isset($_SESSION['user_id'])) {
	echo '<div class="alert">Спасибо за интерес, проявленный к контенту сайта. Чтобы получить возможность просмотра PDF-файлов, войдите в качестве зарегистрированного пользователя.</div>';
}

// Получить PDF-файлы
$q = 'SELECT tmp_name, title, description, size FROM pdfs ORDER BY date_created DESC';
$r = mysqli_query($dbc, $q);
if (mysqli_num_rows($r) > 0) { // При наличии записей...
	
		//Выборка каждой записи
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

		// Отображение каждой записи
		echo '<div><h4><a href="view_pdf.php?id=' . htmlspecialchars($row['tmp_name']) . '">' . htmlspecialchars($row['title']) . ' </a> (' . $row['size'] . 'kb)</h4><p>' . htmlspecialchars($row['description']) . '</p></div>';

	} // завершение цикла WHILE
	
} else { // PDF-файлы отсутствуют
	echo '<div class="alert alert-danger">Отсутствуют PDF-файлы, доступные для просмотра. Пожалуйста, выполните повторную проверку!</div>';
}

// Включение HTML-футера
include('./includes/footer.html');
?>