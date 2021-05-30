<?php
    include_once 'Library/MySQL.php';
    $db = new \Library\MySQL('core',
    \Library\MySQL::connect(
        $_SERVER['DB_HOST'],
        $_SERVER['DB_USER'],
        $_SERVER['DB_PASS']
    ));
    $groups = $db->select(['Division' => []])->many();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Odesa Polytechnic Digital Transformation Strategy</title>
		<style>
		@import url('https://fonts.googleapis.com/css2?family=Jura:wght@300&display=swap');
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		html {
			height: 100vh;
			width: 100vw;
		}
		body {
			background-color: #EEE;
			font-family: 'Jura', sans-serif;
			font-size: 12pt;
			background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(27, 21, 21, 0.3)), url("https://routine.pnit.od.ua/data/back.gif");
			background-size: cover;
			background-attachment: scroll;
			height: 100vh;
			width: 100vw;
		}
		main {
			display: flex;
			flex-wrap: wrap;
			padding: 60px 20px;
		}
		h1, h2, h3 {
			color: #FFF;
			margin-bottom: 10px;
		}
		hr {
			width: 100%;
			margin: 10px 0;
		}
		body > label[for="overlay-switch"] {
			top: 40px;
			height: calc(100% - 40px);
			visibility: hidden;
		}
		td {
			padding: 10px;
		}
		div {
			display: flex;
			flex-wrap: wrap;
		}
		ul {
			width: 100%;
			padding: 10px;
		}
		h1, h2, h3, p, .row {
			width: 100%;
		}
		h1 {
			padding: 15px;
			width: calc(100% - 64px);
		}
		input {
			padding: 10px;
			font-size: 12pt;
			margin: 5px;
		}
		details {
			width: 100%;
			margin: 10px 0;
		}
		pre {
			margin: 10px 0;
		}
		code {
			display: inline-block;
			font-weight: bold;
			padding: 5px;
			margin: 5px;
			border-radius: 5px;
			background-color: #95a5ff;
		}
		summary {
			padding: 5px;
			background-color: rgb(35, 219, 235);
		}
		.col-1-2 {
			width: 50%;
		}
		.col-1-3 {
			width: 33.33%;
		}
		.col-2-3 {
			width: 66.66%;
		}
		.serviceInput {
			display: none;
		}
		.tab {
			display: inline-flex;
			background-color: #FFF;
			padding: 10px;
			align-items: center;
		}
		.tab-info {
			background-color: rgba(20, 76, 152, 0.56);
			padding: 20px;
			position: fixed;
			z-index: 2;
			width: 100%;
			top: 40px;
			left: 0;
		}
		.tab-info .details {
			background-color: #FFF;
			padding: 20px;
		}
		.break-top {
			margin-top: 20px;
		}
		.status[data-value] {
			display: inline-flex;
			vertical-align: middle;
			width: 10px;
			height: 10px;
			border-radius: 5px;
			margin: 5px;
		}
		.popup h1,.popup h2, .popup h3 {
			color: #000;
			margin-top: 10px;
		}
		.popup-overlay, .popup .overlay {
			display: flex;
			position: fixed;
			width: 100%;
			height: 100%;
			background-color: rgba(0,0,0,0.4);
		}
		.popup-overlay,
		.popup:not(:target) {
			display: none;
		}
		.popup {
			position: fixed;
			top: 0;
			left: 0;
			z-index: 3;
			height: 100%;
			width: 100%;
		}
		.popup time {
			margin: 10px 0;
			font-weight: bold;
		}
		.popup .content {
			text-align: justify;
		}
		.popup .body {
			padding: 20px;
			width: 100%;
			max-width: 540px;
			min-height: 320px;
			max-height: 60%;
			margin: auto;
			color: #000;
			align-content: flex-start;
			background-color: #FFF;
			z-index: 1;
			overflow: scroll;
		}
		.popup .body.wide {
			max-width: 1000px;
			min-height: 700px;
		}
		.popup {
			text-align: center;
		}
		.popup .button {
			margin: 10px auto;
		}
		.popup h2 {
			color: #000;
			font-weight: bold;
			text-transform: uppercase;
			text-align: center;
		}
		.state[data-state] {
			text-transform: uppercase;
			font-weight: bold;
			margin: 10px inherit;
		}
		.state[data-state]:not(.active) {
			display: none;
		}
		.dark {
			background-color: rgba(0,0,0,0.3);
		}
		.widget {
			width: 320px;
			background-color: #FFF;
			padding: 10px;
			align-content: space-between;
			box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.22);
			margin-bottom: 3px;
		}
		.widget:not(:last-child) {
			margin-right: 20px;
		}
		.widget h2 {
			color: #000;
		}
		.widget .button {
			align-self: flex-end;
			box-shadow: none;
			background-color: transparent;
			color: #000;
		}
		.widget > .button {
			display: block;
			width: 100%;
			padding: 10px 5px 5px 5px;
			text-transform: uppercase;
			margin-top: 20px;
			font-weight: bold;
			border-top: 1px solid orange;
		}
		.widget time {
			display: block;
			margin-top: 10px;
			font-weight: bold;
			width: 100%;
		}
		.button {
			text-transform: uppercase;
			text-decoration: none;
			border: none;
			color: #FFF;
			font-size: 12pt;
			padding: 10px;
			font-weight: bold;
			background-color: rgba(20, 76, 152);
			box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.22);
		}
		.button[disabled] {
			filter: grayscale();
		}
		.container {
			overflow: auto;
		}
		.container > .row {
			flex-wrap: nowrap;
		}
		#tabs {
			position: fixed;
			z-index: 2;
			top: 0;
			left: 0;
			background-color: #790000;
		}
		#login {
			color: #FFF;
			float: right;
			margin: 10px 10px;
		}
		#clickToAction {
			align-items: flex-start;
			justify-items: center;
		}
		#clickToAction img {
			width: 64px;
		}
		#clickToAction a {
			background-color: rgba(20, 76, 152, 0.56);
		}
				.status[data-value="planned"] {
			background-color: red;
			color: red;
		}
				.status[data-value="development"] {
			background-color: blue;
			color: blue;
		}
				.status[data-value="processing"] {
			background-color: orange;
			color: orange;
		}
				.status[data-value="completed"] {
			background-color: green;
			color: green;
		}
				[name="tabs"]:checked:not([id="overlay-switch"]) ~ label[for="overlay-switch"] {
			visibility: visible;
		}
				#api-switch:not(:checked) ~ .tab-info[id="api-tab"] {
			display: none;
		}
				#routine-switch:not(:checked) ~ .tab-info[id="routine-tab"] {
			display: none;
		}
				#structure-switch:not(:checked) ~ .tab-info[id="structure-tab"] {
			display: none;
		}
				#workers-switch:not(:checked) ~ .tab-info[id="workers-tab"] {
			display: none;
		}
				</style>
	</head>
	<body>
		<input id="overlay-switch" class="serviceInput" type="radio" name="tabs" checked>
				<input id="api-switch" class="serviceInput" type="radio" name="tabs">
				<input id="routine-switch" class="serviceInput" type="radio" name="tabs">
				<input id="structure-switch" class="serviceInput" type="radio" name="tabs">
				<input id="workers-switch" class="serviceInput" type="radio" name="tabs">
				<header id="tabs" class="row dark">
                    <?php foreach ($groups as $group): ?>
                    <label for="api-switch" class="tab">
                        <div  data-value="development" class="status"></div>     <?=$group['name']?>    </label>
                    <?php endforeach;?>
		</header>
					<div id="api-tab" class="tab-info">
				<h2>Довідник реєстрів</h2>
				<div class="details col-2-3">
					<div class="row">
						<table>
							<tr>
								<td>Статус:</td>
								<td><div data-value="development" class="status"></div>development</td>
							</tr>
							<tr>
								<td>Адреса:</td>
								<td><a href="https://api.pnit.od.ua">api.pnit.od.ua</a></td>
							</tr>
							<tr>
								<td>Оновлено:</td>
								<td>8 березня 2021</td>
							</tr>
							<tr>
								<td>Розпорядник:</td>
								<td>Кафедра проєктного навчання в ІТ</td>
							</tr>
							<tr>
								<td>Відповідальна особа:</td>
								<td>Сергій Шкрабак (<a href="https://t.me/q_kex">q_kex</a>), Асистент</td>
							</tr>
							<tr>
								<td>Опис:</td>
								<td>Містить інформацію про стан наявних в університеті реєстрів</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="col-1-3"></div>
			</div>

						<div id="routine-tab" class="tab-info">
				<h2>Реєстр розкладу</h2>
				<div class="details col-2-3">
					<div class="row">
						<table>
							<tr>
								<td>Статус:</td>
								<td><div data-value="development" class="status"></div>development</td>
							</tr>
							<tr>
								<td>Адреса:</td>
								<td><a href="https://routine.pnit.od.ua">routine.pnit.od.ua</a></td>
							</tr>
							<tr>
								<td>Оновлено:</td>
								<td>19 березня 2021</td>
							</tr>
							<tr>
								<td>Розпорядник:</td>
								<td>Студентське самоврядування ІШІР</td>
							</tr>
							<tr>
								<td>Відповідальна особа:</td>
								<td>Юлій Маєвський (<a href="https://t.me/not_sure1">not_sure1</a>), Цифровий амбасадор</td>
							</tr>
							<tr>
								<td>Опис:</td>
								<td>Розміщує інформацію про поточний розклад студентів університету</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="col-1-3"></div>
			</div>
						<div id="structure-tab" class="tab-info">
				<h2>Реєстр структурних підрозділів</h2>
				<div class="details col-2-3">
					<div class="row">
						<table>
							<tr>
								<td>Статус:</td>
								<td><div data-value="planned" class="status"></div>planned</td>
							</tr>
							<tr>
								<td>Адреса:</td>
								<td><a href="https://structure.pnit.od.ua">structure.pnit.od.ua</a></td>
							</tr>
							<tr>
								<td>Оновлено:</td>
								<td>7 березня 2021</td>
							</tr>
							<tr>
								<td>Розпорядник:</td>
								<td>Студентське самоврядування ІКС</td>
							</tr>
							<tr>
								<td>Відповідальна особа:</td>
								<td>Ахмед Валяєв (<a href="https://t.me/aabselyam">aabselyam</a>), Цифровий амбасадор</td>
							</tr>
							<tr>
								<td>Опис:</td>
								<td>Містить інформацію про наявні факультети, кафедри, тощо</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="col-1-3"></div>
			</div>
						<div id="workers-tab" class="tab-info">
				<h2>Реєстр співробітників</h2>
				<div class="details col-2-3">
					<div class="row">
						<table>
							<tr>
								<td>Статус:</td>
								<td><div data-value="planned" class="status"></div>planned</td>
							</tr>
							<tr>
								<td>Адреса:</td>
								<td><a href="https://account.pnit.od.ua">account.pnit.od.ua</a></td>
							</tr>
							<tr>
								<td>Оновлено:</td>
								<td>7 березня 2021</td>
							</tr>
							<tr>
								<td>Розпорядник:</td>
								<td>Студентське самоврядування ІКС</td>
							</tr>
							<tr>
								<td>Відповідальна особа:</td>
								<td>Ахмед Валяєв (<a href="https://t.me/aabselyam">aabselyam</a>), Цифровий амбасадор</td>
							</tr>
							<tr>
								<td>Опис:</td>
								<td>Містить інформацію про науково-педагогічний, адміністративний та технічний персонал. Деяка інформація знаходиться в обмеженному доступі</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="col-1-3"></div>
			</div>
					<main>
			<div class="row">
				<div id="clickToAction" class="col-1-3">
					<a href="#joinUs" class="button">🚀 Приєднатись</a>
				</div>
				<div class="col-2-3 container">
					<div class="row">
						<div class="widget">
							<h2>Стратегія</h2>
							<p>Для забезпечення якісного навчання слід максимально оптимізувати робочі процеси співробітників та максимально спростити взаємодію між структурними підрозділами</p>
							<a class="button">Деталі</a>
						</div>
						<div class="widget">
							<h2>Проєкти</h2>
							<p>Студенти та викладачі впродовж освітнього процесу впроваджують нові цифрові ідеї. Усі проєкти наближають нас до paperless-університету</p>
							<a class="button">Деталі</a>
						</div>
						<div class="widget">
							<h2>Реєстри</h2>
							<p>Будь-які цифрові процеси базуються на даних. Паперові дані неможливо оптимізувати, їх неможливо швидко знайти та дорого обслуговувати</p>
							<a class="button">Деталі</a>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<h2>Новини</h2>
				<div class="col-2-3 container">
					<div class="row">
											<article class="widget">
							<h2>Запущено Портал з Цифрової трансформації університету</h2>
							<p>В рамках роботи над дисципліною "Об'єктно-орієнтоване програмування" створено координаційний ресурс</p>
							<time>7 березня 2021</time>
							<a class="button" href="#post-1">Деталі</a>
						</article>
											<article class="widget">
							<h2>Випущено бета-версію універсального децентралізованого протоколу UNI.ROAD</h2>
							<p>Універсальний протокол призначений для комунікації між децентралізованими сервісами Універсітету</p>
							<time>11 квітня 2021</time>
							<a class="button" href="#post-2">Деталі</a>
						</article>
											<article class="widget">
							<h2>Ухвалено положення СС Інституту Штучного Інтелекту та Робототехніки</h2>
							<p>Проведено загальні збори студентів. Обрано склад виборчої комісії інституту та запущено виборчий процес</p>
							<time>14 квітня 2021</time>
							<a class="button" href="#post-3">Деталі</a>
						</article>
										</div>
				</div>
			</div>
		</main>
		<label for="overlay-switch" class="popup-overlay"></label>
		<div id="joinUs" class="popup">
			<form class="body" method="POST" action="#" data-trigger="ambassadorForm">
				<input type="hidden" name="api" value="form/submitAmbassador">
				<h2>Стань Цифровим амбасадором вже сьогодні!</h2>
				<large class="state" data-state="success">✅ Все чудово</large>
				<large class="state" data-state="error">🚨 Трапилась халепа</large>
				<p><small>Ти пройдеш тренінг і відповідатимеш за стратегію та процеси діджиталізації Одеської політехніки</small></p>
				<p><input name="firstname" placeholder="Ім'я"></p>
				<p><input name="secondname" placeholder="Прізвище"></p>
				<p><input name="position" placeholder="Посада/Група"></p>
				<p><input name="phone" placeholder="Телефон"></p>
				<button type="submit" class="button">Подати заявку</button>
			</form>
			<a href="#" class="overlay"></a>
		</div>
				<div id="post-1" class="popup">
			<div class="body wide">
				<h2>Запущено Портал з Цифрової трансформації університету</h2>
				<p>В рамках роботи над дисципліною "Об'єктно-орієнтоване програмування" створено координаційний ресурс</p>
				<time>7 березня 2021</time>
				<hr>
				<div class="row content">
								</div>
			</div>
			<a href="#" class="overlay"></a>
		</div>
				<div id="post-2" class="popup">
			<div class="body wide">
				<h2>Випущено бета-версію універсального децентралізованого протоколу UNI.ROAD</h2>
				<p>Універсальний протокол призначений для комунікації між децентралізованими сервісами Універсітету</p>
				<time>11 квітня 2021</time>
				<hr>
				<div class="row content">
									<p>Для взаємодії із реєстрами, що розміщені на різних серверах, необхідно використуовувати спільний сумістний інструментарій.
