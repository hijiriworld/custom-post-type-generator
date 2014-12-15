<?php

global $wpdb;

$results = $pre_result = $results_tax = array();

$sql = "SELECT
		option_id, 
		option_name,
		option_value
	FROM 
		$wpdb->options 
	WHERE 
		option_name LIKE '%%cptg_cpt%%'
	ORDER BY 
		option_id ASC
	";

$pre_results = $wpdb->get_results($sql);

// cptg_orderに従ってソート
$cptg_order = get_option('cptg_order');

if ( isset( $cptg_order ) ) {
	$order = $cptg_order['cptg'];
	foreach( $order as $num ) {
		foreach( $pre_results as $pre_result ) {
			if ( $num == $pre_result->option_id ) {
				$results[] = $pre_result;
				break;
			}
		}
	}
	foreach( $pre_results as $pre_result ) {
		if ( !in_array( $pre_result->option_id, $order ) ) {
			$results[] = $pre_result;
		}
	}
} else {
	$results = $pre_results;
}

$sql = "SELECT
		option_id, 
		option_name,
		option_value
	FROM 
		$wpdb->options 
	WHERE 
		option_name LIKE '%%cptg_tax%%'
	ORDER BY 
		option_id ASC
	";
 
$results_tax = $wpdb->get_results($sql);

