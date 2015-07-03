<?php

global $wpdb;

$results = $pre_result = $results_tax = array();

$sql = "
	SELECT option_id, option_name, option_value
	FROM $wpdb->options
	WHERE option_name LIKE '%%cptg_cpt%%'
	ORDER BY option_id ASC
	";

$pre_results = $wpdb->get_results($sql);

// cptg_orderに従ってソート
$cptg_order = get_option('cptg_order');

if ( $cptg_order ) {
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

$sql = "
	SELECT option_id, option_name, option_value
	FROM $wpdb->options
	WHERE option_name LIKE '%%cptg_tax%%'
	ORDER BY option_id ASC
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

		$code .= 'add_action( \'init\', \'cptg_custom_post_types\' );'."\n";
		$code .= 'function cptg_custom_post_types()'."\n";
		$code .= '{'."\n";

		if ( is_array( $export_cpts ) ) {

			foreach( $export_cpts as $key => $cpt ) $export_cpts[$key] = '\''.$cpt.'\'';

			$sql_export_cpts = implode( ',', $export_cpts );

			$sql = "
				SELECT option_id, option_name, option_value
				FROM $wpdb->options
				WHERE option_name IN ($sql_export_cpts)
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

					// $labels

					$cpt_labels = array();
					$cpt_labels['name'] = $cpt['labels']['name'] ? esc_html( $cpt['labels']['name'] ) : $cpt['post_type'];
					$cpt_labels['singular_name'] = $cpt['labels']['singular_name'] ? esc_html( $cpt['labels']['singular_name'] ) : $cpt_labels['name'];
					$cpt_labels['menu_name'] = $cpt['labels']['menu_name'] ? esc_html( $cpt['labels']['menu_name'] ) : $cpt_labels['name'];
					$cpt_labels['name_admin_bar'] = $cpt['labels']['name_admin_bar'] ? esc_html( $cpt['labels']['name_admin_bar'] ) : $cpt_labels['singular_name'];
					$cpt_labels['all_items'] = $cpt['labels']['all_items'] ? esc_html( $cpt['labels']['all_items'] ) : __('All Posts');
					$cpt_labels['add_new'] = $cpt['labels']['add_new'] ? esc_html( $cpt['labels']['add_new'] ) : __('Add New', 'cptg');
					$cpt_labels['add_new_item'] = $cpt['labels']['add_new_item'] ? esc_html( $cpt['labels']['add_new_item'] ) : __('Add New Post');
					$cpt_labels['edit_item'] = $cpt['labels']['edit_item'] ? esc_html( $cpt['labels']['edit_item'] ) : __('Edit Post');
					$cpt_labels['new_item'] = $cpt['labels']['new_item'] ? esc_html( $cpt['labels']['new_item'] ) : __('New Post');
					$cpt_labels['view_item'] = $cpt['labels']['view_item'] ? esc_html( $cpt['labels']['view_item'] ) : __('View Post');
					$cpt_labels['search_items'] = $cpt['labels']['search_items'] ? esc_html( $cpt['labels']['search_items'] ) : __('Search Posts');
					$cpt_labels['not_found'] = $cpt['labels']['not_found'] ? esc_html( $cpt['labels']['not_found'] ) : __('No posts found.');
					$cpt_labels['not_found_in_trash'] = $cpt['labels']['not_found_in_trash'] ? esc_html( $cpt['labels']['not_found_in_trash'] ) : __('No posts found in Trash.');
					$cpt_labels['parent_item_colon'] = $cpt['labels']['parent_item_colon'] ? esc_html( $cpt['labels']['parent_item_colon'] ) : __('Parent Page', 'cptg');

					$code .= "\t".'$labels = array('."\n";
					$code .= "\t\t".'\'name\' => \''.$cpt_labels['name'].'\','."\n";
					$code .= "\t\t".'\'singular_name\' => \''.$cpt_labels['singular_name'].'\','."\n";
					$code .= "\t\t".'\'menu_name\' => \''.$cpt_labels['menu_name'].'\','."\n";
					$code .= "\t\t".'\'name_admin_bar\' => \''.$cpt_labels['name_admin_bar'].'\','."\n";
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

					// $args

					$code .= "\t".'$args = array('."\n";
					$code .= "\t\t".'\'labels\' => $labels,'."\n";
					if ( $cpt['description'] ) $code .= "\t\t".'\'description\' => \''.esc_html($cpt['description']).'\','."\n";
					$code .= "\t\t".'\'public\' => '.cptg_return_disp_boolean($cpt['public']).','."\n";
					$code .= "\t\t".'\'exclude_from_search\' => '.cptg_return_disp_boolean($cpt['exclude_from_search']).','."\n";
					$code .= "\t\t".'\'publicly_queryable\' => '.cptg_return_disp_boolean($cpt['publicly_queryable']).','."\n";
					$code .= "\t\t".'\'show_ui\' => '.cptg_return_disp_boolean($cpt['show_ui']).','."\n";
					$code .= "\t\t".'\'show_in_nav_menus\' => '.cptg_return_disp_boolean($cpt['show_in_nav_menus'] ).','."\n";

					if ( $cpt['show_in_menu']['string'] ) {
						$code .= "\t\t".'\'show_in_menu\' => \''.esc_html( $cpt['show_in_menu']['string'] ).'\','."\n";
					} else {
						$code .= "\t\t".'\'show_in_menu\' => '.cptg_return_disp_boolean($cpt['show_in_menu']['show_in_menu']).','."\n";
					}

					$code .= "\t\t".'\'show_in_admin_bar\' => '.cptg_return_disp_boolean($cpt['show_in_admin_bar']).','."\n";

					$code .= "\t\t".'\'has_archive\' => '.cptg_return_disp_boolean($cpt['has_archive']).','."\n";

					if ( $cpt['menu_position'] ) {
						$code .= "\t\t".'\'menu_position\' => '.intval( $cpt['menu_position'] ).','."\n";
					} else {
						$code .= "\t\t".'\'menu_position\' => null,'."\n";
					}
					if ( $cpt['menu_icon'] ) {
						$code .= "\t\t".'\'menu_icon\' => \''.$cpt['menu_icon'].'\','."\n";
					} else {
						$code .= "\t\t".'\'menu_icon\' => null,'."\n";
					}

					$code .= "\t\t".'\'hierarchical\' => '.cptg_return_disp_boolean($cpt['hierarchical']).','."\n";

					if ( $cpt['rewrite']['rewrite'] ) {
						$code .= "\t\t".'\'rewrite\' => array( ';
						if ( $cpt['rewrite']['slug'] ) {
							$code .= '\'slug\' => \''.esc_html($cpt['rewrite']['slug']).'\',';
						} else {
							$code .= '\'slug\' => \''.$cpt['post_type'].'\',';
						}
						$code .= '\'with_front\' => '.cptg_return_disp_boolean($cpt['rewrite']['with_front']).',';
						$code .= '\'feeds\' => '.cptg_return_disp_boolean($cpt['rewrite']['feeds']).',';
						$code .= '\'pages\' => '.cptg_return_disp_boolean($cpt['rewrite']['pages']);
						$code .= ' ),'."\n";
					} else {
						$code .= "\t\t".'\'rewrite\' => '.cptg_return_disp_boolean( $cpt['rewrite']['rewrite'] ).','."\n";
					}

					if ( $cpt['query_var']['query_var'] && $cpt['query_var']['string'] ) {
						$code .= "\t\t".'\'query_var\' => \''.esc_html($cpt['query_var']['string']).'\','."\n";
					} else {
						$code .= "\t\t".'\'query_var\' => '.cptg_return_disp_boolean($cpt['query_var']['query_var']).','."\n";
					}

					$code .= "\t\t".'\'can_export\' => '.cptg_return_disp_boolean($cpt['can_export']).','."\n";

					if ( count( $cpt['supports'] ) ) {
						$code .= "\t\t".'\'supports\' => array( ';
						foreach ( $cpt['supports'] as $support ) {
							$code .= '\''.$support.'\'';
							if ( $support != end($cpt['supports']) ) $code .= ',';
						}
						$code .= ' ),'."\n";
					} else {
						$code .= "\t\t".'\'supports\' => '.cptg_return_disp_boolean( 0 ).','."\n";
					}

					$code .= "\t".');'."\n";
					$code .= "\t".'register_post_type( \''.$cpt['post_type'].'\', $args );'."\n";

				}
			}
		}

		if ( is_array( $export_taxs ) ) {
			foreach( $export_taxs as $key => $tax ) $export_taxs[$key] = '\''.$tax.'\'';

			$sql_export_taxs = implode( ',', $export_taxs );

			$sql = "
				SELECT option_id, option_name, option_value
				FROM $wpdb->options
				WHERE option_name IN ($sql_export_taxs)
				";

			$results_tax_export = $wpdb->get_results($sql);

			if ( is_array( $results_tax_export ) ) {
				foreach ( $results_tax_export as $result ) {

					$tax = unserialize( $result->option_value );

					// $labels

					$tax_labels = array();
					$tax_labels['name'] = $tax['labels']['name'] ? esc_html($tax['labels']['name']) : $tax['taxonomy'];
					$tax_labels['singular_name'] = $tax['labels']['singular_name'] ? esc_html($tax['labels']['singular_name']) : $tax_labels['name'];
					$tax_labels['menu_name'] = $tax['labels']['menu_name'] ? esc_html($tax['labels']['menu_name']) : $tax_labels['name'];
					$tax_labels['all_items'] = $tax['labels']['all_items'] ? esc_html($tax['labels']['all_items']) : __('All Tags');
					$tax_labels['edit_item'] = $tax['labels']['edit_item'] ? esc_html($tax['labels']['edit_item']) : __('Edit Tag');
					$tax_labels['view_item'] = $tax['labels']['view_item'] ? esc_html($tax['labels']['view_item']) : __('View Tag');
					$tax_labels['update_item'] = $tax['labels']['update_item'] ? esc_html($tax['labels']['update_item']) : __('Update Tag');
					$tax_labels['add_new_item'] = $tax['labels']['add_new_item'] ? esc_html($tax['labels']['add_new_item']) : __('Add New Tag');
					$tax_labels['new_item_name'] = $tax['labels']['new_item_name'] ? esc_html($tax['labels']['new_item_name']) : __('New Tag Name');
					$tax_labels['parent_item'] = $tax['labels']['parent_item'] ? esc_html($tax['labels']['parent_item']) : __('Parent Category');
					$tax_labels['parent_item_colon'] = $tax['labels']['parent_item_colon'] ? esc_html($tax['labels']['parent_item_colon']) : __('Parent Category');
					$tax_labels['search_items'] = $tax['labels']['search_items'] ? esc_html($tax['labels']['search_items']) : __('Search Tags');
					$tax_labels['popular_items'] = $tax['labels']['popular_items'] ? esc_html($tax['labels']['popular_items']) : __('Popular Tags');
					$tax_labels['separate_items_with_commas'] = $tax['labels']['separate_items_with_commas'] ? esc_html($tax['labels']['separate_items_with_commas']) : __('Separate tags with commas');
					$tax_labels['add_or_remove_items'] = $tax['labels']['add_or_remove_items'] ? esc_html($tax['labels']['add_or_remove_items']) : __('Add or remove tags');
					$tax_labels['choose_from_most_used'] = $tax['labels']['choose_from_most_used'] ? esc_html($tax['labels']['choose_from_most_used']) : __('Choose from the most used tags');
					$tax_labels['not_found'] = $tax['labels']['not_found'] ? esc_html($tax['labels']['not_found']) : __('No tags found.');


					$code .= "\t".'$labels = array('."\n";
					$code .= "\t\t".'\'name\' => \''.$tax_labels['name'].'\','."\n";
					$code .= "\t\t".'\'singular_name\' => \''.$tax_labels['singular_name'].'\','."\n";
					$code .= "\t\t".'\'menu_name\' => \''.$tax_labels['menu_name'].'\','."\n";
					$code .= "\t\t".'\'all_items\' => \''.$tax_labels['all_items'].'\','."\n";
					$code .= "\t\t".'\'edit_item\' => \''.$tax_labels['edit_item'].'\','."\n";
					$code .= "\t\t".'\'view_item\' => \''.$tax_labels['view_item'].'\','."\n";
					$code .= "\t\t".'\'update_item\' => \''.$tax_labels['update_item'].'\','."\n";
					$code .= "\t\t".'\'add_new_item\' => \''.$tax_labels['add_new_item'].'\','."\n";
					$code .= "\t\t".'\'new_item_name\' => \''.$tax_labels['new_item_name'].'\','."\n";
					$code .= "\t\t".'\'parent_item\' => \''.$tax_labels['parent_item'].'\','."\n";
					$code .= "\t\t".'\'parent_item_colon\' =>  \''.$tax_labels['parent_item_colon'].'\','."\n";
					$code .= "\t\t".'\'search_items\' => \''.$tax_labels['search_items'].'\','."\n";
					$code .= "\t\t".'\'popular_items\' => \''.$tax_labels['popular_items'].'\','."\n";
					$code .= "\t\t".'\'separate_items_with_commas\' => \''.$tax_labels['separate_items_with_commas'].'\','."\n";
					$code .= "\t\t".'\'add_or_remove_items\' => \''.$tax_labels['add_or_remove_items'].'\','."\n";
					$code .= "\t\t".'\'choose_from_most_used\' => \''.$tax_labels['choose_from_most_used'].'\','."\n";
					$code .= "\t\t".'\'not_found\' => \''.$tax_labels['not_found'].'\','."\n";
					$code .= "\t".');'."\n";

					// $args

					$code .= "\t".'$args = array('."\n";
					$code .= "\t\t".'\'labels\' => $labels,'."\n";
					$code .= "\t\t".'\'public\' => '.cptg_return_disp_boolean($tax['public']).','."\n";
					$code .= "\t\t".'\'show_ui\' => '.cptg_return_disp_boolean($tax['show_ui']).','."\n";
					$code .= "\t\t".'\'show_in_nav_menus\' => '.cptg_return_disp_boolean($tax['show_in_nav_menus']).','."\n";
					$code .= "\t\t".'\'show_tagcloud\' => '.cptg_return_disp_boolean($tax['show_tagcloud']).','."\n";
					
					// since 2.3.7
					$code .= isset( $tax['meta_box_cb'] ) ? "\t\t".'\'meta_box_cb\' => '.cptg_return_disp_null_false($tax['meta_box_cb']).','."\n" : "\t\t".'\'meta_box_cb\' => null,'."\n";
					
					$code .= "\t\t".'\'show_admin_column\' => '.cptg_return_disp_boolean($tax['show_admin_column']).','."\n";
					$code .= "\t\t".'\'hierarchical\' => '.cptg_return_disp_boolean($tax['hierarchical']).','."\n";

					if ( $tax['query_var']['query_var'] && $tax['query_var']['string'] ) {
						$code .= "\t\t".'\'query_var\' => \''.esc_html($tax['query_var']['string']).'\','."\n";
					} else {
						$code .= "\t\t".'\'query_var\' => '.cptg_return_disp_boolean($tax['query_var']['query_var']).','."\n";
					}

					if ( $tax['rewrite']['rewrite'] ) {
						$code .= "\t\t".'\'rewrite\' => array( ';
						if ( $tax['rewrite']['slug'] ) {
							$code .= '\'slug\' => \''.esc_html($tax['rewrite']['slug']).'\',';
						} else {
							$code .= '\'slug\' => \''.$tax['taxonomy'].'\',';
						}
						$code .= '\'with_front\' => '.cptg_return_disp_boolean($tax['rewrite']['with_front']).',';
						$code .= '\'hierarchical\' => '.cptg_return_disp_boolean($tax['rewrite']['hierarchical']).',';
						$code .= ' ),'."\n";
					} else {
						$code .= "\t\t".'\'rewrite\' => '.cptg_return_disp_boolean( $tax['rewrite']['rewrite'] ).','."\n";
					}

					$code .= "\t\t".'\'sort\' => '.cptg_return_disp_boolean($tax['sort']).','."\n";
					$code .= "\t".');'."\n";

					$code .= "\t".'register_taxonomy( \''.$tax['taxonomy'].'\', ';
					$code .= 'array( ';
					foreach ( $tax['post_types'] as $post_type ) {
						$code .= '\''.$post_type.'\'';
						if ( $post_type != end($tax['post_types']) ) $code .= ',';
					}
					$code .= ' )';
					$code .= ' , $args );'."\n";
				}
			}
		}

		$code .= '}'."\n\n";

		$code .= 'add_action( \'after_switch_theme\', \'cptg_rewrite_flush\' );'."\n";
		$code .= 'function cptg_rewrite_flush()'."\n";
		$code .= '{'."\n";;
		$code .= "\t".'flush_rewrite_rules();'."\n";
		$code .= '}'."\n";
	}
}