На сервері розробника має бути метод uniwebhook, що призначено для реакції на повідомлення з основного телеграм-боту та реакції на події у ньому.
<pre class="code">public function uniwebhook(String $type = '', String $text = '', Int $code = 0):?array {
	$result = null;
	switch ($type) {
		case 'message':
			...
			break;
		..
	}
	return $result;
}
</pre>
Після взаємодії користувача із телеграм-ботом можливе виникнення подій наступних типів:
<details>
	<summary>message - текстове повідомлення</summary>
	<p>Підходить для простих команд типу "запитання-відповідь"</p>
	<h3>Параметри</h3>
	<p><code>type</code>Тип події</p>
	<p><code>value</code>Зміст тестового повідомлення</p>
</details>
<details>
	<summary>click - натискання кнопки</summary>
	<p>Користувач може натиснути кнопку, що належить сервісу</p>
	<h3>Параметри</h3>
	<p><code>type</code>Тип події</p>
	<p><code>code</code>Локальний id кнопки в сервісі</p>
</details>
<details>
	<summary>contact - ідентифікація за номером</summary>
	<p>Телеграм дозволяє ідентифікувати особу за її номером</p>
	<h3>Параметри</h3>
	<p><code>type</code>Тип події</p>
	<p><code>value</code>Номер телефону</p>
