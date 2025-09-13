<?php 
require_once 'auth_check.php';
require_once 'chicken-road/currency.php';

// Конвертируем баланс в USD для отображения в игре
$balance_usd = 0;
if (defined('SYS_BALANCE') && SYS_BALANCE > 0 && defined('SYS_COUNTRY')) {
    $balance_usd = convertToUSD(SYS_BALANCE, SYS_COUNTRY);
} else {
    $balance_usd = 0;
}

if (true) { ?>
	<style>
		.casino {
			display: block;
		}

		.casino__sidebar {
			display: none;
		}

		#aviator_frame2 {
			width: 100%;
			height: 100vh;
		}
	</style>
	<iframe
		src="/chicken-road/?lang=es&user_id=&balance=<?= number_format($balance_usd, 2, '.', ''); ?>"
		id="aviator_frame2"></iframe>
	<div id="modeSelectionModal2" class="mode-selection-modal">
		<div class="mode-selection-content" style="display:block;">
			<h2>Selecciona un modo de juego</h2>
			<button class="mode-two-btn" data-mode="demo">Modo demostración</button>
			<button class="mode-two-btn" data-mode="real">El juego de siempre</button>
		</div>
	</div>
	<script>
		$('#modeSelectionModal2 .mode-two-btn').off().on('click', function () {
			var $self = $(this);
			var $mode = $self.attr('data-mode');
			var $url = $('#aviator_frame2').attr('src');
			if ($mode == 'demo') { 
				$('#aviator_frame2').attr('src', '/chicken-road/?lang=es&user_id=demo&balance=500'); 
			} else { 
				$('#aviator_frame2').attr('src', '/chicken-road/?lang=es&user_id=<?= UID; ?>&balance=<?= number_format($balance_usd, 2, '.', ''); ?>'); 
			}
			$('#modeSelectionModal2').hide().remove();
		});
	</script>
<?php } else { ?>
	<div id="app">
		<style>
			#app {
				width: 100%;
			}

			#aviator_frame {
				width: 100%;
				aspect-ratio:4/3;
				height: 100%;
			}
		</style>
		<iframe
			src="/chicken-road/?lang=es&user_id=&balance=<?= number_format($balance_usd, 2, '.', ''); ?>"
			id="aviator_frame"></iframe>
		<!-- Debug: Original Balance = <?= defined('SYS_BALANCE') ? SYS_BALANCE : 'NOT_DEFINED'; ?>, USD Balance = <?= $balance_usd; ?> -->
		<div id="modeSelectionModal" class="mode-selection-modal">
			<div class="mode-selection-content" style="display:block;">
				<h2>Selecciona un modo de juego</h2>
				<button class="mode-two-btn" data-mode="demo">Modo demostración</button>
				<button class="mode-two-btn" data-mode="real">El juego de siempre</button>
				<p style="display:none;"><?= json_encode($_SESSION); ?></p>
			</div>
		</div>
		<script>
			$('#modeSelectionModal .mode-two-btn').off().on('click', function () {
				var $self = $(this);
				var $mode = $self.attr('data-mode');
				var $url = $('#aviator_frame').attr('src');
				if ($mode == 'demo') { 
					$('#aviator_frame').attr('src', '/chicken-road/?lang=es&user_id=demo&balance=500'); 
				} else { 
					$('#aviator_frame').attr('src', '/chicken-road/?lang=es&user_id=<?= UID; ?>&balance=<?= number_format($balance_usd, 2, '.', ''); ?>'); 
				}
				$('#modeSelectionModal').hide().remove();
			});
			setInterval(function () {
				$.ajax({
					url: "/chicken-road/get_balance.php",
					dataType: "json",
					method: "post",
					data: {},
					error: function (xhr, status, error) {
						console.error('Balance update error:', {
							status: xhr.status,
							statusText: xhr.statusText,
							responseText: xhr.responseText,
							error: error
						});
					},
					success: function ($r) {
						console.log('Balance response:', $r);
						if ($r && $r.deposit) {
							// Обновляем баланс в iframe (уже в USD)
							var iframe = document.getElementById('aviator_frame');
							console.log('Iframe found:', iframe);
							if (iframe && iframe.contentWindow) {
								console.log('Sending USD balance to iframe:', $r.deposit);
								iframe.contentWindow.postMessage({ type: 'updateBalance', balance: $r.deposit }, '*');
							}
						}
					}
				});
			}, 5000);
		</script>
	</div>
<?php } ?>