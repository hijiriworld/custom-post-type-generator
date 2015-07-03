<?php
/*
Plugin Name: Custom Post Type Generator
Plugin URI: http://hijiriworld.com/web/plugins/custom-post-type-generator/
Description: Generate Custom Post Types and Custom Taxonomies, from the admin interface which is easy to understand. it's a must have for any user working with WordPress.
Author: hijiri
Author URI: http://hijiriworld.com/web/
Version: 2.3.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
* Define
*/

define( 'CPTG_URL', plugin_dir_path(__FILE__) );
load_plugin_textdomain( 'cptg', false, basename(dirname(__FILE__)).'/lang' );

/**
* Class & Method
*/

$cptg = new Cptg;

class Cptg
{
	function __construct()
	{
		if ( !get_option( 'cptg_activation' ) ) $this->cptg_activation();
		add_action( 'admin_menu', array( $this, 'add_menus' ) );
		add_action( 'admin_init', array( $this,'cptg_actions'));
		add_action( 'init', array( $this,'cptg_generate') );
		if ( strpos( $_SERVER['REQUEST_URI'], 'cptg-' ) > 0 || strpos( $_SERVER['REQUEST_URI'], '_tax' ) > 0 ) {
			add_action( 'admin_head', array( $this, 'cptg_js') );
			add_action( 'admin_head', array( $this, 'cptg_css') );
		}
		add_action( 'wp_ajax_update-cptg-order', array( $this, 'update_cptg_order' ) );
	}

	function cptg_activation()
	{
		global $wpdb;

		$sql = "
			SELECT option_id, option_name, option_value
			FROM $wpdb->options
			WHERE option_name LIKE '%%cptg_cpt%%'
			ORDER BY option_id ASC
			";
		$results = $wpdb->get_results($sql);

		if ( count( $results ) ) {
			foreach ( $results as $result ) {
				$cpt = unserialize( $result->option_value );

				if ( isset( $cpt['label'] ) ) {
					$cpt['labels']['name'] = $cpt['label'];
					unset( $cpt['label'] );
				}
				if ( isset( $cpt['labels']['singular_label'] ) ) {
					$cpt['labels']['singular_name'] = $cpt['labels']['singular_label'];
					unset( $cpt['labels']['singular_label'] );
				}
				if ( !isset( $cpt['labels']['name_admin_bar'] ) ) $cpt['labels']['name_admin_bar'] = '';

				if ( !isset( $cpt['show_in_menu'] ) ) {
					$cpt['show_in_menu'] = array(
						'show_in_menu' => $cpt['show_ui'],
						'string' => ''
					);
				}
				if ( !isset( $cpt['show_in_admin_bar'] ) ) $cpt['show_in_admin_bar'] = $cpt['show_ui'];

				$update_rewrite = array();
				if ( isset( $cpt['rewrite_slug'] ) ) { // before.2.2.2
					$update_rewrite = array(
						'rewrite' => $cpt['rewrite'],
						'slug' => $cpt['rewrite_slug'],
						'with_front' => 1,
						'feeds' => 0,
						'pages' => 1,
					);
					$cpt['rewrite'] = $update_rewrite;
					unset( $cpt['rewrite_slug'] );
				}

				$update_query_var = array();
				if ( !is_array( $cpt['query_var'] ) ) { // before 2.2.4
					$update_query_var = array(
						'query_var' => $cpt['query_var'],
						'string' => '',
					);
					$cpt['query_var'] = $update_query_var;
				}

				update_option( $result->option_name, $cpt );
			}
		}

		$sql = "
			SELECT option_id, option_name, option_value
			FROM $wpdb->options
			WHERE option_name LIKE '%%cptg_tax%%'
			ORDER BY option_id ASC
			";
		$results = $wpdb->get_results($sql);

		if ( count( $results) ) {
			foreach ( $results as $result ) {
				$tax = unserialize( $result->option_value );

				if ( isset( $tax['label'] ) ) {
					$tax['labels']['name'] = $tax['label'];
					unset( $tax['label'] );
				}
				if ( isset( $tax['labels']['singular_label'] ) ) {
					$tax['labels']['singular_name'] = $tax['labels']['singular_label'];
					unset( $tax['labels']['singular_label'] );
				}
				if ( !isset( $tax['labels']['menu_name'] ) ) $tax['labels']['menu_name'] = '';
				if ( !isset( $tax['labels']['view_item'] ) ) $tax['labels']['view_item'] = '';
				if ( !isset( $tax['labels']['not_found'] ) ) $tax['labels']['not_found'] = '';
				if ( !isset( $tax['labels']['show_admin_column'] ) ) $tax['show_admin_column'] = 0;

				if ( !isset( $tax['show_in_nav_menus'] ) ) $tax['show_in_nav_menus'] = $tax['public'];
				if ( !isset( $tax['show_tagcloud'] ) ) $tax['show_tagcloud'] = $tax['show_ui'];
				if ( !isset( $tax['sort'] ) ) $tax['sort'] = 0;

				$update_rewrite = array();
				if ( isset( $tax['rewrite_slug'] ) ) { // before.2.2.2
					$update_rewrite = array(
						'rewrite' => $tax['rewrite'],
						'slug' => $tax['rewrite_slug'],
						'with_front' => 1,
						'hierarchical' => 0,
					);
					$tax['rewrite'] = $update_rewrite;
					unset( $tax['rewrite_slug'] );
				}

				$update_query_var = array();
				if ( !is_array( $tax['query_var']) ) { // before 2.2.4
					$update_query_var = array(
						'query_var' => $tax['query_var'],
						'string' => '',
					);
					$tax['query_var'] = $update_query_var;
				}
				update_option( $result->option_name, $tax );
			}
		}
		update_option( 'cptg_activation', 1 );
		delete_option( 'cptg_version' ); // before ver.2.3.1
	}