</details>
<h2>Callback-події</h2>
<p>Користувач, що надсилає запит, може одразу отримати у відповідь повідомлення наступних типів:</p>
<details>
	<summary>message - текстове повідомення</summary>
	<p>Будь-яке текстове повідомлення з/без кнопок</p>
	<h3>Параметри</h3>
	<p><code>type</code>Тип події</p>
	<p><code>to</code>guid користувача, якому необхідно надіслати повідомлення</p>
	<p><code>value</code>Текст повідомлення</p>
	<p><code>keyboard</code>Інформація про кнопки, представлена масивом:</p>
	<pre>'keyboard' => [
		'inline' => false,
		'buttons' => [
			[
				[
					'id' => 9, // Ідентифікатор кнопки в системі
					'title' => 'Надати номер', // Текст кнопки
					'request' => 'contact' // Додається за умови необхідності зробити запит на дані. Підтримується лише номер телефону
				]
			]
		]
	]</pre>
</details>
<details>
	<summary>context - встановлення контексту</summary>
	<p>Для того, щоб інший сервіс отримав керування поточним чатом, можна здійснити передачу йому контексту</p>
	<h3>Параметри</h3>
	<p><code>type</code>Тип події</p>
	<p><code>set</code>псевдонім сервісу, що вказується в core.Services.code. Значення null здійснить вихід з режиму зовнішнього сервісу</p>
