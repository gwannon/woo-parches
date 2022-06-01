<?php
/**
 * Plugin Name: WooParches
 * Plugin URI:  https://github.com/gwannon
 * Description: Configurador de compra de parches
 * Version:     1.0
 * Author:      Gwannon
 * Author URI:  https://github.com/gwannon
 * License:     GNU General Public License v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-parches
 *
 * PHP 7.3
 * WordPress 5.5.3
 */
 
//Cargamos las funciones que crean las páginas en el WP-ADMIN
require_once(dirname(__FILE__)."/admin.php");
 
 function woo_parches_shortcode($params = array(), $content = null) {
  global $post;
  ob_start(); ?>
  <div>
  	<form>
  	 <h3>1) Nombre del pedido:</h3>
  	 <input type="text" name="pedido" placeholder="Añada un nombre a su pedido" /><br/>
  	 <h3>2) Cantidad:</h3>
    <input type="number" name="cantidad" placeholder="Cantidad" min="0" step="1" /><br/>
    <h3>3) Adjuntar archivo:</h3>
    <input type="file" name="imagen" accept="image/png, image/jpeg" />
    <h3>4) Tamaño del parche (máximo 10x10 cm):</h3>
  	 <b>Alto</b> <input type="number" name="alto" placeholder="en cm." min="0" max="10" step="0.1" /><br/>
  	 <b>Ancho</b> <input type="number" name="ancho" placeholder="en cm." min="0" max="10" step="0.1" /><br/>
  	<h3>5) Opciones disponibles:</h3>
	<h4>Borde bordado</h4>
  	<b>Contorno del parche bordado en el color deseado</b><br/>
	Pedido mínimo 50 unidades<br/>
	<input type="checkbox" name="borde" value="bordado" /> + 75% incremento del precio
	<h4>Velcro en parte trasera</h4>
  	<b>Parte trasera con velcro</b><br/>
	<input type="radio" name="velcro" value="trasera" data-show="#trasera_opciones" /> + 25% incremento del precio<br/>
	<div class="opciones" id="trasera_opciones" style="display: none;">
		<input type="radio" name="velcro_trasera" value="macho" /> Velcro macho en la espalda<br/>
		<input type="radio" name="velcro_trasera" value="hembra" /> Velcro hembra en la espalda<br/>
   </div>	
	
	<h4>Velcro en parte trasera + velcro suelto para prenda</h4>
  	<b>Parte trasera con velcro + velcro suelto para coser en prenda</b><br/>
	<input type="radio" name="velcro" value="trasera_prenda" data-show="#trasera_prenda_opciones" /> + 30% incremento del precio<br/>
	<div class="opciones" id="trasera_prenda_opciones" style="display: none;">
		<input type="radio" name="velcro_trasera_prenda" value="macho" /> Velcro macho en la espalda<br/>
		<input type="radio" name="velcro_trasera_prenda" value="hembra" /> Velcro hembra en la espalda<br/>
   </div>
   <input type="submit" name="anadir-parche" value="Añadir parche" />
  </form>
  

  </div>
  <style>
	.opciones {
		padding: 10px;
		border: 1px solid #cecece;
	}
  </style>
  <script>
  	  jQuery("input[value=trasera], input[value=trasera_prenda]").click(function() {
	  	jQuery(".opciones").not(jQuery(this).data("show")).fadeOut();
	   jQuery(jQuery(this).data("show")).fadeIn();
	  });
  </script>
  <?php
  $html = ob_get_clean(); 
  return $html;
}
add_shortcode('woo-parches', 'woo_parches_shortcode');

