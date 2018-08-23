<?php

// Этот сценарий отображает выбранную страницу, содержащую HTML-контент
// Сценарий создан в главе 5

// Чтобы контролировать отображение сообщений об ошибках, перед выполнением кода PHP подключается файл конфигурации
require('./includes/config.inc.php');
// Файл конфигурации также открывает сеанс

// Выполняется подключение к базе данных
require(MYSQL);

// Верификация идентификатора категории
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT, array('min_range' => 1))) {
	$page_id = $_GET['id'];

	// Получение сведений о странице
	$q = 'SELECT title, description, content FROM pages WHERE id=' . $page_id;
	$r = mysqli_query($dbc, $q);
	if (mysqli_num_rows($r) !== 1) { // проблема
		$page_title = 'Ошибка!';
		include('./includes/header.html');
		echo '<div class="alert alert-danger">Ошибка при попытке доступа к странице.</div>';
		include('./includes/footer.html');
		exit();
	}

	// Выборка сведений о странице
	$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
	$page_title = $row['title'];
	include('includes/header.html');
	echo '<h1>' . htmlspecialchars($page_title) . '</h1>';

	// Отображение контента для текущей учетной записи пользователя
	if (isset($_SESSION['user_not_expired'])) {
		echo "<div>{$row['content']}</div>";
		} elseif (isset($_SESSI0N['user_id'])) {
			echo '<div class="alert"><h4>Cpoк действия учетной записи истек </h4>Спасибо, что вы заинтересовались контентом нашего сайта, но ваша учетная запись недействительна. Пожалуйста, <а href="renew.php"> обновите вашу учетную запись</а>, чтобы полностью просмотреть страницу.</div>'; echo '<div>' . htmlspecialchars($row['description']) . '</div>';
		} else { 
			echo '<div class="alert">Спасибо, что вы заинтересовались контентом нашего сайта. Нужно зайти на сайт в качестве зарегистрированного пользователя, чтобы получить полный доступ к контенту сайта.</div>';
			echo '<div>' . htmlspecialchars($row['description']) . '</div>';
		}
} else { // отсутствует корректный идентификатор
	 $page_title = 'Ошибка!';
	 include('includes/header.html');
	 echo '<div class="alert alert-danger">Ошибка при попытке доступа к этой странице.</div>';
} // завершение главного блока условного выражения IF

//Завершите создание страницы.
include('./includes/footer.html');
?>