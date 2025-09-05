<div class="_modal_15zx8_1">
    <div class="_overlay_hi522_1 _overlay--open_hi522_12" aria-hidden="true"></div>
    <div class="_container_15zx8_19 _container_heightAuto_15zx8_27">
      	<div class="_container_10y1t_1 _login_uzguq_1 _login_desktop_uzguq_77 _container--lg_10y1t_15 _container_backgroundColor_white_10y1t_69">
        	<div class="_head_10y1t_76 _head_color_yellow_10y1t_101 _head_desktop_10y1t_88">
          		<svg width="144" height="130" viewBox="0 0 144 130" fill="none" xmlns="http://www.w3.org/2000/svg">
            		<g opacity="0.1">
	              		<path d="M111.628 17.8389L120.77 21.4838L106.194 37.3233L113.865 43.0161L95.2512 56.8294L98.4242 62.4229L72.1608 108.726L45.8971 62.4229L49.0701 56.8291L30.4563 43.0161L38.1275 37.3236L23.5508 21.4841L32.4245 17.946C29.8231 12.9085 27.6154 7.18264 25.5029 1.15791H0L58.4064 130H85.6008L144 1.15791H118.495C116.398 7.14004 114.206 12.8279 111.628 17.8389Z" fill="black"></path>
	              		<path d="M136.333 -56.0461C109.792 -38.5286 104.918 -10.498 95.5236 0.129364C99.1967 -17.5356 92.559 -26.9903 106.935 -42.5995C89.9579 -31.28 96.0359 -15.9238 89.303 -0.683891L87.7949 -1.56792C96.2198 -25.3343 73.3298 -55.7099 108.436 -74C67.5759 -61.4941 79.3861 -34.0497 75.04 -9.04321L72.1606 -10.7307L69.281 -9.04321C64.9349 -34.0497 76.7453 -61.4941 35.8844 -74C70.9911 -55.7099 48.1017 -25.3342 56.5263 -1.5679L54.7567 -0.530853C47.9148 -15.8212 54.0985 -31.242 37.0648 -42.5994C51.4402 -26.9902 44.8028 -17.5355 48.476 0.129387C39.0813 -10.4983 34.2072 -38.5285 7.66733 -56.046C26.5193 -38.8968 30.1245 3.84444 44.3543 21.857L37.3862 24.6353L50.1061 38.457L43.9626 43.0161L59.5797 54.6052L55.1454 62.4229L72.1607 92.4211L89.1759 62.4229L84.7419 54.6055L100.359 43.0161L94.2152 38.457L106.935 24.6352L99.7196 21.7587C113.887 3.69006 117.514 -38.9273 136.333 -56.0461ZM59.0439 40.5436L48.3543 20.2334L64.5806 32.0339L59.0439 40.5436ZM85.2772 40.5436L79.7404 32.0339L95.9667 20.2334L85.2772 40.5436Z" fill="black"></path>
            		</g>
          		</svg>
          		<h3 class="_title_10y1t_114 _title_fontSize_medium_10y1t_131">Acceso</h3>
          		<div data-cy="close-modal-button-login" class="_close_10y1t_164" aria-hidden="true"></div>
        	</div>
        	<div data-cy="login-modal" class="_body_uzguq_1">
          		<form id="login-form" class="_form_uzguq_10" method="POST">
            		<div class="_controls_uzguq_10">
              			<div class="_row_uzguq_10">
                			<div class="_root_1rq38_1 _root_ltr_1rq38_115">
                  				<label class="_label_1rq38_10 _label_color_mirage_1rq38_16" for="username">Correo electrónico / Teléfono</label>
                  				<div class="_inputContent_1rq38_22">
                    				<input class="_input_1rq38_22" id="email" name="email" placeholder="Correo electrónico o teléfono..." required>
                  				</div>
                			</div>
              			</div>
              			<div class="_row_uzguq_10">
               				<div class="_root_1rq38_1">
                  				<label class="_label_1rq38_10 _label_color_mirage_1rq38_16" for="password">Contraseña</label>
                  				<div class="_inputContent_1rq38_22">
                    				<input class="_input_1rq38_22 toggle-password-input" id="password" name="password" placeholder="Contraseña..." type="password" required>
                    				<div class="_endIcon_1rq38_75">
                      					<img class="toggle-password-input-svg" src="../images/uneach.svg" alt>
                    				</div>
                  				</div>
                			</div>
              			</div>
            		</div>
            		<div class="_buttons_uzguq_98">
              			<button type="submit" class="_button_1qy1r_1 _button_color_yellow_1qy1r_33 _button_border-radius_medium_1qy1r_23 _button_border_1qy1r_20 _button_flex_1qy1r_14 _button_fixHeight_1qy1r_76 _submit_uzguq_26" data-cy="login-submit">Acceso</button>
            		</div>
          		</form>
          		<script>
            		document.getElementById('login-form').addEventListener('submit', function (e) {
              			e.preventDefault(); // Отменяем стандартную отправку формы

						// Собираем данные формы
						const formData = new FormData(this);

            			// Отправляем данные на сервер через AJAX
            			fetch('login.php', {
                			method: 'POST',
                			body: formData
              			})
                		.then(response => response.json())
                		.then(data => {
                  			if (data.success) {
								// Успешная авторизация
								Notiflix.Notify.success(data.message);
								setTimeout(() => {
                      				window.location.href = 'account.php'; // Перенаправление на account.php
                    			}, 2000); // Задержка 2 секунды
                  			} 
                  			else {
                    			// Ошибка
                   				 Notiflix.Notify.failure(data.message);
                  			}
                		})
                		.catch(error => {
                  			console.error('Ошибка:', error);
                  			Notiflix.Notify.failure('Se ha producido un error al enviar los datos.');
                		});
            		});
          		</script>
        	</div>
        	<div class="_footer_10y1t_188 _footer_uzguq_53">
          		<p class="_text_uzguq_59">No tengo una cuenta</p>
          		<button type="button" class="_link_uzguq_65 open_login-modal">Registrarse <img src="../images/arrow-right.svg" alt></button>
        	</div>
      	</div>
    </div>
</div>

