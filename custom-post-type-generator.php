<?php
/*
Plugin Name: Custom Post Type Generator
Plugin URI: http://hijiriworld.com/web/plugins/custom-post-type-generator/
Description: Generate Custom Post Types and Custom Taxonomies, from the admin interface which is easy to understand. it's a must have for any user working with WordPress.
Author: hijiri
Author URI: http://hijiriworld.com/web/
Version: 1.0.1
*/

/*  Copyright 2013 hijiri

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/***************************************************************

	Define

***************************************************************/

define( 'CPTG_URL', plugin_dir_path(__FILE__) );

load_plugin_textdomain( 'cptg', false, basename(dirname(__FILE__)).'/lang' );

/***************************************************************

	CPTG Class

***************************************************************/

$cptg = new Cptg;

class Cptg
{
	function __construct()
	{
		// add_option('cptg_cpts');
		// add_option('hcpt_taxs');
		
		// add_menu
		add_action( 'admin_menu', array($this, 'add_menus' ));
		
		// generate
		add_action( 'init', array($this,'generate_ctps'), 0 );
		add_action( 'init', array($this,'generate_taxs'), 0 );
		
		// actions
		add_action( 'admin_init', array($this,'cptg_actions'));
		
		// load JavaScript and CSS
		if ( strpos( $_SERVER['REQUEST_URI'], '_cpt' ) > 0 || strpos( $_SERVER['REQUEST_URI'], '_tax' ) > 0 ) {
			add_action( 'admin_head', array($this, 'cptg_js') );
			add_action( 'admin_head', array($this, 'cptg_css') );
		}
	}
	
	function add_menus()
	{
		$menu_top = add_utility_page(__('Custom Post Type', 'cptg'), __('Custom Post Type', 'cptg'),  'administrator', 'manage_cpt', array($this,'manage_cpt'));
		add_submenu_page( $menu_top, __('Add New', 'cptg'), __('Add New', 'cptg'), 'administrator', 'regist_cpt', array($this,'regist_cpt'));
		add_submenu_page( 'manage_cpt', __('Custom Taxonomy', 'cptg'), __('Custom Taxonomy', 'cptg'), 'administrator', 'manage_tax', array($this,'manage_tax'));
		add_submenu_page( $menu_top, __('Add New', 'cptg'), __('Add New', 'cptg'), 'administrator', 'regist_tax', array($this,'regist_tax'));
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
	
	function cptg_js()
	{
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'cptg', plugins_url('/js/cptg.js', __FILE__) );
	}
	function cptg_css()
	{
		wp_enqueue_style( 'cptg', plugins_url('/css/cptg.css', __FILE__), array(), null );
    }
	
	/************************************************************************************************
				
		Genarate Custom Post Type
				
	************************************************************************************************/
	