</details>
Заплановано додати тип "Форма" для заповнення користувачем набору даних

<h2>Виконання запитів до сервісів</h2>
<p>Для того, щоб виконати запит до сервісу, достатньо мати лише актуальну інформацію в таблиці core.Services про сервіси</p>
<pre>$this->uni()->get('proxy', [ // proxy - це псевдонім сервісу, що вказується в core.Services.code
	'firstname' => 'Doctor Who', // Параметр форми
	'secondname' => 'Corporation', // Параметр форми
	'phone' => '380000000000' // Параметр форми
], 'form/submitAmbassador')->one(); // form/submitAmbassador - метод API
</pre>
<p>Як результат виконання буде повернуто масив із даними</p></p>
								</div>
			</div>
			<a href="#" class="overlay"></a>
		</div>
				<div id="post-3" class="popup">
			<div class="body wide">
				<h2>Ухвалено положення СС Інституту Штучного Інтелекту та Робототехніки</h2>
				<p>Проведено загальні збори студентів. Обрано склад виборчої комісії інституту та запущено виборчий процес</p>
				<time>14 квітня 2021</time>
				<hr>
				<div class="row content">
									<p><p>Студенти - рушійна сила цифрової трансформації, а студентське самоврядування - найкраща форма її координації. Висловлюємо глибокі сподівання на плідну співпрацю із студентським самоврядуванням ІШІР!</p>
