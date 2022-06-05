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

 /*
  *
	* TODO
	* - Validar formulario
	* - Chequear imagen antes de subirla
	* - Nombre de pedido
	* - Checkout con foto del producto
	* - Preparar para traducir
	*
	*/

define("WOO_PARCHES_UPLOADS_DIR", dirname(__FILE__).'/uploads/');
define("WOO_PARCHES_UPLOADS_URL", plugin_dir_url(__FILE__).'/uploads/');
define("WOO_PARCHES_HASH", hash('ripemd160', date("YmdHis").rand(1000, 9999)));
define("WOO_PARCHES_PAGE_ID", 2680);

 //Al activar el plugin
register_activation_hook( __FILE__, 'woo_parches_activate_plugin');
function woo_parches_activate_plugin() {
  if (!file_exists(WOO_PARCHES_UPLOADS_DIR)) {
    mkdir(WOO_PARCHES_UPLOADS_DIR, 0777, true);
  }
}
 
//Cargamos las funciones que crean las páginas en el WP-ADMIN
require_once(dirname(__FILE__)."/admin.php");
 
 function woo_parches_shortcode($params = array(), $content = null) {
  global $post;
  ob_start(); ?>
	<div>
		<form id="woo-parches" method="post" enctype="multipart/form-data">
			<h3>Nombre del pedido:</h3>
			<input type="text" name="pedido" placeholder="Añada un nombre a su pedido" required />
			<h3>Cantidad:</h3>
			<input type="number" name="cantidad" placeholder="Cantidad" min="1" step="1" value="1" required />
			<h3>Adjuntar archivo:</h3>
			<input type="file" name="imagen" accept="image/png, image/jpeg" />
			<h3>Tamaño del parche (máximo <?php echo get_option("_woo_parches_max_alto"); ?>x<?php echo get_option("_woo_parches_max_ancho"); ?> cm.):</h3>
			<p><b>Alto</b> <input type="number" name="alto" placeholder="en cm." min="0" max="<?php echo get_option("_woo_parches_max_alto"); ?>" step="0.1" required /></p>
			<p><b>Ancho</b> <input type="number" name="ancho" placeholder="en cm." min="0" max="<?php echo get_option("_woo_parches_max_ancho"); ?>" step="0.1" required /></p>
			<h3>Opciones disponibles:</h3>
			<h4>Borde bordado</h4>
			<p><b>Contorno del parche bordado en el color deseado</b><br>Pedido mínimo de 50 unidades</p>
			<p><input type="checkbox" name="borde" value="borde-bordado" disabled="disabled" /> + 75% incremento del precio</p>
			<h4>Velcro en parte trasera</h4>
			<p><b>Parte trasera con velcro</b></p>
			<p><input type="radio" name="velcro_tipo" value="trasera" data-show="#trasera_opciones" /> + 25% incremento del precio</p>
			<p class="opciones" id="trasera_opciones" style="display: none;">
				<input type="radio" name="velcro" value="velcro-en-parte-trasera-macho" /> Velcro macho en la espalda<br/>
				<input type="radio" name="velcro" value="velcro-en-parte-trasera-hembra" /> Velcro hembra en la espalda
 			</p>	
			<h4>Velcro en parte trasera + velcro suelto para prenda</h4>
			<p><b>Parte trasera con velcro + velcro suelto para coser en prenda</b></p>
			<p><input type="radio" name="velcro_tipo" value="trasera_prenda" data-show="#trasera_prenda_opciones" /> + 30% incremento del precio</p>
			<p class="opciones" id="trasera_prenda_opciones" style="display: none;">
				<input type="radio" name="velcro" value="velcro-en-parte-trasera-macho-velcro-suelto-para-prenda" /> Velcro macho en la espalda<br/>
				<input type="radio" name="velcro" value="velcro-en-parte-trasera-hembra-velcro-suelto-para-prenda" /> Velcro hembra en la espalda
 			</p>
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
		jQuery("input[name=cantidad]").on("change", function() {
			console.log(jQuery(this).val());
			if(jQuery(this).val() >= 50) {
				jQuery("input[name=borde]").prop("disabled", false);
				jQuery("input[name=borde]").prop('checked', false);
			} else {
				jQuery("input[name=borde]").prop("disabled", true);
				jQuery("input[name=borde]").prop('checked', false);
			}
		});
  	jQuery("input[value=trasera], input[value=trasera_prenda]").click(function() {
	  	jQuery(".opciones").not(jQuery(this).data("show")).fadeOut();
			jQuery(jQuery(this).data("show")).fadeIn();
	  });

		jQuery("#woo-parches input").on("change", function() {
			if(jQuery("#woo-parches input[name=alto]").val() > 0 && jQuery("#woo-parches input[name=ancho]").val() > 0) {
				jQuery.ajax({
					url : '/wp-admin/admin-ajax.php',
					data : {
						action: 'woo_parches',
						cantidad: jQuery("#woo-parches input[name=cantidad]").val(),
						alto: jQuery("#woo-parches input[name=alto]").val(),
						ancho: jQuery("#woo-parches input[name=ancho]").val(),
						borde: jQuery("#woo-parches input[name=borde]:checked").val(),
						velcro: jQuery("#woo-parches input[name=velcro]:checked").val(),
					},
					type : 'GET',
					dataType : 'json',
					beforeSend: function () {
						jQuery("#parches-total").empty();
					},
					success : function(json) {
						jQuery("#parches-total").html(json.total);
					},
					error : function(xhr, status) {

					},
					complete : function(xhr, status) {

					}
				});
			}
		});
  </script>
  <?php
  $html = ob_get_clean(); 
  return $html;
}
add_shortcode('woo-parches', 'woo_parches_shortcode');