	function generate_ctps()
	{
		
		// get_option custom post types
		$cptg_cpts = get_option('cptg_cpts');
		
		if ( is_array( $cptg_cpts ) ) {
			foreach ($cptg_cpts as $cpt) {
				
				/* --- Label Options Initialize --- */
				
				$cpt_label = $cpt["label"] ? esc_html($cpt["label"]) : esc_html($cpt["post_type"]);

					$cpt_labels['singular_name'] = $cpt["labels"]["singular_label"] ? $cpt["labels"]["singular_label"] :				$cpt_label;
					$cpt_labels['menu_name']			= $cpt["labels"]["menu_name"] ? $cpt["labels"]["menu_name"] :					$cpt_label;
					$cpt_labels['all_items']			= $cpt["labels"]["all_items"] ? $cpt["labels"]["all_items"] :					__('All Posts', 'cptg');
					$cpt_labels['add_new']				= $cpt["labels"]["add_new"] ? $cpt["labels"]["add_new"] :						__('Add New', 'cptg');
					$cpt_labels['add_new_item']			= $cpt["labels"]["add_new_item"] ? $cpt["labels"]["add_new_item"] :				__('Add New Post', 'cptg');
					$cpt_labels['edit_item']			= $cpt["labels"]["edit_item"] ? $cpt["labels"]["edit_item"] :					__('Edit Post', 'cptg');
					$cpt_labels['new_item']				= $cpt["labels"]["new_item"] ? $cpt["labels"]["new_item"] :						__('New Post', 'cptg');
					$cpt_labels['view_item']			= $cpt["labels"]["view_item"] ? $cpt["labels"]["view_item"] :					__('View Post', 'cptg');
					$cpt_labels['search_items']			= $cpt["labels"]["search_items"] ? $cpt["labels"]["search_items"] :				__('Search Posts', 'cptg');
					$cpt_labels['not_found']			= $cpt["labels"]["not_found"] ? $cpt["labels"]["not_found"] :					__('No posts found.', 'cptg');
					$cpt_labels['not_found_in_trash']	= $cpt["labels"]["not_found_in_trash"] ? $cpt["labels"]["not_found_in_trash"] :	__('No posts found in Trash.', 'cptg');
					$cpt_labels['parent_item_colon']	= $cpt["labels"]["parent_item_colon"] ? $cpt["labels"]["parent_item_colon"] :	__('Parent Page', 'cptg');
			
				/* --- Advanced Options Initialize --- */
				
				$cpt_rewrite_slug = $cpt["rewrite_slug"] ? esc_html($cpt["rewrite_slug"]) : esc_html($cpt["post_type"]);
				$cpt_menu_position = $cpt["menu_position"] ? intval($cpt["menu_position"]) : null;
				$cpt_supports = $cpt["supports"] ? $cpt["supports"] : array(null);
				
				if ( isset ( $cpt["show_in_menu"] ) ) {
					$cpt_show_in_menu = ( $cpt["show_in_menu"] == 1 ) ? true : false;
					$cpt_show_in_menu = ( $cpt["show_in_menu_string"] ) ? $cpt["show_in_menu_string"] : $cpt_show_in_menu;
				} else {
					$cpt_show_in_menu = true;
				}
				
				/* --- register_post_type() --- */
				
				register_post_type( $cpt["post_type"],
					array (
						'label'					=> $cpt_label,
						'labels'				=> $cpt_labels,
						'description'			=> esc_html($cpt["description"]),
						'public'				=> get_disp_cptg_boolean($cpt["public"]),
						'publicly_queryable'	=> get_disp_cptg_boolean($cpt["publicly_queryable"]),
						'exclude_from_search'	=> get_disp_cptg_boolean($cpt["exclude_from_search"]),
						'show_ui'				=> get_disp_cptg_boolean($cpt["show_ui"]),
						'show_in_nav_menus'		=> get_disp_cptg_boolean( $cpt["show_in_nav_menus"] ),
						'show_in_menu'			=> $cpt_show_in_menu,
						'capability_type'		=> $cpt["capability_type"],
						'has_archive'			=> get_disp_cptg_boolean($cpt["has_archive"]),
						'hierarchical'			=> get_disp_cptg_boolean($cpt["hierarchical"]),
						'rewrite'				=> array('slug' => $cpt_rewrite_slug),
						'query_var'				=> get_disp_cptg_boolean($cpt["query_var"]),
						'can_export'				=> get_disp_cptg_boolean($cpt["can_export"]),
						'menu_position'			=> $cpt_menu_position,
						'menu_icon'				=> $cpt["menu_icon"],
						'supports'				=> $cpt_supports,
					)
				);
			}
		}
	}
	
	/************************************************************************************************
				
		Genarate Custom Taxonomy
				
	************************************************************************************************/
	