Загальними зборами студентів ІШІР обрано виборчу комісію у складі:
<ul>
	<li>Володимир Деревенча, ст.гр. УП-191.</li>
	<li>Яна Бєлік, ст.гр. УП-191.</li>
</ul>
<p>Ознайомитись із положенням можна за <a href="/media/attachments/iair-ss.pdf" target="_blank">посиланням</a></p></p>
								</div>
			</div>
			<a href="#" class="overlay"></a>
		</div>
				<script>
		'use strict';
		var server = {
			api: '//api.pnit.od.ua/'
		}
		for (let form of document.getElementsByTagName('form')) {
			form.addEventListener('submit', async function(e) {
				e.preventDefault();
				var params = {};
				for (let field of e.target.elements)
					if (field.name) {
						params[field.name] = field.value
					}
				if ( params.api ) {
					var request = '',
						url = server.api + params.api;
					delete params.api;
					for (let key in params) {
						request += '&' + key + '=' + params[key];
					}
					if (request)
						request = request.substr(1);
					try {
						var event = new CustomEvent('before');
						e.target.dispatchEvent(event);
						let answer = await fetch(url, {
							method: 'POST',
							headers: {
								"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
							},
							credentials: 'include',
							body: request
						});
						answer.json().then(response => {
							var event = new CustomEvent(response.state == 0 ? 'success' : 'error', {detail:response});
							e.target.dispatchEvent(event);
							console.log(response);
						});
					} catch(error) {
						alert(error);
					}
				}
			});
		}
		var triggers = {
			ambassadorForm: {
				success: function(e){
					for (let button of this.getElementsByClassName('button')) {
						button.disabled = false;
					}
					for (let state of this.getElementsByClassName('state'))
						state.classList[state.dataset.state == 'success' ? 'add' : 'remove']('active');
				},
				error: function(e){
					for (let button of this.getElementsByClassName('button')) {
						button.disabled = false;
					}
					for (let state of this.getElementsByClassName('state'))
						state.classList[state.dataset.state == 'error' ? 'add' : 'remove']('active');
				},
				before: function(){
					for (let button of this.getElementsByClassName('button')) {
						button.disabled = true;
					}
				}
			}
		}
		for (let form of document.querySelectorAll('[data-trigger]')) {
			if (triggers[form.dataset.trigger])
				for (let i in triggers[form.dataset.trigger]) {
					form.addEventListener( i, triggers[form.dataset.trigger][i] );
				}
		}
		</script>
	</body>
</html>