//Añadimos el producto al carrito
function woo_parches_add_to_cart() {
	global $woocommerce;
	if(isset($_REQUEST['anadir-parche']) && $_REQUEST['anadir-parche'] != '') {
		$fichero_subido = WOO_PARCHES_UPLOADS_DIR . WOO_PARCHES_HASH."-".basename($_FILES['imagen']['name']);
		//$fichero_url = WOO_PARCHES_UPLOADS_URL . WOO_PARCHES_HASH."-".basename($_FILES['imagen']['name']);
		if (move_uploaded_file($_FILES['imagen']['tmp_name'], $fichero_subido)) {
			$borde = (isset($_REQUEST['borde']) && $_REQUEST['borde'] == 'borde-bordado' ? 'borde-bordado' : 'borde-normal');
			$velcro = (isset($_REQUEST['velcro']) && $_REQUEST['velcro'] != '' ? $_REQUEST['velcro'] : 'sin-velcro');
			$productos_tamanos = json_decode( get_option("_woo_parches_productos_tamanos"), true);
			$tamano = $_REQUEST['alto'] * $_REQUEST['ancho'];
			foreach ($productos_tamanos as $producto_tamano) {
				if ($producto_tamano['tamano'] != '' && $tamano <= $producto_tamano['tamano']) {
					WC()->cart->add_to_cart($producto_tamano['id'], (is_numeric($_REQUEST['cantidad']) && $_REQUEST['cantidad'] > 0 ? $_REQUEST['cantidad'] : 1), woo_parches_find_matching_product_variation_id($producto_tamano['id'], $borde, $velcro));
					wp_redirect ($woocommerce->cart->get_cart_url());
					die;
				}
			}
		}
	}
}
add_action('template_redirect', 'woo_parches_add_to_cart' );

//Redireccionar páginas de tienda a configurador
function woo_parches_redirect() {
	if( is_shop() ) {
		wp_redirect (get_the_permalink(WOO_PARCHES_PAGE_ID));
		die;
	} else if(is_single(woo_parches_get_product_ids()) && is_product()) {
		wp_redirect (get_the_permalink(WOO_PARCHES_PAGE_ID));
		die;
	}
}
add_action('template_redirect', 'woo_parches_redirect' );



//Find matching product variation
function woo_parches_find_matching_product_variation_id($product_id, $borde, $velcro) {
	$attributes = [
		'attribute_pa_borde' => $borde,
    'attribute_pa_velcro' => $velcro,
	];
	$data_store = WC_Data_Store::load( 'product' );
	$variation_id = $data_store->find_matching_product_variation(new \WC_Product($product_id), $attributes);
	return $variation_id;
}

//Conseguimos las ids de los productos parche
function woo_parches_get_product_ids() {
	$products = array();
	foreach (json_decode( get_option("_woo_parches_productos_tamanos"), true) as $producto_tamano) {
		if ($producto_tamano['tamano'] != '' && $producto_tamano['id'] != '' ) {
			$products[] = $producto_tamano['id'];
		}
	}
	return $products;
}

//--------------------------------------------------------------------------------
//Metemos los nuevos campos en los productos/carrito, checkout y pedidos del admin
//--------------------------------------------------------------------------------
function custom_checkboxes(){
	return array(
			'alto' => __( "%s cm.", "woo-parches"),
			'ancho' => __( "%s cm.", "woo-parches"),
	);
}

// Add data to cart item
add_filter( 'woocommerce_add_cart_item_data', 'woo_parches_add_cart_item_data', 25, 2 );
function woo_parches_add_cart_item_data( $cart_item_data, $product_id ) {
	if(!in_array($product_id, woo_parches_get_product_ids())) return $cart_item_data;
	$data = array() ;
	if (isset($_FILES['imagen']['name'])) {
		$fichero_url = WOO_PARCHES_UPLOADS_URL . WOO_PARCHES_HASH."-".basename($_FILES['imagen']['name']);
		$cart_item_data['custom_data']['imagen'] = $data['imagen'] = $fichero_url;
	}
	foreach( custom_checkboxes() as $key => $value ){
		if( isset( $_REQUEST[$key] ) ) $cart_item_data['custom_data'][$key] = $data[$key] = sprintf($value, $_REQUEST[$key]);
	}
	if( count($data ) > 0 ){
		$cart_item_data['custom_data']['unique_key'] = md5( microtime().rand() );
		WC()->session->set( 'custom_data', $data );
	}
	return $cart_item_data;
}


