<?php

// Этот файл представляет домашнюю страницу 
// Этот сценарий начал создаваться в главе 3

// Подключение файла конфигурации перед выполнением кода PHP для контроля сообщений об ошибках
require('./includes/config.inc.php');
// Файл конфигурации также запускает сеанс

// Тестирование боковых панелей:
$_SESSION['user_id'] = 1;
$_SESSION['user_admin'] = true;
$_SESSION['user_not_expired'] = true;
$_SESSION=array();

// Выполняется подключение к базе данных
require(MYSQL);

// Следующий блок добавлен в главе 4
// Если выполняется запрос POST, обрабатывается попытка входа
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	include('./includes/login.inc.php');
}

// Поключение файла заголовка:
include('/includes/header.html');

/* ЗДЕСЬ НАХОДИТСЯ КОНТЕНТ СТРАНИЦЫ! */
?><h1>Добро пожаловать</h1>
 <p class="lead">Добро пожаловать на сайт Знание - сила, на котором публикуется самая современная информация о безопасности в Интернете и программировании. Да, да, да. Конечно, конечно, конечно.</p>
	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent consectetur volutpat nunc, eget vulputate quam tristique sit amet. Donec suscipit mollis erat in egestas. Morbi id risus quam. Sed vitae erat eu tortor tempus consequat. Morbi quam massa, viverra sed ullamcorper sit amet, ultrices ullamcorper eros. Mauris ultricies rhoncus leo, ac vehicula sem condimentum vel. Morbi varius rutrum laoreet. Maecenas vitae turpis turpis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce leo turpis, faucibus et consequat eget, adipiscing ut turpis. Donec lacinia sodales nulla nec pellentesque. Fusce fringilla dictum purus in imperdiet. Vivamus at nulla diam, sagittis rutrum diam. Integer porta imperdiet euismod.</p>


<h3>Lorem Ipsum</h3>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent consectetur volutpat nunc, eget vulputate quam tristique sit amet. Donec suscipit mollis erat in egestas. Morbi id risus quam. Sed vitae erat eu tortor tempus consequat. Morbi quam massa, viverra sed ullamcorper sit amet, ultrices ullamcorper eros. Mauris ultricies rhoncus leo, ac vehicula sem condimentum vel. Morbi varius rutrum laoreet. Maecenas vitae turpis turpis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce leo turpis, faucibus et consequat eget, adipiscing ut turpis. Donec lacinia sodales nulla nec pellentesque. Fusce fringilla dictum purus in imperdiet. Vivamus at nulla diam, sagittis rutrum diam. Integer porta imperdiet euismod.</p>
<?php /* КОНЕЦ КОНТЕНТА СТРАНИЦЫ! */

// Подключение файла футера, завершающего шаблон
include('/includes/footer.html');


?>