	function add_menus()
	{
		$menu_top = add_utility_page(__('Custom Post Type', 'cptg'), __('Custom Post Type', 'cptg'),  'administrator', 'cptg-manage-cpt', array( $this,'manage_cpt' ) );
		add_submenu_page( $menu_top, __('Add New', 'cptg'), __('Add New', 'cptg'), 'administrator', 'cptg-regist-cpt', array( $this,'regist_cpt' ) );
		add_submenu_page( 'cptg-manage-cpt', __('Custom Taxonomy', 'cptg'), __('Custom Taxonomy', 'cptg'), 'administrator', 'cptg-manage-tax', array( $this,'manage_tax' ) );
		add_submenu_page( $menu_top, __('Add New', 'cptg'), __('Add New', 'cptg'), 'administrator', 'cptg-regist-tax', array( $this,'regist_tax' ) );
		add_submenu_page( 'cptg-manage-cpt', __('Export', 'cptg'), __('Export', 'cptg'), 'administrator', 'cptg-export', array( $this,'export' ) );
	}

	function manage_cpt()
	{
		require CPTG_URL.'include/manage_cpt.php';
	}
	function manage_tax()
	{
		require CPTG_URL.'include/manage_tax.php';
	}
	function regist_cpt()
	{
		require CPTG_URL.'include/regist_cpt.php';
	}
	function regist_tax()
	{
		require CPTG_URL.'include/regist_tax.php';
	}
	function export()
	{
		require CPTG_URL.'include/export.php';
	}
	function cptg_js()
	{
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'cptg', plugins_url('/js/cptg.js', __FILE__) );
	}
	function cptg_css()
	{
		wp_enqueue_style( 'cptg', plugins_url('/css/cptg.css', __FILE__), array(), null );
	}

	function update_cptg_order()
	{
		parse_str($_POST['order'], $data);
		if ( is_array($data) ) {
			update_option('cptg_order', $data);
		}
	}

	/*
	* Init: Generate Custom Post Types & Taxonomies
	*/

	function cptg_generate()
	{
		global $wpdb;

		$results = $pre_result = array();

		$sql = "
			SELECT option_id, option_name, option_value
			FROM $wpdb->options
			WHERE option_name LIKE '%%cptg_cpt%%'
			ORDER BY option_id ASC
			";

		$pre_results = $wpdb->get_results($sql);

		// sort from 'cptg_order'
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

		if ( count( $results ) ) {
			foreach ( $results as $result ) {

				$cpt = unserialize( $result->option_value );

				// labels
				$cpt_labels = array();
				$cpt_labels['name'] = $cpt['labels']['name'] ? $cpt['labels']['name'] : $cpt['post_type'];
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
				$cpt_labels['parent_item_colon'] = $cpt['labels']['parent_item_colon'] ? esc_html( $cpt['labels']['parent_item_colon'] ) : __('Parent Page');

				// $args
				$args = array(
					'labels'				=> $cpt_labels,
					'description'			=> esc_html( $cpt['description'] ),
					'public'				=> cptg_return_boolean( $cpt['public'] ),
					'exclude_from_search'	=> cptg_return_boolean( $cpt['exclude_from_search'] ),
					'publicly_queryable'	=> cptg_return_boolean( $cpt['publicly_queryable'] ),
					'show_ui'				=> cptg_return_boolean( $cpt['show_ui'] ),
					'show_in_nav_menus'		=> cptg_return_boolean( $cpt['show_in_nav_menus'] ),
					'show_in_menu'			=> $cpt['show_in_menu']['show_in_menu'] && $cpt['show_in_menu']['string'] ? $cpt['show_in_menu']['string'] : cptg_return_boolean( $cpt['show_in_menu']['show_in_menu'] ),
					'show_in_admin_bar'		=> cptg_return_boolean( $cpt['show_in_admin_bar'] ),
					'has_archive'			=> cptg_return_boolean( $cpt['has_archive'] ),
					'hierarchical'			=> cptg_return_boolean( $cpt['hierarchical'] ),
					'rewrite'				=> $cpt['rewrite']['rewrite'] ? $cpt['rewrite'] : cptg_return_boolean( $cpt['rewrite']['rewrite'] ),
					'query_var'				=> $cpt['query_var']['query_var'] && $cpt['query_var']['string'] ? $cpt['query_var']['string'] : cptg_return_boolean( $cpt['query_var']['query_var'] ),
					'can_export'			=> cptg_return_boolean( $cpt['can_export'] ),
					'menu_position'			=> $cpt['menu_position'] ? intval($cpt['menu_position']) : null,
					'menu_icon'				=> $cpt['menu_icon'] ? esc_html( $cpt['menu_icon'] ) : null,
					'supports'				=> count( $cpt['supports'] ) ? $cpt['supports'] : cptg_return_boolean( 0 ),
				);
				register_post_type( $cpt['post_type'], $args );
			}
		}

		$results = array();

		$sql = "
			SELECT option_id, option_name, option_value
			FROM $wpdb->options
			WHERE option_name LIKE '%%cptg_tax%%'
			ORDER BY option_id ASC
			";

		$results = $wpdb->get_results($sql);

		if ( count( $results ) ) {
			foreach ( $results as $result ) {

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

				// $args
				$args = array(
					'labels'				=> $tax_labels,
					'public'				=> cptg_return_boolean( $tax['public'] ),
					'show_ui'				=> cptg_return_boolean( $tax['show_ui'] ),
					'show_in_nav_menus'		=> cptg_return_boolean( $tax['show_in_nav_menus'] ),
					'show_tagcloud'			=> cptg_return_boolean( $tax['show_tagcloud'] ),
					// since 2.3.7
					'meta_box_cb'			=> isset( $tax['meta_box_cb'] ) ? cptg_return_null_false( $tax['meta_box_cb'] ) : null,
					'show_admin_column'		=> cptg_return_boolean( $tax['show_admin_column'] ),
					'hierarchical'			=> cptg_return_boolean( $tax['hierarchical'] ),
					'query_var'				=> $tax['query_var']['query_var'] && $tax['query_var']['string'] ? $tax['query_var']['string'] : cptg_return_boolean( $tax['query_var']['query_var'] ),
					'rewrite'				=> $tax['rewrite']['rewrite'] ? $tax['rewrite'] : cptg_return_boolean( $tax['rewrite']['rewrite'] ),
					'sort'					=> cptg_return_boolean( $tax['sort'] ),
				);

				register_taxonomy( $tax['taxonomy'], $tax['post_types'], $args );
			}
		}
	}

	/*
	* Actions
	*/

	function add_cpt()
	{
		check_admin_referer( 'nonce_regist_cpt' );

		$input_data = $_POST['input_cpt'];
		if ( !isset( $input_data['supports'] ) ) $input_data['supports'] = array();

		update_option( uniqid('cptg_cpt_'), $input_data );
		wp_redirect( 'admin.php?page=cptg-manage-cpt&msg=add' );
	}
	function edit_cpt()
	{
		check_admin_referer( 'nonce_regist_cpt' );
		$key = $_POST['key'];

		$input_data = $_POST['input_cpt'];
		if ( !isset( $input_data['supports'] ) ) $input_data['supports'] = array();

		update_option( $key, $input_data );
		wp_redirect( 'admin.php?page=cptg-manage-cpt&msg=edit' );
	}
	function delete_cpt()
	{
		global $wpdb;

		check_admin_referer( 'nonce_del_cpt' );
		$key = $_GET['key'];

		// delete id from cptg_order
		$cptg_order = get_option('cptg_order');
		if ( $cptg_order ) {
			$result = $wpdb->get_results( "SELECT option_id FROM $wpdb->options WHERE option_name = '$key'", OBJECT );
			$key_id = $result[0]->option_id;
			$order = $cptg_order['cptg'];
			$offset = array_keys($order, $key_id);
			if ( !empty($offset) ) {
				array_splice($order, $offset[0], 1);
				update_option('cptg_order', array( 'cptg' => $order ) );
			}
		}

		// delete cptg_xxx
		delete_option( $key );

		wp_redirect( 'admin.php?page=cptg-manage-cpt&msg=del' );
	}
	function add_tax()
	{
		check_admin_referer( 'nonce_regist_tax' );
		$input_data = $_POST['input_tax'];
		update_option( uniqid('cptg_tax_'), $input_data );
		wp_redirect( 'admin.php?page=cptg-manage-tax&msg=add' );
	}
	function edit_tax()
	{
		check_admin_referer( 'nonce_regist_tax' );
		$key = $_POST['key'];
		$input_data = $_POST['input_tax'];
		update_option( $key, $input_data );
		wp_redirect( 'admin.php?page=cptg-manage-tax&msg=edit' );
	}
	function delete_tax()
	{
		check_admin_referer( 'nonce_del_tax' );
		$key = $_GET['key'];
		delete_option( $key );
		wp_redirect( 'admin.php?page=cptg-manage-tax&msg=del' );
	}


	function cptg_actions()
	{
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'del_cpt' ) {
			$this->delete_cpt();
		}
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'del_tax' ) {
			$this->delete_tax();
		}
		if ( isset( $_POST['cpt_submit'] ) ) {
			isset( $_POST['key'] ) ? $this->edit_cpt() : $this->add_cpt();
		}
		if ( isset( $_POST['tax_submit'] ) ) {
			isset( $_POST['key'] ) ? $this->edit_tax() : $this->add_tax();
		}
	}
}