// Display custom data on cart and checkout page.
add_filter( 'woocommerce_get_item_data', 'woo_parches_get_item_data' , 25, 2 );
function woo_parches_get_item_data ( $cart_data, $cart_item ) {
	if(!in_array($cart_item['product_id'], woo_parches_get_product_ids())) return $cart_item_data;
	if( ! empty( $cart_item['custom_data'] ) ){
		$values =  array();
		foreach( $cart_item['custom_data'] as $key => $value ) {
			if( $key != 'unique_key' ) {
				if($key == 'alto' || $key == 'ancho') $size[$key] = $value;
				else if($key == 'imagen') $image = $value;
			} 
		}
		$cart_data[] = array(
			'name'    => __( "Tamaño", "woo-parches"),
			'display' => sprintf(__("%s x %s", "woo-parches"), $size['ancho'], $size['alto']),
		);
		$cart_data[] = array(
			'name'    => __( "Imagen", "woo-parches"),
			'display' => sprintf(__( "<a href='%s' target='_blank'>Ver diseño</a>", "woo-parches"), $image),
		);
	}
	return $cart_data;
}

// Add order item meta.
add_action( 'woocommerce_add_order_item_meta', 'woo_parches_add_order_item_meta' , 10, 3 );
function woo_parches_add_order_item_meta ( $item_id, $cart_item, $cart_item_key ) {
	if ( isset( $cart_item[ 'custom_data' ] ) ) {
		$values =  array();
		foreach( $cart_item[ 'custom_data' ] as $key => $value ) {
			if( $key != 'unique_key' ) {
				if($key == 'alto' || $key == 'ancho') $size[$key] = $value;
				else if($key == 'imagen') $image = $value;
			} 
		}
		$values = implode( ', ', $values );
		wc_add_order_item_meta( $item_id, __( "Tamaño", "woo-parches"), sprintf(__("%s x %s", "woo-parches"), $size['ancho'], $size['alto']) );
		wc_add_order_item_meta( $item_id, __( "Imagen", "woo-parches"), sprintf(__( "<a href='%s' target='_blank'>Ver diseño</a>", "woo-parches"), $image) );
	}
}






// Product thumbnail in checkout
add_filter( 'woocommerce_cart_item_name', 'woo_parches_product_thumbnail_in_checkout', 20, 3 );
function woo_parches_product_thumbnail_in_checkout( $product_name, $cart_item, $cart_item_key ){
  if (is_checkout() || is_cart()) {
		$thumbnail   = $cart_item['data']->get_image(array( 80, 80));
		$image_html  = '<div class="product-item-thumbnail" style="border: 1px dashed #cecece; margin-bottom: 10px;"><img src="'.$cart_item[ 'custom_data' ]['imagen'].'" style="max-width: 300px;max-height: 300px;"/></div> ';
		$product_name = $image_html . $product_name;
  }
  return $product_name;
}




//AJAX ----------------------
function woo_parches_ajax() {
	$borde = (isset($_REQUEST['borde']) && $_REQUEST['borde'] == 'borde-bordado' ? 'borde-bordado' : 'borde-normal');
	$velcro = (isset($_REQUEST['velcro']) && $_REQUEST['velcro'] != '' ? $_REQUEST['velcro'] : 'sin-velcro');
	$productos_tamanos = json_decode( get_option("_woo_parches_productos_tamanos"), true);
	$tamano = $_REQUEST['alto'] * $_REQUEST['ancho'];
	foreach ($productos_tamanos as $producto_tamano) {
		if ($producto_tamano['tamano'] != '' && $tamano <= $producto_tamano['tamano']) {
			$product = wc_get_product(woo_parches_find_matching_product_variation_id($producto_tamano['id'], $borde, $velcro));
			$bulk = new Woo_Bulk_Discount_Plugin_t4m();
			$discount = $bulk->get_discounted_coeff($producto_tamano['id'], $_REQUEST['cantidad'] );
			$json['price'] = $product->get_regular_price() * $discount;
			$json['total'] = wc_price($product->get_regular_price() * $_REQUEST['cantidad'] * $discount);
			echo json_encode($json);
			wp_die();


			die;
		}
	}



	$json['total'] = number_format(rand(100, 200), 2, ',', ',')." €";
	echo json_encode($json);
	wp_die();
}

add_action('wp_ajax_woo_parches', 'woo_parches_ajax');
add_action('wp_ajax_nopriv_woo_parches', 'woo_parches_ajax');