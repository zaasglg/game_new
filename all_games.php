<?php 
	error_reporting(E_ALL); 
	ini_set('display_errors', 1);

	if( !defined('BASE_DIR') ){ define('BASE_DIR', dirname(__FILE__)."/"); } 
	if( !defined('TPL_DIR') ){ define('TPL_DIR', BASE_DIR ."templates/"); } 

	define( 'IS_MOBILE', preg_match('/Android|iPhone|iPad|iPod|Windows Phone|Mobile/i', $_SERVER['HTTP_USER_AGENT']) );
	
	require_once 'auth_check.php';
	
	// текущая игра если есть
	define( 'CURRENT_GAME', ( isset( $_REQUEST['game'] ) ? $_REQUEST['game'] : '' ) ); 
	// авторизован ли юзер
	//define('AUTH', $is_logged_in );
	// баланс пользователя 
	//define('SYS_BALANCE', $deposit ); 
	// системная валюта
	//define('SYS_CURRENCY', $currency ); 
	// бонусы 
	//define('SYS_BONUS', $bonificaciones ); 
	// страна юзера
	//define('SYS_COUNTRY', $country ); 
	// 
	//define('SYS_STAGE', $stage ); 
?>
<!DOCTYPE html>
<html translate="no" lang="en" prefix="og: https://ogp.me/ns#" style="--tg-viewport-height: 100vh; --tg-viewport-stable-height: 100vh">
	
	<?php 
		// блок head. настройки страницы, стили, скрипты
		include_once TPL_DIR ."head.php"; 
	?>

	<body class="<?= $body_class; ?>" data-game="<?= CURRENT_GAME; ?>" data-mobile="<?= IS_MOBILE ? 1 : 0; ?>">
		<div id="root">
			<?php 
				// блок header. верхнее меню
				include_once TPL_DIR ."header.php"; 
			?>

			<main class="main-content main-content-games">
				<?php 
					// основная страница 
					include_once TPL_DIR ."casino.php"; 
					// игры нет, показываем лоадер
					if( !CURRENT_GAME ){ 		
				?>
						<div class="block-loader block-loader-desktop" style="margin: 20px 0 0 0;">
				            <div class="_loader_r3fud_1 _loader_color_yellow_r3fud_64">
								<div></div>
								<div></div>
								<div></div>
								<div></div>
				            </div>
				        </div>
				<?php 
					} 
				?>
			</main>

			<?php 
				// блок footer. подвал сайта 
				include_once TPL_DIR ."footer.php"; 

				// блок tabbar. нижнее меню для мобилки 
				if( IS_MOBILE ){ 
					include_once TPL_DIR ."tabbar.php"; 
				}
			?>
		</div>
		<?php 
			// вход 
			include_once TPL_DIR ."login.php"; 
			// кугистрация 
			include_once TPL_DIR ."register.php"; 
		?>
		
		<script src="../js/script.js"></script>
		<script src="../js/toggle-password.js"></script>
		<script src="../js/mobile-sidebar.js"></script>
		<script src="../js/select-active.js"></script>
		<script src="../js/utm.js"></script>
		<script src="./js/href.js"></script>
		
		<script>
			const USER_ID = <?= json_encode( $_SESSION['user_id'] ?? null ); ?>;
			// Передаем данные пользователя в глобальную область видимости
			window.userData = {
				country: "<? SYS_COUNTRY; ?>",
				stage: "<?= SYS_STAGE; ?>"
			};
		</script>
		
		<script src="../js/country_limits.js"></script>
		<script src="./js/limited.js"></script>
		
		<script>
		    // Функция для обновления заголовка и отображения соответствующего блока
		    function updateTitleAndContent() {
		      	const activeItem = document.querySelector('._item_active_1rs5q_69');
		      	if (!activeItem) return;

		      	const titleSpan = activeItem.querySelector('._itemTitle_1rs5q_52');
		      	const h1Title = document.querySelector('._title_1fyf8_29');
		      	const h2Title = document.querySelector('h2._name_sakbi_8');

		      	if (!titleSpan || !h1Title || !h2Title) return;

		      	// Получаем текст заголовка
		    	// Очищаем заголовок от лишних пробелов и переносов
		    	let titleText = titleSpan.textContent.replace(/\s+/g, ' ').trim();
		    	const normalizedTitle = titleText.toLowerCase();
		    	const safeTitleText = CSS.escape(titleText);

		      	// Проверяем, находится ли активный элемент внутри блока с "proveedores"
		      	const isInProvidersSection = activeItem.closest('._section_1rs5q_1')?.querySelector('._title_1rs5q_9')?.textContent === 'proveedores';

		      	if (isInProvidersSection) {
		        	// Если да — вставляем картинку и текст
		        	const img = activeItem.querySelector('img._icon_1rs5q_48');
		        	if (img) {
		          		h1Title.innerHTML = `<img src="${img.src}" alt="${img.alt}" style="height: 20px; vertical-align: middle; margin-right: 8px;"> ${titleText}`;
		          		h2Title.textContent = titleText;

		          		// Обновляем значение в первом селекторе
		          		const providerSelectValue = document.querySelector('.games-filters__selects .select:first-child .select__control-value');
		          		if (providerSelectValue) {
		            		providerSelectValue.textContent = titleText;
		          		}
		        	} 
		        	else {
		          		// Если картинки нет (например, SVG), вставляем только текст
		          		h1Title.textContent = titleText;
		          		h2Title.textContent = titleText;
		        	}
		      	} 
		      	else {
		        	// Если нет — вставляем только текст
		        	h1Title.textContent = titleText;
		        	h2Title.textContent = titleText;

		        	// Обновляем значение во втором селекторе (категории)
		        	const categorySelectValue = document.querySelector('.games-filters__selects .select--second .select__control-value');
		        	if (categorySelectValue) {
		          		categorySelectValue.textContent = titleText.toLowerCase();
		        	}
		      	}

		      	// Скрываем все блоки с классом _body_1fyf8_40
		      	const allBodyBlocks = document.querySelectorAll('._body_1fyf8_40');
		      	allBodyBlocks.forEach(block => {
		        	block.style.display = 'none';
		      	});

		      	// Показываем только блок с data-category, соответствующий заголовку
		      	const matchingBlock = document.querySelector(`._body_1fyf8_40[data-category="${titleText}"]`);
		      	if (matchingBlock) {
		        	matchingBlock.style.display = 'block';
		      	}
		    }

		    // Запускаем при загрузке страницы
		    document.addEventListener('DOMContentLoaded', updateTitleAndContent);

		    // И при кликах (на случай, если активный элемент меняется)
		    document.addEventListener('click', updateTitleAndContent);
		</script>

		<?php /* СРАВНИТЬ ДИФОМ, ПОХОДУ ПОВТОР */ ?>
		<script>
		    // Функция для обновления контента
		    function updateContent() {
		      	const activeItem = document.querySelector('._item_active_1rs5q_69');
		      	if (!activeItem) return;

		      	const titleSpan = activeItem.querySelector('._itemTitle_1rs5q_52');
		      	const h1Title = document.querySelector('._title_1fyf8_29');
		      	const h2Title = document.querySelector('h2._name_sakbi_8');

		      	if (!titleSpan || !h1Title || !h2Title) return;

		      	// Получаем текст заголовка и очищаем от лишних пробелов и переносов
		      	let titleText = titleSpan.textContent.replace(/\s+/g, ' ').trim();
		      	const normalizedTitle = titleText.toLowerCase();

		      	// Обновляем заголовки
		      	if (activeItem.closest('._section_1rs5q_1')?.querySelector('._title_1rs5q_9')?.textContent.toLowerCase() === 'proveedores') {
		        	const img = activeItem.querySelector('img._icon_1rs5q_48');
		        	h1Title.innerHTML = img ?
		          		`<img src="${img.src}" alt="${img.alt}" style="height: 20px; vertical-align: middle; margin-right: 8px;"> ${titleText}` :
		          		titleText;

			        // Обновляем селектор провайдеров
			        const providerSelectValue = document.querySelector('.games-filters__selects .select:first-child .select__control-value');
			        if (providerSelectValue) providerSelectValue.textContent = titleText.toLowerCase();
		      	} 
		      	else {
		        	h1Title.textContent = titleText;

		        	// Обновляем селектор категорий
		        	const categorySelectValue = document.querySelector('.games-filters__selects .select--second .select__control-value');
		        	if (categorySelectValue) categorySelectValue.textContent = normalizedTitle;
		      	}

		      	h2Title.textContent = titleText;

		      	// Управление отображением блоков
		      	const allBodyBlocks = document.querySelectorAll('._body_1fyf8_40');
		      	const allMobileBlocks = document.querySelectorAll('._category_moblile_ > [data-category-mobile]');

		      	// Скрываем все блоки
		      	allBodyBlocks.forEach(block => block.style.display = 'none');
		      	allMobileBlocks.forEach(block => block.style.display = 'none');

		      	// Безопасный поиск блока по data-category
		      	const desktopMatch = findElementByDataAttribute('._body_1fyf8_40', 'data-category', titleText);
		      	if (desktopMatch) desktopMatch.style.display = 'block';

		      	// Показываем соответствующий мобильный блок
		      	const mobileMatch = Array.from(allMobileBlocks).find(block => {
			        const blockCategory = block.getAttribute('data-category-mobile');
			        return blockCategory && blockCategory.toLowerCase() === normalizedTitle;
			    });

		      	if (mobileMatch) {
		        	mobileMatch.style.display = 'block';
		      	} 
		      	else if (normalizedTitle === 'all games') {
		        	// Для "All Games" показываем все мобильные блоки
		        	allMobileBlocks.forEach(block => block.style.display = 'block');
		      	}
		    }

		    // Функция для поиска элемента по data-атрибуту с учетом пробелов и переносов
		    function findElementByDataAttribute(selector, attribute, value) {
		      	const elements = document.querySelectorAll(selector);
		      	return Array.from(elements).find(el => {
		        	const attrValue = el.getAttribute(attribute);
		        	return attrValue && attrValue.replace(/\s+/g, ' ').trim() === value;
		      	});
		    }

		    // Функция для поиска элемента по текстовому содержимому
		    function findElementByText(selector, text) {
		      	const elements = document.querySelectorAll(selector);
		      	return Array.from(elements).find(el =>
		        	el.textContent.trim().includes(text) ||
		        	el.textContent.trim().toLowerCase().includes(text.toLowerCase())
		      	);
		    }

		    // Обработчики событий для селекторов
		    function setupSelectListeners() {
		      	const providerItems = document.querySelectorAll('.select:first-child .select__list-item');
		      	const categoryItems = document.querySelectorAll('.select--second .select__list-item');

		      	// Обработчики для селектора провайдеров
		      	providerItems.forEach(item => {
		        	item.addEventListener('click', function () {
		          		const providerName = this.querySelector('.select__list-item-name').textContent.trim();
		          		const titleElement = findElementByText('._itemTitle_1rs5q_52', providerName);
		          		const targetItem = titleElement?.closest('._item_1rs5q_1');

		          		if (targetItem) {
		            		document.querySelectorAll('._item_1rs5q_1').forEach(el => el.classList.remove('._item_active_1rs5q_69'));
		            		targetItem.classList.add('._item_active_1rs5q_69');
		            		updateContent();
		          		}
		        	});
		      	});

		      	// Обработчики для селектора категорий
		      	categoryItems.forEach(item => {
		        	item.addEventListener('click', function () {
		          		const categoryName = this.querySelector('.select__list-item-name').textContent.trim();
		          		const titleElement = findElementByText('._itemTitle_1rs5q_52', categoryName);
		          		const targetItem = titleElement?.closest('._item_1rs5q_1');

		          		if (targetItem) {
		            		document.querySelectorAll('._item_1rs5q_1').forEach(el => el.classList.remove('._item_active_1rs5q_69'));
		            		targetItem.classList.add('._item_active_1rs5q_69');
		            		updateContent();
		          		}
		        	});
		      	});
		    }

		    // Инициализация
		    document.addEventListener('DOMContentLoaded', function () {
		      	updateContent();
		      	setupSelectListeners();

		      	// Делегирование событий для навигационных элементов
		      	document.addEventListener('click', function (e) {
		        	if (e.target.closest('._item_1rs5q_1')) {
		          		setTimeout(updateContent, 50);
		        	}
		      	});
		    });

		    // Функция для безопасного выполнения querySelector с очисткой строки
		    function safeQuerySelector(selector) {
		      	try {
		        	// Удаляем лишние пробелы и переносы строк
		        	const cleanSelector = selector.replace(/\s+/g, ' ').trim();
		        	return document.querySelector(cleanSelector);
		      	} 
		      	catch (e) {
		        	console.error('Invalid selector:', selector, e);
		        	return null;
		      	}
		    }
		</script>

		<script>
		    document.addEventListener('DOMContentLoaded', function () {
		      	// Функция для обработки выбора в селекторе
		      	function handleSelectChange(selectElement) {
		        	const items = selectElement.querySelectorAll('.select__list-item');

		        	items.forEach(item => {
		          		item.addEventListener('click', function (e) {
		            		// Останавливаем всплытие события, чтобы оно не дошло до .select__control
		            		e.stopPropagation();

		            		// Получаем имя выбранного элемента
		            		const selectedName = item.querySelector('.select__list-item-name').textContent;

		            		// Обновляем отображаемое значение в контроле селектора
		            		const controlValue = selectElement.querySelector('.select__control-value');
		            		controlValue.textContent = selectedName;

		            		// Закрываем список
		            		selectElement.querySelector('.select__list-wrapper').style.display = 'none';

		            		// Перезагружаем страницу
		            		window.location.reload();
		          		});
		        	});
		      	}

		      	// Находим оба селектора и применяем обработчик
		      	const selects = document.querySelectorAll('.games-filters__selects .select');
		      	selects.forEach(select => {
		        	// Обработчик для открытия/закрытия списка
		        	const control = select.querySelector('.select__control');
		        	control.addEventListener('click', function () {
		          		const listWrapper = select.querySelector('.select__list-wrapper');
		          		const isHidden = listWrapper.style.display === 'none';

		          		// Сначала скрываем все открытые списки
		          		document.querySelectorAll('.select__list-wrapper').forEach(wrapper => {
		            		wrapper.style.display = 'none';
		          		});

		          		// Затем показываем текущий, если он был скрыт
		          		listWrapper.style.display = isHidden ? 'block' : 'none';
		        	});

		        	// Обработчик для выбора элемента
		        	handleSelectChange(select);
		      	});

		      	// Закрываем выпадающие списки при клике вне селектора
		      	document.addEventListener('click', function (e) {
		        	if (!e.target.closest('.select')) {
		          		document.querySelectorAll('.select__list-wrapper').forEach(wrapper => {
		            		wrapper.style.display = 'none';
		          		});
		        	}
		      	});
		    });
		</script>
	</body>
</html>