/**
* Method
*/

function cptg_return_boolean( $obj )
{
	return $obj ? true : false;
}
function cptg_return_null_false( $obj )
{
	return $obj ? null : false;	
}
function cptg_return_disp_boolean( $obj )
{
	return $obj ? 'true' : 'false';
}
function cptg_return_disp_null_false( $obj )
{
	return $obj ? 'null' : 'false';
}
function echo_boolean_options( $obj, $default )
{
	if ( isset( $obj ) ) {
		if ( $obj == 0 ) {
			echo '<option value="0" selected="selected">false</option>';
			echo '<option value="1">true</option>';
		} else if( $obj == 1 ) {
			echo '<option value="0">false</option>';
			echo '<option value="1" selected="selected">true</option>';
		}
	} else {
		if ( $default == 0 ) {
			echo '<option value="0" selected="selected">false</option>';
			echo '<option value="1">true</option>';
		} else if( $default == 1 ) {
			echo '<option value="0">false</option>';
			echo '<option value="1" selected="selected">true</option>';
		}
	}
}
function echo_boolean_null_false_options( $obj, $default )
{
	if ( isset( $obj ) ) {
		if ( $obj == 0 ) {
			echo '<option value="0" selected="selected">false</option>';
			echo '<option value="1">null</option>';
		} else if( $obj == 1 ) {
			echo '<option value="0">false</option>';
			echo '<option value="1" selected="selected">null</option>';
		}
	} else {
		if ( $default == 0 ) {
			echo '<option value="0" selected="selected">false</option>';
			echo '<option value="1">null</option>';
		} else if( $default == 1 ) {
			echo '<option value="0">false</option>';
			echo '<option value="1" selected="selected">null</option>';
		}
	}
}
?>
