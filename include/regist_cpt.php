<?php 

if ( isset($_GET['action']) && $_GET['action'] == 'edit_cpt' ) {
	
	check_admin_referer('nonce_regist_cpt');
	
	// get edit key
	$key = $_GET['key'];
	$cpt = get_option( $key );
	
	// load cpt to edit
	$cpt_post_type = $cpt["post_type"];
	$cpt_label					= $cpt["label"];
		
	// labels
	$cpt_labels					= $cpt["labels"]; // Array
		
	$cpt_description			= $cpt["description"];
	$cpt_public					= $cpt["public"];
	$cpt_publicly_queryable		= $cpt["publicly_queryable"];
	$cpt_exclude_from_search	= isset( $cpt["exclude_from_search"] ) ? $cpt["exclude_from_search"] : null;
	$cpt_showui					= $cpt["show_ui"];
	$cpt_show_in_nav_menus		= isset( $cpt["show_in_nav_menus"] ) ? $cpt["show_in_nav_menus"] : null;
	$cpt_menu_position			= $cpt["menu_position"];
	$cpt_menu_icon				= $cpt["menu_icon"];
	$cpt_capability				= $cpt["capability_type"];
	$cpt_hierarchical			= $cpt["hierarchical"];
	$cpt_supports				= $cpt["supports"];	// Array
	$cpt_has_archive			= isset( $cpt["has_archive"] ) ? $cpt["has_archive"] : null;
	$cpt_rewrite				= $cpt["rewrite"];
		$cpt_rewrite_slug			= $cpt["rewrite_slug"];
	$cpt_query_var				= $cpt["query_var"];
	$cpt_can_export				= $cpt["can_export"];
		
		
	$page_title = __('Edit Custom Post Type', 'cptg');
	$submit_title = __('Update', 'cptg');
	
} else {

	$page_title = __('Add New Custom Post Type', 'cptg');
	$submit_title = __('Add New', 'cptg');
}


// flush rewrite rules
flush_rewrite_rules();

?>




<div class="wrap">

<!-- error -->
<div class="error error-cptg" id="error" style="display: none;">
	<p><?php _e('Post Type is required.', 'cptg'); ?></p>
</div>

<?php screen_icon( 'plugins' ); ?>

<h2><?php echo $page_title; ?></h2>

