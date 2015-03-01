		<div class="modalbox resize">
			<a href="#close" title="Close" class="close">X</a>
				<?php
					if (isset($_SERVER['HTTP_ORIGIN'])) {  
						header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");  
						header('Access-Control-Allow-Credentials: true');  
						header('Access-Control-Max-Age: 86400');   
					}  
					  
					if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {  
					  
						if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))  
							header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  
					  
						if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))  
							header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");  
					}
				@session_start();
				require '../languages/'.$_SESSION["IDIOMA"];
				?>
				<input type="text" id="txt_input" class="filter-movimiento" placeholder="<?php print text_placeholder_movimiento; ?>"
					Onkeyup="BuscarProducto('/php/SearchMovimientoFilter.php', 'filter-movimiento', '#movimiento-filter');" autofocus/>
				<section id="movimiento-filter">
					<p style="clear:both">
						<a><?php print text_aviso_intro_criterio; ?></a>
					</p>
				</section>
		</div>	
		<script>
			$(".filter-movimiento").focus();
		</script>