	function generate_taxs() {
		
		// get_option custom taxonomies
		$cptg_taxs = get_option('cptg_taxs');
		
		if ( is_array( $cptg_taxs ) ) {
			foreach ($cptg_taxs as $tax) {
				
				/* --- Label Options Initialize --- */
				
				$set_label = $tax["label"] ? esc_html($tax["label"]) : esc_html($tax["taxonomy"]);
					
					$set_labels['singular_name']				= $tax["labels"]["singular_label"] ? $tax["labels"]["singular_label"] :							$set_label;
					$set_labels['search_items']					= $tax["labels"]["search_items"] ? $tax["labels"]["search_items"] :								__('Search Tags', 'cptg');
					$set_labels['popular_items']				= $tax["labels"]["popular_items"] ? $tax["labels"]["popular_items"] :							__('Popular Tags', 'cptg');
					$set_labels['all_items']					= $tax["labels"]["all_items"] ? $tax["labels"]["all_items"] :									__('All Tags', 'cptg');
					$set_labels['parent_item']					= $tax["labels"]["parent_item"] ? $tax["labels"]["parent_item"] :								__('Parent Category', 'cptg');
					$set_labels['parent_item_colon'] 			= $tax["labels"]["parent_item_colon"] ? $tax["labels"]["parent_item_colon"] :					__('Parent Category', 'cptg');
					$set_labels['edit_item']					= $tax["labels"]["edit_item"] ? $tax["labels"]["edit_item"] :									__('Edit Tag', 'cptg');
					$set_labels['update_item']					= $tax["labels"]["update_item"] ? $tax["labels"]["update_item"] :								__('Update Tag', 'cptg');
					$set_labels['add_new_item']					= $tax["labels"]["add_new_item"] ? $tax["labels"]["add_new_item"] :								__('Add New Tag', 'cptg');
					$set_labels['new_item_name']				= $tax["labels"]["new_item_name"] ? $tax["labels"]["new_item_name"] :							__('New Tag Name', 'cptg');
					$set_labels['separate_items_with_commas']	= $tax["labels"]["separate_items_with_commas"] ? $tax["labels"]["separate_items_with_commas"] :	__('Separate tags with commas', 'cptg');
					$set_labels['add_or_remove_items']			= $tax["labels"]["add_or_remove_items"] ? $tax["labels"]["add_or_remove_items"] :				__('Add or remove tags', 'cptg');
					$set_labels['choose_from_most_used']		= $tax["labels"]["choose_from_most_used"] ? $tax["labels"]["choose_from_most_used"] :			__('Choose from the most used tags', 'cptg');

				/* --- Advanced Options Initialize --- */
				
				$set_rewrite_slug = $tax["rewrite_slug"] ? esc_html($tax["rewrite_slug"]) : esc_html($tax["taxonomy"]);
				$set_post_types = $tax["post_types"];
				
				/* --- register_taxonomy() --- */
				
				register_taxonomy( $tax["taxonomy"], $set_post_types,
					array (
						'label'				=> $set_label,
						'labels'			=> $set_labels,
						'show_ui'			=> get_disp_cptg_boolean($tax["show_ui"]),
						'hierarchical'		=> get_disp_cptg_boolean($tax["hierarchical"]),
						'rewrite'			=> array('slug' => $set_rewrite_slug),
						'query_var'			=> get_disp_cptg_boolean($tax["query_var"]),
					)
				);
			}
		}
	}
	
	
	
	/************************************************************************************************
	
		Actions
	
	************************************************************************************************/
	
	
	function cptg_actions()
	{
		
		/* --- Delete cpt --- */
		
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'del_cpt' ) {
		
			check_admin_referer( 'nonce_del_cpt' );
		
			$del_num = intval( $_GET['num'] );
			$cptg_cpts = get_option( 'cptg_cpts' );
			unset( $cptg_cpts[$del_num] );
			$cptg_cpts = array_values( $cptg_cpts );
			update_option( 'cptg_cpts', $cptg_cpts );
			wp_redirect( 'admin.php?page=manage_cpt&msg=del' );
		}
		