// export
$code = '';
if ( isset( $_POST['cptg_export'] ) ) {

	$export_cpts = isset( $_POST['cptg_cpts'] ) ? $_POST['cptg_cpts'] : null;
	$export_taxs = isset( $_POST['cptg_taxs'] ) ? $_POST['cptg_taxs'] : null;
	
	if ( is_array( $export_cpts ) || is_array( $export_taxs ) ) {
		$code .= '/**'."\n";
		$code .= '* Register Custom Post Types and Custom taxonomies.'."\n";
		$code .= '*'."\n";
		$code .= '* from Custom Post Type Generator Plugins.'."\n";
		$code .= '*/'."\n\n";
		
		$code .= 'add_action( \'init\', \'my_custom_post_types\' );'."\n";
		$code .= 'function my_custom_post_types()'."\n";
		$code .= '{'."\n";
	}
	
	if ( is_array( $export_cpts ) ) {
		
		foreach( $export_cpts as $key => $cpt ) $export_cpts[$key] = '\''.$cpt.'\'';
		
		$sql_export_cpts = implode( ',', $export_cpts );
		
		$sql = "SELECT
				option_id,
				option_name,
				option_value
			FROM
				$wpdb->options
			WHERE
				option_name IN ($sql_export_cpts)
			";
		
		$pre_results_export = $wpdb->get_results( $sql );
	
		// cptg_orderに従ってソート
		$cptg_order = get_option('cptg_order');
		
		if ( is_array( $cptg_order ) ) {
			$order = $cptg_order['cptg'];
			foreach( $order as $num ) {
				foreach( $pre_results_export as $pre_result ) {
					if ( $num == $pre_result->option_id ) {
						$results_export[] = $pre_result;
						break;
					}
				}
			}
			foreach( $pre_results_export as $pre_result ) {
				if ( !in_array( $pre_result->option_id, $order ) ) {
					$results_export[] = $pre_result;
				}
			}
		} else {
			$results_export = $pre_results;
		}
		
		if ( is_array( $results_export ) ) {
			foreach ( $results_export as $result ) {
				$cpt = unserialize( $result->option_value );
				
				/* --- Label Options Setting --- */
				
				$cpt_label = $cpt['label'] ? esc_html($cpt['label']) : esc_html($cpt['post_type']);
				
				$cpt_labels = array(
					'name' => $cpt_label,
					'singular_name' => $cpt['labels']['singular_label'] ? $cpt['labels']['singular_label'] : $cpt_label,
					'all_items' => $cpt['labels']['all_items'] ? $cpt['labels']['all_items'] : __('All Posts', 'cptg'),
					'add_new' => $cpt['labels']['add_new'] ? $cpt['labels']['add_new'] : __('Add New', 'cptg'),
					'add_new_item' => $cpt['labels']['add_new_item'] ? $cpt['labels']['add_new_item'] : __('Add New Post', 'cptg'),
					'edit_item' => $cpt['labels']['edit_item'] ? $cpt['labels']['edit_item'] : __('Edit Post', 'cptg'),
					'new_item' => $cpt['labels']['new_item'] ? $cpt['labels']['new_item'] : __('New Post', 'cptg'),
					'view_item' => $cpt['labels']['view_item'] ? $cpt['labels']['view_item'] : __('View Post', 'cptg'),
					'search_items' => $cpt['labels']['search_items'] ? $cpt['labels']['search_items'] : __('Search Posts', 'cptg'),
					'not_found' => $cpt['labels']['not_found'] ? $cpt['labels']['not_found'] : __('No posts found.', 'cptg'),
					'not_found_in_trash' => $cpt['labels']['not_found_in_trash'] ? $cpt['labels']['not_found_in_trash'] : __('No posts found in Trash.', 'cptg'),
					'parent_item_colon' => $cpt['labels']['parent_item_colon'] ? $cpt['labels']['parent_item_colon'] : __('Parent Page', 'cptg'),
					'menu_name' => $cpt['labels']['menu_name'] ? $cpt['labels']['menu_name'] : $cpt_label,
				);
				
				/* --- Advanced Options Setting --- */
				
				$cpt_rewrite_slug = $cpt['rewrite_slug'] ? esc_html($cpt['rewrite_slug']) : esc_html($cpt['post_type']);
				$cpt_menu_position = $cpt['menu_position'] ? intval($cpt['menu_position']) : null;
				$cpt_supports = $cpt['supports'] ? $cpt['supports'] : array(null);
				$cpt_menu_icon = $cpt['menu_icon'] ? $cpt['menu_icon'] : null;
				
				
				/* --- register_post_type() --- */
				
				$code .= "\t".'$labels = array('."\n";
				$code .= "\t\t".'\'name\' => \''.$cpt_label.'\','."\n";
				$code .= "\t\t".'\'singular_name\' => \''.$cpt_labels['singular_name'].'\','."\n";
				$code .= "\t\t".'\'menu_name\' => \''.$cpt_labels['menu_name'].'\','."\n";
				$code .= "\t\t".'\'all_items\' => \''.$cpt_labels['all_items'].'\','."\n";
				$code .= "\t\t".'\'add_new\' => \''.$cpt_labels['add_new'].'\','."\n";
				$code .= "\t\t".'\'add_new_item\' => \''.$cpt_labels['add_new_item'].'\','."\n";
				$code .= "\t\t".'\'edit_item\' => \''.$cpt_labels['edit_item'].'\','."\n";
				$code .= "\t\t".'\'new_item\' => \''.$cpt_labels['new_item'].'\','."\n";
				$code .= "\t\t".'\'view_item\' => \''.$cpt_labels['view_item'].'\','."\n";
				$code .= "\t\t".'\'search_items\' => \''.$cpt_labels['search_items'].'\','."\n";
				$code .= "\t\t".'\'not_found\' =>  \''.$cpt_labels['not_found'].'\','."\n";
				$code .= "\t\t".'\'not_found_in_trash\' => \''.$cpt_labels['not_found_in_trash'].'\','."\n"; 
				$code .= "\t\t".'\'parent_item_colon\' => \''.$cpt_labels['parent_item_colon'].'\','."\n";
				$code .= "\t".');'."\n";
				
				$code .= "\t".'$args = array('."\n";
				$code .= "\t\t".'\'labels\' => $labels,'."\n";
				if ( $cpt['description'] ) $code .= "\t\t".'\'description\' => \''.esc_html($cpt['description']).'\','."\n";
				$code .= "\t\t".'\'public\' => '.cptg_return_disp_boolean($cpt['public']).','."\n";
				$code .= "\t\t".'\'publicly_queryable\' => '.cptg_return_disp_boolean($cpt['publicly_queryable']).','."\n";
				$code .= "\t\t".'\'exclude_from_search\' => '.cptg_return_disp_boolean($cpt['exclude_from_search']).','."\n";
				$code .= "\t\t".'\'show_ui\' => '.cptg_return_disp_boolean($cpt['show_ui']).','."\n";
				$code .= "\t\t".'\'show_in_nav_menus\' => '.cptg_return_disp_boolean($cpt['show_in_nav_menus'] ).','."\n";
				$code .= "\t\t".'\'capability_type\' => \''.$cpt['capability_type'].'\','."\n";
				$code .= "\t\t".'\'has_archive\' => '.cptg_return_disp_boolean($cpt['has_archive']).','."\n";
				$code .= "\t\t".'\'hierarchical\' => '.cptg_return_disp_boolean($cpt['hierarchical']).','."\n";
				$code .= "\t\t".'\'rewrite\' => array( \'slug\' => \''.$cpt_rewrite_slug.'\' ),'."\n";
				$code .= "\t\t".'\'query_var\' => '.cptg_return_disp_boolean($cpt['query_var']).','."\n";
				$code .= "\t\t".'\'can_export\' => '.cptg_return_disp_boolean($cpt['can_export']).','."\n";
				if ( $cpt_menu_position ) $code .= "\t\t".'\'menu_position\' => '.$cpt_menu_position.','."\n";
				if ( $cpt_menu_icon ) $code .= "\t\t".'\'menu_icon\' => \''.$cpt_menu_icon.'\','."\n";
				
				if ( $cpt_supports != array(null) ) {
					$code .= "\t\t".'\'supports\' => array( ';
					foreach ( $cpt_supports as $support ) {
						$code .= '\''.$support.'\'';
						if ( $support != end($cpt_supports) ) $code .= ',';
					}
					$code .= ' ),'."\n";
				} else {
					$code .= "\t\t".'\'supports\' => array( null ),'."\n";
				}
				
				$code .= "\t".' );'."\n";
				$code .= "\t".'register_post_type( \''.$cpt['post_type'].'\', $args );'."\n";
	
			}
		}
	}
	
	if ( is_array( $export_taxs ) ) {
		foreach( $export_taxs as $key => $tax ) $export_taxs[$key] = '\''.$tax.'\'';
		
		$sql_export_taxs = implode( ',', $export_taxs );
		
		$sql = "SELECT
				option_id,
				option_name,
				option_value
			FROM
				$wpdb->options
			WHERE
				option_name IN ($sql_export_taxs)
			";
	 
		$results_tax_export = $wpdb->get_results($sql);
		
		if ( is_array( $results_tax_export ) ) {
			foreach ( $results_tax_export as $result ) {
	
				$tax = unserialize( $result->option_value );
				
				/* --- Label Options Setting --- */
				
				$tax_label = $tax['label'] ? esc_html($tax['label']) : esc_html($tax['taxonomy']);
				
				$tax_labels = array(
					'name' => $tax_label,
					'singular_name' => $tax['labels']['singular_label'] ? $tax['labels']['singular_label'] : $tax_label,
					'search_items' => $tax['labels']['search_items'] ? $tax['labels']['search_items'] : __('Search Tags', 'cptg'),
					'popular_items' => $tax['labels']['popular_items'] ? $tax['labels']['popular_items'] : __('Popular Tags', 'cptg'),
					'all_items' => $tax['labels']['all_items'] ? $tax['labels']['all_items'] : __('All Tags', 'cptg'),
					'parent_item' => $tax['labels']['parent_item'] ? $tax['labels']['parent_item'] : __('Parent Category', 'cptg'),
					'parent_item_colon' => $tax['labels']['parent_item_colon'] ? $tax['labels']['parent_item_colon'] : __('Parent Category', 'cptg'),
					'edit_item' => $tax['labels']['edit_item'] ? $tax['labels']['edit_item'] : __('Edit Tag', 'cptg'),
					'update_item' => $tax['labels']['update_item'] ? $tax['labels']['update_item'] : __('Update Tag', 'cptg'),
					'add_new_item' => $tax['labels']['add_new_item'] ? $tax['labels']['add_new_item'] : __('Add New Tag', 'cptg'),
					'new_item_name' => $tax['labels']['new_item_name'] ? $tax['labels']['new_item_name'] : __('New Tag Name', 'cptg'),
					'separate_items_with_commas' => $tax['labels']['separate_items_with_commas'] ? $tax['labels']['separate_items_with_commas'] : __('Separate tags with commas', 'cptg'),
					'add_or_remove_items' => $tax['labels']['add_or_remove_items'] ? $tax['labels']['add_or_remove_items'] : __('Add or remove tags', 'cptg'),
					'choose_from_most_used' => $tax['labels']['choose_from_most_used'] ? $tax['labels']['choose_from_most_used'] : __('Choose from the most used tags', 'cptg'),
				);
				
				/* --- Advanced Options Setting --- */
				
				$tax_rewrite_slug = $tax['rewrite_slug'] ? esc_html($tax['rewrite_slug']) : esc_html($tax['taxonomy']);
				$tax_post_types = $tax['post_types'];
				
				/* --- register_taxonomy() --- */
	
				$code .= "\t".'$labels = array('."\n";
				$code .= "\t\t".'\'name\' => \''.$tax_label.'\','."\n";
				$code .= "\t\t".'\'singular_name\' => \''.$tax_labels['singular_name'].'\','."\n";
				$code .= "\t\t".'\'search_items\' => \''.$tax_labels['search_items'].'\','."\n";
				$code .= "\t\t".'\'popular_items\' => \''.$tax_labels['popular_items'].'\','."\n";
				$code .= "\t\t".'\'all_items\' => \''.$tax_labels['all_items'].'\','."\n";
				$code .= "\t\t".'\'parent_item\' => \''.$tax_labels['parent_item'].'\','."\n";
				$code .= "\t\t".'\'parent_item_colon\' => \''.$tax_labels['parent_item_colon'].'\','."\n";
				$code .= "\t\t".'\'edit_item\' => \''.$tax_labels['edit_item'].'\','."\n";
				$code .= "\t\t".'\'update_item\' => \''.$tax_labels['update_item'].'\','."\n";
				$code .= "\t\t".'\'add_new_item\' => \''.$tax_labels['add_new_item'].'\','."\n";
				$code .= "\t\t".'\'new_item_name\' => \''.$tax_labels['new_item_name'].'\','."\n";
				$code .= "\t\t".'\'separate_items_with_commas\' => \''.$tax_labels['separate_items_with_commas'].'\','."\n";
				$code .= "\t\t".'\'add_or_remove_items\' => \''.$tax_labels['add_or_remove_items'].'\','."\n";
				$code .= "\t\t".'\'choose_from_most_used\' => \''.$tax_labels['choose_from_most_used'].'\','."\n";
				$code .= "\t".');'."\n";
				
				$code .= "\t".'$args = array('."\n";
				$code .= "\t\t".'\'labels\' => $labels,'."\n";
				$code .= "\t\t".'\'show_ui\' => '.cptg_return_disp_boolean($tax['show_ui']).','."\n";
				$code .= "\t\t".'\'hierarchical\' => '.cptg_return_disp_boolean($tax['hierarchical']).','."\n";
				
				$code .= "\t\t".'\'rewrite\' => array( \'slug\' => \''.$tax_rewrite_slug.'\' ),'."\n";
				$code .= "\t\t".'\'query_var\' => '.cptg_return_disp_boolean($tax['query_var']).','."\n";
				$code .= "\t".');'."\n";
				
				$code .= "\t".'register_taxonomy( \''.$tax['taxonomy'].'\', ';
				$code .= 'array( ';
				foreach ( $tax_post_types as $post_type ) {
					$code .= '\''.$post_type.'\'';
					if ( $post_type != end($tax_post_types) ) $code .= ',';
				}
				$code .= ' )';
				$code .= ' , $args );'."\n";
			}
		}
	}
	
	if ( is_array( $export_cpts ) || is_array( $export_taxs ) ) {
		$code .= '}';
	}
}

