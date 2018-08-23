<?php

// С помощью этой страницы администратор создает выбранную страницу с HTML-контентом
// Этот сценарий был создан в главе 5

// Требуется конфигурирование перед выполнением кода PHP, чтобы контролировать отображение сообщение об ошибках
require('./includes/config.inc.php');

// Перенаправление пользователя, который не вошел в систему как администратор, функция определена в сценарии con-fig.inc.php
redirect_invalid_user('user_admin');

// Установка подключения к базе данных
require(MYSQL);

// Включение файла заголовка:
$page_title = 'Добавление заголовка страницы сайта';
include('./includes/header.html');

// Массив, предназначенный для хранения сообщений об ошибках
$add_page_errors = array();

// Проверка передачи данных формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {	
	
	// Проверка заголовка
	if (!empty($_POST['title'])) {
		$t = escape_data(strip_tags($_POST['title']), $dbc);
	} else {
		$add_page_errors['title'] = 'Пожалуйста, введите заголовок!';
	}
	
	// Проверка категории
	if (filter_var($_POST['category'], FILTER_VALIDATE_INT, array('min_range' => 1))) {
		$cat = $_POST['category'];
	} else { // категория не выбрана
		$add_page_errors['category'] = 'Пожалуйста, выберите категорию!';
	}

	// Проверка описания
	if (!empty($_POST['description'])) {
		$d = escape_data(strip_tags($_POST['description']), $dbc);
	} else {
		$add_page_errors['description'] = 'Пожалуйста, введите описание!';
	}
		
	// Проверка контента
	if (!empty($_POST['content'])) {
		$allowed = '<div><p><span><br><a><img><h1><h2><h3><h4><ul><ol><li><blockquote>';
		$c = escape_data(strip_tags($_POST['content'], $allowed), $dbc);
	} else {
		$add_page_errors['content'] = 'Пожалуйста, введите контент!';
	}
		
	if (empty($add_page_errors)) { // Если все в порядке.

		// Добавить страницу в базу данных
		$q = "INSERT INTO pages (categories_id, title, description, content) VALUES ($cat, '$t', '$d', '$c')";
		$r = mysqli_query($dbc, $q);

		if (mysqli_affected_rows($dbc) === 1) { // если выполняется без сбоев
	
			// Вывод сообщения
			echo '<div class="alert alert-success"><h3>Страница добавлена!</h3></div>';
			
			// Очистка значения переменной $_POST
			$_POST = array();
			
			// Отправить сообщение администратору о том, что контент был добавлен?
			
		} else { // Если при выполнении произошел сбой
			trigger_error('Страница не может быть добавлена из-за системной ошибки. Приносим извинения за доставленные неудобства.');
		}
		
	} // завершение условной конструкции $add_page_errors
	
} // завершение условного выражения передачи данных главной формы

// Подключения сценария form functions, определяющего функцию create_form_input()
require('includes/form_functions.inc.php');
?>
<h1>Добавление страницы контента сайта</h1>
<form action="add_page.php" method="post" accept-charset="utf-8">

	<fieldset><legend>Чтобы добавить страницу контента, заполните форму:</legend>
		<!--
		<div class="form-group">
		<label for="status" class="control-label">Состояние</label>
		<select name="status" class="form-control"><option value="draft">Черновик</option>
		<option value="live">Рабочая версия</option>
		</select></div>
		-->
		<?php
		create_form_input('title', 'text', 'Название', $add_page_errors); 

		// Добавление категории раскрывающегося списка
		echo '<div class="form-group';
		if (array_key_exists('category', $add_page_errors)) echo 'has-error'; 

		echo '"><label for="category" class="control-label">Категория</label>
		<select name="category" class="form-control">
		<option>Выбрана одна категория</option>';


		// Дополнительный материал!
		// Добавлено в главе 12
		// Допускается несколько категорий:
		// echo '"><label for="category" class="control-label">Категория</label>
		// <select name="category[]" class="form-control" multiple size="5">';

		// Выборка всех категорий и добавление в раскрывающееся меню
		$q = "SELECT id, category FROM categories ORDER BY category ASC";
		$r = mysqli_query($dbc, $q);
		while ($row = mysqli_fetch_array($r, MYSQLI_NUM)) {
			//mysqli_fetch_array выбирает одну строку из результирующего набора и помещает ее в ассоциативный массив, обычный массив или в оба
			echo "<option value=\"$row[0]\"";
			// Проверка состояния выборки:
			if (isset($_POST['category']) && ($_POST['category'] == $row[0]) ) echo ' selected="selected"';
			echo ">$row[1]</option>\n";
		}

		echo '</select>';
		if (array_key_exists('category', $add_page_errors)) echo '<span class="help-block">' . $add_page_errors['category'] . '</span>';
		echo '</div>';

		create_form_input('description', 'textarea', 'Описание', $add_page_errors); 
		create_form_input('content', 'textarea', 'Контент', $add_page_errors); 
		?>
				
		<input type="submit" name="submit_button" value="Добавить страницу" id="submit_button" class="btn btn-default" />

	</fieldset>
</form>

<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>

<script type="text/javascript">
	tinyMCE.init({
		// Общие параметры
		selector : "#content",
		width : 800,
		height : 400,
		browser_spellcheck : true,
		
		plugins: "paste,searchreplace,fullscreen,hr,link,anchor,image,charmap,media,autoresize,autosave,contextmenu,wordcount",

		toolbar1: "cut,copy,paste,|,undo,redo,removeformat,|hr,|,link,unlink,anchor,image,|,charmap,media,|,search,replace,|,fullscreen",
		toolbar2:	"bold,italic,underline,strikethrough,|,alignleft,aligncenter,alignright,alignjustify,|,formatselect,|,bullist,numlist,|,outdent,indent,blockquote,",

		// Пример контента CSS (может быть в случае сайта CSS)
		content_css : "/css/bootstrap.min.css",

	});
</script>
<!-- /TinyMCE -->

<?php /* ЗАВЕРШЕНИЕ КОНТЕНТА СТРАНИЦЫ! */

// Включение файла футера, завершающего шаблон
include('./includes/footer.html');
?>