		/* --- Delete tax --- */
		
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'del_tax' ) {
	
			check_admin_referer( 'nonce_del_tax' );
		
			$del_num = intval( $_GET['num'] );
			$cpt_taxonomies = get_option( 'cptg_taxs' );
			unset( $cpt_taxonomies[$del_num] );
			$cpt_taxonomies = array_values( $cpt_taxonomies );
			update_option( 'cptg_taxs', $cpt_taxonomies );
			wp_redirect( 'admin.php?page=manage_tax&msg=del' );
		}
		
		/* --- Edit cpt --- */
		
		if ( isset( $_POST['edit_cpt_num'] ) ) {
		
			check_admin_referer( 'nonce_regist_cpt' );
	
			$edit_num = intval( $_POST['edit_cpt_num'] );
	
			// get input values
			$input_cpt = $_POST['input_cpt'];
			
			// labels to array
			$input_cpt += array( 'labels' => $_POST['cpt_labels'] );
			
			// supports to array
			$cpt_supports = ( isset( $_POST['cpt_supports'] ) ) ? $_POST['cpt_supports'] : null;
			$input_cpt += array( 'supports' => $cpt_supports );
			
			//Update cptg_cpts options
			
			$cptg_ctps = get_option( 'cptg_cpts' );
	
			if ( is_array( $cptg_ctps ) ) {
				
				unset( $cptg_ctps[$edit_num] );
				
				array_push( $cptg_ctps, $input_cpt);
				
				//$cptg_ctps_new = array_replace( $cptg_ctps, array( $edit_num => $input_cpt) );
				
				$cptg_ctps = array_values( $cptg_ctps );
				
				update_option( 'cptg_cpts', $cptg_ctps );
				wp_redirect( 'admin.php?page=manage_cpt&msg=edit' );
			}
		
		/* --- Add cpt --- */
		
		} else if ( isset( $_POST['cpt_submit'] ) ) {
		
			check_admin_referer( 'nonce_regist_cpt' );
	
			// get input values
			$input_cpt = $_POST['input_cpt'];
			
			// labels to array
			$input_cpt += array( 'labels' => $_POST['cpt_labels'] );
			
			// supports to array
			$cpt_supports = ( isset( $_POST['cpt_supports'] ) ) ? $_POST['cpt_supports'] : null;
			$input_cpt += array( 'supports' => $cpt_supports );
			
			// Update cptg_cpts options
			
			$cptg_options = get_option( 'cptg_cpts' );
			
			if ( !is_array( $cptg_options ) ) {
				$cptg_options = array();
			}
			
			array_push( $cptg_options,  $input_cpt );
			update_option( 'cptg_cpts', $cptg_options );
	
			wp_redirect( 'admin.php?page=manage_cpt&msg=add' );
		}
		
		/* --- Edit tax --- */
		
		if ( isset( $_POST['edit_tax_num'] ) ) {
		
			check_admin_referer( 'nonce_regist_tax' );
	
			//custom taxonomy to edit
			$edit_num = intval( $_POST['edit_tax_num'] );
	
			// get input values
			$input_tax = $_POST['input_tax'];
			
			// labels to array
			$input_tax += array( 'labels' => $_POST['tax_labels'] );
			
			// attached post type to array
			$input_tax += array( 'post_types' => $_POST['tax_post_types'] );
			
			// Update cptg_taxs options
				
			$cptg_options = get_option( 'cptg_taxs' );
			
			if ( is_array( $cptg_options ) ) {
				
				unset( $cptg_options[$edit_num] );
				
				array_push( $cptg_options, $input_tax);
				
				//$cptg_options = array_replace( $cptg_options, array( $edit_num => $input_tax) );
				
				$cptg_options = array_values( $cptg_options );
				
				update_option( 'cptg_taxs', $cptg_options );
				wp_redirect( 'admin.php?page=manage_tax&msg=edit' );
			}
		
		/* --- Add tax --- */
		
		} else if ( isset( $_POST['tax_submit'] ) ) {
			
			check_admin_referer( 'nonce_regist_tax' );
	
			// get input values
			$input_tax = $_POST['input_tax'];
			
			// labels to array
			$input_tax += array( 'labels' => $_POST['cpt_tax_labels'] );
			
			// attached post type to array
			$input_tax += array( 'post_types' => $_POST['tax_post_types'] );
			
			// Update cptg_taxs options
			
			$cptg_options = get_option( 'cptg_taxs' );
	
			if ( !is_array( $cptg_options ) ) {
				$cptg_options = array();
			}
	
			array_push( $cptg_options, $input_tax );
			update_option( 'cptg_taxs', $cptg_options );
	
			wp_redirect( 'admin.php?page=manage_tax&msg=add' );
		}
	}
}


/************************************************************************************************
				
	Method
				
************************************************************************************************/
	
function get_disp_cptg_boolean( $booText )
{
	return $booText ? true : false;
}

function disp_cptg_boolean( $booText )
{
	return $booText ? 'true' : 'false';
}

function echo_boolean_options($obj, $default)
{
	if (isset($obj)) {
		if ($obj == 0) {
			echo '<option value="0" selected="selected">false</option>';
			echo '<option value="1">true</option>';
		} else if($obj == 1) {
			echo '<option value="0">false</option>';
			echo '<option value="1" selected="selected">true</option>';
		}
	} else {
		if ($default == 0) {
			echo '<option value="0" selected="selected">false</option>';
			echo '<option value="1">true</option>';
		} else if($default == 1) {
			echo '<option value="0">false</option>';
			echo '<option value="1" selected="selected">true</option>';
		}
	}
}


/************************************************************************************************
				
	Note: WP Options Structure
				
************************************************************************************************/

/*

	[cptg_cpts]
	
	Array (
		[0] => Array (
			[post_type] => 
			[label] => 
			[menu_position] => 
			...
			
			[labels] => Array (
				[singular_label] => 
				[menu_name] =>
				...
			)
			
			[supports] => Array (
				[0] => title
				[1] => editor
				...
			)
		)
	)
	
	[cptg_taxs]
	
	Array (
		[0] => Array (
			[taxonomy] => 
			[label] => 
			[hierarchical] =>
			...
			
			[labels] => Array (
				[singular_label] =>
				[search_items] =>
				...
			)
			
			[post_types] => Array (
				[0] => post
				[1] => page
				...
			)
		)
	)

*/
	
?>