<form id="cptg_cpt_form" method="post">

	<!-- wp_nonce_field -->
	<?php if ( function_exists( 'wp_nonce_field' ) ) wp_nonce_field( 'nonce_regist_cpt' ); ?>
	
	<!-- edit flg -->
	<?php if ( isset($_GET['action']) && $_GET['action'] == 'edit_cpt' ) : ?>
		<input type="hidden" name="key" value="<?php echo $key; ?>" />
	<?php endif; ?>
	
	<div class="metabox-holder cptg-metabox">
		<div class="postbox">
			<h3 class="hndle"><span><?php _e('Basic', 'cptg'); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<strong><?php _e('Post Type', 'cptg') ?></strong> <span style="color:red;">*</span>
							<br /><?php _e('($post_type)', 'cptg') ?>
						</th>
						<td>
							<input type="text" id="post_type_name" name="input_cpt[post_type]" value="<?php if (isset($cpt_post_type)) { echo esc_attr($cpt_post_type); } ?>" maxlength="20" onblur="this.value=this.value.toLowerCase()" />
							<p><?php _e('max. 20 characters, can not contain capital letters or spaces', 'cptg'); ?></p>	
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Label', 'cptg') ?></th>
						<td>
							<input type="text" name="input_cpt[label]" value="<?php if (isset($cpt_label)) { echo esc_attr($cpt_label); } ?>" />
							<?php _e('(Default: $post_type)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Menu Position', 'cptg') ?></th>
						<td>
							<input class='selector' type="text" name="input_cpt[menu_position]" size="5" value="<?php if (isset($cpt_menu_position)) { echo esc_attr($cpt_menu_position); } ?>" /> <?php _e('(Default: null - defaults to below Comments)', 'cptg'); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Supports', 'cptg') ?></th>
						<td>
							<label><input type="checkbox" name="cpt_supports[]" value="title" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('title', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('title', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="editor" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('editor', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('editor', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="author" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('author', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('author', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="thumbnail" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('thumbnail', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('thumbnail', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="excerpt" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('excerpt', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('excerpt', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="trackbacks" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('trackbacks', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('trackbacks', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="custom-fields" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('custom-fields', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('custom-fields', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="comments" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('comments', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('comments', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="revisions" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('revisions', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('revisions', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="page-attributes" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('page-attributes', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('page-attributes', 'cptg'); ?></label><br />
							<label><input type="checkbox" name="cpt_supports[]" value="post-formats" <?php if (isset($cpt_supports) && is_array($cpt_supports)) { if (in_array('post-formats', $cpt_supports)) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?> /> <?php _e('post-formats', 'cptg'); ?></label><br />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Has Archive', 'cptg') ?></th>
						<td>
							<select name="input_cpt[has_archive]">
								<?php echo_boolean_options($cpt_has_archive, 0); ?>
							</select> <?php _e('(Default: false)'); ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	
		<div class="postbox">
			<h3 class="hndle"><span><?php _e('Label Options', 'cptg'); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('singular name', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[singular_label]" value="<?php if (isset($cpt_labels["singular_label"])) { echo esc_attr($cpt_labels["singular_label"]); } ?>" />
							<?php _e('(Default: $post_type)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('menu name', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[menu_name]" value="<?php if (isset($cpt_labels["menu_name"])) { echo esc_attr($cpt_labels["menu_name"]); } ?>" />
							<?php _e('(Default: $post_type)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('all items', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[all_items]" value="<?php if (isset($cpt_labels["all_items"])) { echo esc_attr($cpt_labels["all_items"]); } ?>" />
							<?php _e('(Default: All Posts)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('add new', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[add_new]" value="<?php if (isset($cpt_labels["add_new"])) { echo esc_attr($cpt_labels["add_new"]); } ?>" />
							<?php _e('(Default: Add New)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('add new item', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[add_new_item]" value="<?php if (isset($cpt_labels["add_new_item"])) { echo esc_attr($cpt_labels["add_new_item"]); } ?>" />
							<?php _e('(Default: Add New Post)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('edit item', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[edit_item]" value="<?php if (isset($cpt_labels["edit_item"])) { echo esc_attr($cpt_labels["edit_item"]); } ?>" />
							<?php _e('(Default: Edit Post)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('new item', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[new_item]" value="<?php if (isset($cpt_labels["new_item"])) { echo esc_attr($cpt_labels["new_item"]); } ?>" />
							<?php _e('(Default: New Post)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('view item', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[view_item]" value="<?php if (isset($cpt_labels["view_item"])) { echo esc_attr($cpt_labels["view_item"]); } ?>" />
							<?php _e('(Default: View Post)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('search items', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[search_items]" value="<?php if (isset($cpt_labels["search_items"])) { echo esc_attr($cpt_labels["search_items"]); } ?>" />
							<?php _e('(Default: Search Posts)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('not found', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[not_found]" value="<?php if (isset($cpt_labels["not_found"])) { echo esc_attr($cpt_labels["not_found"]); } ?>" />
							<?php _e('(Default: No posts found.)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('not found in trash', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[not_found_in_trash]" value="<?php if (isset($cpt_labels["not_found_in_trash"])) { echo esc_attr($cpt_labels["not_found_in_trash"]); } ?>" />
							<?php _e('(Default: No posts found in Trash.)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('parent item colon', 'cptg') ?></th>
						<td>
							<input type="text" name="cpt_labels[parent_item_colon]" value="<?php if (isset($cpt_labels["parent_item_colon"])) { echo esc_attr($cpt_labels["parent_item_colon"]); } ?>" />
							<?php _e('(Default: Parent Page)', 'cptg') ?>
						</td>
					</tr>
					
				</table>
			</div>
		</div>
		
		<div class="postbox">
			<h3 class="hndle"><span><?php _e('Advanced Options', 'cptg'); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Description', 'cptg') ?></th>
						<td>
							<textarea name="input_cpt[description]" rows="4" cols="40"><?php if (isset($cpt_description)) { echo esc_attr($cpt_description); } ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Public', 'cptg') ?></th>
						<td>
							<select name="input_cpt[public]">
								<?php echo_boolean_options($cpt_public, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Publicly Queryable', 'cptg') ?></th>
						<td>
							<select name="input_cpt[publicly_queryable]">
								<?php echo_boolean_options($cpt_publicly_queryable, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Exclude From Search', 'cptg') ?></th>
						<td>
							<select name="input_cpt[exclude_from_search]">
								<?php echo_boolean_options($cpt_exclude_from_search, 0); ?>
							</select> <?php _e('(Default: false)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Show UI', 'cptg') ?></th>
						<td>
							<select name="input_cpt[show_ui]">
								<?php echo_boolean_options($cpt_showui, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Show in Nav Menus', 'cptg') ?></th>
						<td>
							<select name="input_cpt[show_in_nav_menus]">
								<?php echo_boolean_options($cpt_show_in_nav_menus, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Menu Icon', 'cptg') ?></th>
						<td>
							<input type="text" name="input_cpt[menu_icon]" value="<?php if (isset($cpt_menu_position)) { echo esc_attr($cpt_menu_icon); } ?>" /> <?php _e('(Default: null - defaults to the posts icon)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Capability Type', 'cptg') ?></th>
						<td><input type="text" name="input_cpt[capability_type]" value="post" value="<?php if ( isset( $cpt_capability ) ) { echo esc_attr( $cpt_capability ); } ?>" /> <?php _e('(Default: post)', 'cptg') ?></td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Hierarchical', 'cptg') ?></th>
						<td>
							<select name="input_cpt[hierarchical]">
								<?php echo_boolean_options($cpt_hierarchical, 0); ?>
							</select> <?php _e('(Default: false)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Rewrite', 'cptg') ?></th>
						<td>
							<select name="input_cpt[rewrite]">
								<?php echo_boolean_options($cpt_rewrite, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
							<p>
								<?php _e('Custom Rewrite Slug', 'cptg') ?>
								<input type="text" name="input_cpt[rewrite_slug]" value="<?php if (isset($cpt_rewrite_slug)) { echo esc_attr($cpt_rewrite_slug); } ?>" /> <?php _e('(Default: $post_type)', 'cptg') ?>
							</p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Query Var', 'cptg') ?></th>
						<td>
							<select name="input_cpt[query_var]">
								<?php echo_boolean_options($cpt_query_var, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Can Export', 'cptg') ?></th>
						<td>
							<select name="input_cpt[can_export]">
								<?php echo_boolean_options($cpt_can_export, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
						</td>
					</tr>
				</table>
			</div>
		</div>

	</div>
	
	<p class="submit">
		<input type="submit" class="button-primary" name="cpt_submit" value="<?php echo $submit_title; ?>" />
	</p>

</div>

</form>
