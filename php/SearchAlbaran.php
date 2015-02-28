<?php
	@session_start();
	require '../languages/'.$_SESSION["IDIOMA"];
?>
	<div class="modalbox resize">
		<a href="#close" title="Close" class="close">X</a>
		<div class="modal-content">
			<input type="number" id="txt_input" class="ubicacion-codigo" placeholder="<?php print text_placeholder_esperando_albaran?>" onchange="BuscarLineas($(this).val());">
		</div>
	</div>