?>

<div class="wrap">

<?php screen_icon( 'plugins' ); ?>

<h2><?php _e('Export', 'cptg'); ?></h2>

<h3><?php _e('Export to PHP'); ?></h3>

<p><?php _e('Custom Post Type Generator will create the PHP code to include in your theme.', 'cptg') ?></p>

<p><?php _e('Registered Custom Post Type and Taxonomy will not appear in the list of editable object.', 'cptg') ?><br><?php _e('This is useful for including Custom Post Type and Taxonomy in themes.', 'cptg') ?></p>

<ol>
	<li><?php _e('Select Custom Post Type(s) and Taxonomy(s) from the list and click "Export to PHP".', 'cptg') ?></li>
	<li><?php _e('Copy the PHP code generated.', 'cptg') ?></li>
	<li><?php _e('Paste into your functions.php file.', 'cptg') ?></li>
</ol>

<form id="cptg_export_form" method="post">

<div id="cptg_export_objects">
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
					</label><br>
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
					</label><br>
					<?php endforeach; ?>
				<?php else : ?>
					<?php _e('No Taxonomy found.', 'cptg') ?>
				<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<p><label><input type="checkbox" id="cptg_export_allcheck"> <?php _e( 'All Check', 'cptg' ) ?></label></p>

<p class="submit"><input type="submit" name="cptg_export" id="cptg_export" class="button button-primary" value="<?php _e('Export to PHP'); ?>"></p>

<textarea id="cptg_code" readonly="true" class="pre">
<?php echo $code; ?>
</textarea>

</form>

</div>
