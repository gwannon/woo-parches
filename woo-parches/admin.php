<?php

//ADMIN -----------------------------------------

add_action( 'admin_menu', 'woo_parches_plugin_menu' );
function woo_parches_plugin_menu() {
	add_options_page( __('Administración Parches', 'woo-parches'), __('Parches', 'woo-parches'), 'manage_options', 'woo-parches', 'woo_parches_page_settings');
}

function woo_parches_page_settings() {  ?>
	<h1><?php _e("Configuración del sistema de pedidos de parches", 'woo-parches'); ?></h1>
	<?php if(isset($_REQUEST['send']) && $_REQUEST['send'] != '') { 
		update_option('_woo_parches_productos_tamanos', json_encode($_POST['_woo_parches_productos_tamanos']));
		?><h3 style="border: 1px solid green; color: green; text-align: center;"><?php _e("Datos guardados correctamente.", 'woo-parches'); ?></h3><?php
	} ?>
	<?php $productos_tamanos = json_decode( get_option("_woo_parches_productos_tamanos"), true); ?>
	<form method="post">
		
		<h2><?php _e("Productos y tamaños", 'woo-parches'); ?>:</h2>
		<table>
			<tr>
				
				<th><?php _e("Id de producto", 'woo-parches'); ?></th>
				<th><?php _e("Tamaño máximo en cm<sup>2</sup>", 'woo-parches'); ?></th>
				<th><?php _e("Producto", 'woo-parches'); ?></th>			
			</tr>
		<?php for ($i = 0; $i < 5; $i++) { ?>
			<tr>
				
				<td valign="top"><input type="number" name="_woo_parches_productos_tamanos[<?=$i ?>][id]" value="<?php echo $productos_tamanos[$i]['id']; ?>" /></td>
				<td valign="top"><input type="number" name="_woo_parches_productos_tamanos[<?=$i ?>][tamano]"value="<?php echo $productos_tamanos[$i]['tamano']; ?>" /></td>
				<td><a href="<?php echo get_edit_post_link($productos_tamanos[$i]['id']); ?>" target="_blank"><?php echo get_the_title($productos_tamanos[$i]['id']); ?></a></td>			
			</tr>
		
		<?php } ?>
		</table>
		<input type="submit" class="button primary" name="send" value="<?php _e("Save"); ?>" />
	<?php
}