?>

<div class="wrap">

<?php screen_icon( 'plugins' ); ?>

<h2><?php _e('Export', 'cptg'); ?></h2>

<h3><?php _e('Export to PHP'); ?></h3>

<p><?php _e('Custom Post Type Generator will create the PHP code to include in your theme.', 'cptg') ?></p>

<p><?php _e('Registered Custom Post Type and Taxonomy will not appear in the list of editable object.', 'cptg') ?><br /><?php _e('This is useful for including Custom Post Type and Taxonomy in themes.', 'cptg') ?></p>

<ol>
	<li><?php _e('Select Custom Post Type(s) and Taxonomy(s) from the list and click "Export to PHP".', 'cptg') ?></li>
	<li><?php _e('Copy the PHP code generated.', 'cptg') ?></li>
	<li><?php _e('Paste into your functions.php file.', 'cptg') ?></li>
</ol>

<form id="cptg_export_form" method="post">

<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><?php _e('Custom Post Types', 'cptg'); ?></th>
			<td>
			<?php if ( count( $results ) ) : ?>
				<?php foreach ( $results as $result ) : ?>
				<?php $cpt = unserialize( $result->option_value ); ?>
				<label>
					<input type="checkbox" name="cptg_cpts[]" value="<?php echo $result->option_name; ?>" <?php if ( isset($_POST['cptg_cpts'] ) && in_array( $result->option_name, $_POST['cptg_cpts'] ) ) echo 'checked'; ?>>
					<?php echo stripslashes($cpt['post_type']); ?>
				</label><br />
				<?php endforeach; ?>
			<?php else : ?>
				<?php _e('No Custom Post Type found.', 'cptg') ?>
			<?php endif; ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Custom Taxonomies', 'cptg'); ?></th>
			<td>
			<?php if ( count( $results_tax ) ) : ?>
				<?php foreach ( $results_tax as $result ) : ?>
				<?php $tax = unserialize( $result->option_value ); ?>
				<label>
					<input type="checkbox" name="cptg_taxs[]" value="<?php echo $result->option_name; ?>" <?php if ( isset($_POST['cptg_taxs'] ) && in_array( $result->option_name, $_POST['cptg_taxs'] ) ) echo 'checked'; ?>>
					<?php echo stripslashes($tax["taxonomy"]); ?>
				</label><br />
				<?php endforeach; ?>
			<?php else : ?>
				<?php _e('No Taxonomy found.', 'cptg') ?>
			<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>

<p class="submit"><input type="submit" name="cptg_export" id="cptg_export" class="button button-primary" value="<?php _e('Export to PHP'); ?>"></p>

<textarea id="cptg_code" readonly="true" class="pre">
<?php echo $code; ?>
</textarea>

</form>

</div>
