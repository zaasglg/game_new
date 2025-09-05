<?php if( IS_MOBILE ){ ?>
		<style>
			.casino{ display:block; }
			.casino__sidebar{ display:none; }
			#aviator_frame2{ width:100%; height:100vh; }
		</style>
		<iframe src="/chicken-road/?lang=es&user_id=" id="aviator_frame2" ></iframe>
		<div id="modeSelectionModal2" class="mode-selection-modal">
			<div class="mode-selection-content" style="display:block;">
				<h2>Selecciona un modo de juego</h2>
				<button class="mode-two-btn" data-mode="demo">Modo demostración</button>
				<button class="mode-two-btn" data-mode="real">El juego de siempre</button> 
			</div>
		</div>
		<script>
			$('#modeSelectionModal2 .mode-two-btn').off().on('click', function(){
				var $self=$(this); 
				var $mode = $self.attr('data-mode'); 
				var $url = $('#aviator_frame2').attr('src');
				if( $mode == 'demo' ){ $('#aviator_frame2').attr('src', $url+'demo'); }
				else { $('#aviator_frame2').attr('src', $url+'<?= UID; ?>'); } 
				$('#modeSelectionModal2').hide().remove(); 
			});
		</script>
		<?php } else { ?>
		<div id="app">
		    <style>
		      	#app{ width:100%; }
		      	#aviator_frame{ width:100%; /*aspect-ratio:4/3;*/ height:calc( 100vh - 65px ); }
		    </style>
		    <iframe src="/chicken-road/?lang=es&user_id=" id="aviator_frame" ></iframe>
		    <div id="modeSelectionModal" class="mode-selection-modal">
		      	<div class="mode-selection-content" style="display:block;">
					<h2>Selecciona un modo de juego</h2>
					<button class="mode-two-btn" data-mode="demo">Modo demostración</button>
					<button class="mode-two-btn" data-mode="real">El juego de siempre</button>
					<p style="display:none;"><?= json_encode( $_SESSION ); ?></p>
		      	</div>
		    </div>
		    <script>
		      	$('#modeSelectionModal .mode-two-btn').off().on('click', function(){
			        var $self=$(this); 
			        var $mode = $self.attr('data-mode'); 
			        var $url = $('#aviator_frame').attr('src');
			        if( $mode == 'demo' ){ 
			        	$('#aviator_frame').attr('src', $url+'demo'); 
			        }
			        else { 
			        	$('#aviator_frame').attr('src', $url+'<?= UID; ?>'); 
			        	
			        	// Синхронизируем баланс с основной системой и конвертируем в USD
			        	setTimeout(function(){
			        		$.ajax({
			        			url: '/get_user_data.php',
			        			method: 'GET',
			        			dataType: 'json',
			        			success: function(response) {
			        				if(response.success && response.data.is_auth) {
			        					// Обновляем баланс в игре с конвертацией в USD
			        					$.ajax({
			        						url: '/chicken-road/update_balance.php',
			        						method: 'POST',
			        						contentType: 'application/json',
			        						data: JSON.stringify({
			        							user_id: <?= UID; ?>,
			        							balance: response.data.balance,
			        							currency: response.data.currency || 'USD'
			        						}),
			        						success: function(updateResponse) {
			        							console.log('Balance synchronized and converted:', updateResponse);
			        							if(updateResponse.success) {
			        								console.log('Original: ' + updateResponse.original_balance + ' ' + updateResponse.original_currency);
			        								console.log('Converted: $' + updateResponse.usd_balance);
			        							}
			        						},
			        						error: function(xhr, status, error) {
			        							console.error('Failed to update balance:', error);
			        						}
			        					});
			        				}
			        			},
			        			error: function(xhr, status, error) {
			        				console.error('Failed to get user data:', error);
			        			}
			        		});
			        	}, 1000);
			        } 
			        $('#modeSelectionModal').hide().remove(); 
		      	});
		    </script>
		</div>
<?php } ?>
