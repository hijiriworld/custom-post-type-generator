<?php 

if ( isset($_GET['action']) && $_GET['action'] == 'edit_cpt' ) {
	check_admin_referer('nonce_regist_cpt');
	
	// get edit key
	$key = $_GET['key'];
	$cpt = get_option( $key );
	
	$page_title = __('Edit Custom Post Type', 'cptg');
	$submit_title = __('Update', 'cptg');
} else {
	$page_title = __('Add New Custom Post Type', 'cptg');
	$submit_title = __('Add New', 'cptg');
}

?>

<div class="wrap">

<!-- error -->
<div class="error error-cptg" id="error1" style="display: none;">
	<p><?php _e('Post Type is required.', 'cptg'); ?></p>
</div>

<?php screen_icon( 'plugins' ); ?>

<h2><?php echo $page_title; ?></h2>

<form id="cptg_cpt_form" method="post">

	<!-- wp_nonce_field -->
	<?php if ( function_exists( 'wp_nonce_field' ) ) wp_nonce_field( 'nonce_regist_cpt' ); ?>
	
	<!-- edit flg -->
	<?php if ( isset($_GET['action']) && $_GET['action'] == 'edit_cpt' ) : ?>
		<input type="hidden" name="key" value="<?php echo $key; ?>">
	<?php endif; ?>
	
	<div class="metabox-holder cptg-metabox">
		
		<div class="meta-box-sortables">
				
			<div class="postbox">
				<div class="handlediv" title="<?php _e('Click to toggle') ?>"></div>
				<h3 class="hndle"><span><?php _e('Basic', 'cptg'); ?></span></h3>
				<div class="inside">
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<?php _e('Post Type', 'cptg') ?> <span style="color:red;">*</span>
								<br><?php _e('($post_type)', 'cptg') ?>
							</th>
							<td>
								<input type="text" id="post_type_name" name="input_cpt[post_type]" value="<?php if (isset($cpt['post_type'])) { echo esc_attr($cpt['post_type']); } ?>" maxlength="20" onblur="this.value=this.value.toLowerCase()">
								<p><?php _e('max. 20 characters, can not contain capital letters or spaces', 'cptg'); ?></p>	
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<?php _e('Label', 'cptg') ?>
								<br><?php _e('($name)', 'cptg') ?>
							</th>
							<td>
								<input type="text" name="input_cpt[labels][name]" value="<?php if (isset($cpt['labels']['name'])) { echo esc_attr($cpt['labels']['name']); } ?>">
								<?php _e('(Default: $post_type)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Supports', 'cptg') ?></th>
							<td>
								<label><input type="checkbox" name="input_cpt[supports][]" value="title" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('title', $cpt['supports'])) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?>> <?php _e('title', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="editor" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('editor', $cpt['supports'])) { echo 'checked="checked"'; } } elseif (!isset($_GET['edittype'])) { echo 'checked="checked"'; } ?>> <?php _e('editor', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="author" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('author', $cpt['supports'])) { echo 'checked="checked"'; } } ?>> <?php _e('author', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="thumbnail" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('thumbnail', $cpt['supports'])) { echo 'checked="checked"'; } } ?>> <?php _e('thumbnail', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="excerpt" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('excerpt', $cpt['supports'])) { echo 'checked="checked"'; } } ?>> <?php _e('excerpt', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="trackbacks" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('trackbacks', $cpt['supports'])) { echo 'checked="checked"'; } } ?>> <?php _e('trackbacks', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="custom-fields" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('custom-fields', $cpt['supports'])) { echo 'checked="checked"'; } } ?>> <?php _e('custom-fields', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="comments" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('comments', $cpt['supports'])) { echo 'checked="checked"'; } } ?>> <?php _e('comments', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="revisions" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('revisions', $cpt['supports'])) { echo 'checked="checked"'; } } ?>> <?php _e('revisions', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="page-attributes" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('page-attributes', $cpt['supports'])) { echo 'checked="checked"'; } } ?>> <?php _e('page-attributes', 'cptg'); ?></label><br>
								<label><input type="checkbox" name="input_cpt[supports][]" value="post-formats" <?php if (isset($cpt['supports']) && is_array($cpt['supports'])) { if (in_array('post-formats', $cpt['supports'])) { echo 'checked="checked"'; } } ?>> <?php _e('post-formats', 'cptg'); ?></label><br>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Has Archive', 'cptg') ?></th>
							<td>
								<p><label><input type="checkbox" id="input_cpt_has_archive_check"><?php _e( 'Update related configurations below as well.', 'cptg' ) ?></label></p>
								<p><select name="input_cpt[has_archive]" id="input_cpt_has_archive">
									<?php echo_boolean_options($cpt['has_archive'], 0); ?>
								</select> <?php _e('(Default: false)'); ?></p>
							</td>
						</tr>
					</table>
				</div>
			</div>
		
			<div class="postbox">
				<div class="handlediv" title="<?php _e('Click to toggle') ?>"></div>
				<h3 class="hndle"><span><?php _e('Label Options', 'cptg'); ?></span></h3>
				<div class="inside">
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('singular_name', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][singular_name]" value="<?php if (isset($cpt['labels']['singular_name'])) { echo esc_attr($cpt['labels']['singular_name']); } ?>">
								<?php _e('(Default: $name)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('menu_name', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][menu_name]" value="<?php if (isset($cpt['labels']['menu_name'])) { echo esc_attr($cpt['labels']['menu_name']); } ?>">
								<?php _e('(Default: $name)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('name_admin_bar', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][name_admin_bar]" value="<?php if (isset($cpt['labels']['name_admin_bar'])) { echo esc_attr($cpt['labels']['name_admin_bar']); } ?>">
								<?php _e('(Default: $singular_name)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('all_items', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][all_items]" value="<?php if (isset($cpt['labels']['all_items'])) { echo esc_attr($cpt['labels']['all_items']); } ?>">
								(Default: <?php _e('All Posts') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('add_new', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][add_new]" value="<?php if (isset($cpt['labels']['add_new'])) { echo esc_attr($cpt['labels']['add_new']); } ?>">
								<?php _e('(Default: Add New)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('add_new_item', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][add_new_item]" value="<?php if (isset($cpt['labels']['add_new_item'])) { echo esc_attr($cpt['labels']['add_new_item']); } ?>">
								(Default: <?php _e('Add New Post') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('edit_item', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][edit_item]" value="<?php if (isset($cpt['labels']['edit_item'])) { echo esc_attr($cpt['labels']['edit_item']); } ?>">
								(Default: <?php _e('Edit Post') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('new_item', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][new_item]" value="<?php if (isset($cpt['labels']['new_item'])) { echo esc_attr($cpt['labels']['new_item']); } ?>">
								(Default: <?php _e('New Post') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('view_item', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][view_item]" value="<?php if (isset($cpt['labels']['view_item'])) { echo esc_attr($cpt['labels']['view_item']); } ?>">
								(Default: <?php _e('View Post') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('search_items', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][search_items]" value="<?php if (isset($cpt['labels']['search_items'])) { echo esc_attr($cpt['labels']['search_items']); } ?>">
								(Default: <?php _e('Search Posts') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('not_found', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][not_found]" value="<?php if (isset($cpt['labels']['not_found'])) { echo esc_attr($cpt['labels']['not_found']); } ?>">
								(Default: <?php _e('No posts found.') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('not_found_in_trash', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][not_found_in_trash]" value="<?php if (isset($cpt['labels']['not_found_in_trash'])) { echo esc_attr($cpt['labels']['not_found_in_trash']); } ?>">
								(Default: <?php _e('No posts found in Trash.') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('parent_item_colon', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[labels][parent_item_colon]" value="<?php if (isset($cpt['labels']['parent_item_colon'])) { echo esc_attr($cpt['labels']['parent_item_colon']); } ?>">
								(Default: <?php _e('Parent Page', 'cptg') ?>)
							</td>
						</tr>
						
					</table>
				</div>
			</div>
			
			<div class="postbox">
				<div class="handlediv" title="<?php _e('Click to toggle') ?>"></div>
				<h3 class="hndle"><span><?php _e('Advanced Options', 'cptg'); ?></span></h3>
				<div class="inside">
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('description', 'cptg') ?></th>
							<td>
								<textarea name="input_cpt[description]"><?php if (isset($cpt['description'])) { echo esc_attr($cpt['description']); } ?></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('public', 'cptg') ?></th>
							<td>
								<p><label><input type="checkbox" id="input_cpt_public_check"><?php _e( 'Update related configurations below as well.', 'cptg' ) ?></label></p>
								<p><select name="input_cpt[public]" id="input_cpt_public">
									<?php echo_boolean_options($cpt['public'], 0); ?>
								</select> <?php _e('(Default: false)', 'cptg') ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('exclude_from_search', 'cptg') ?></th>
							<td>
								<select name="input_cpt[exclude_from_search]" id="input_cpt_exclude_from_search">
									<?php echo_boolean_options($cpt['exclude_from_search'], 1); ?>
								</select> <?php _e('(Default: true - opposite of $public)', 'cptg') ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('publicly_queryable', 'cptg') ?></th>
							<td>
								<select name="input_cpt[publicly_queryable]" id="input_cpt_publicly_queryable">
									<?php echo_boolean_options($cpt['publicly_queryable'], 0); ?>
								</select> <?php _e('(Default: false - $public)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('show_ui', 'cptg') ?></th>
							<td>
								<select name="input_cpt[show_ui]" id="input_cpt_show_ui">
									<?php echo_boolean_options($cpt['show_ui'], 0); ?>
								</select> <?php _e('(Default: false - $public)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('show_in_nav_menus', 'cptg') ?></th>
							<td>
								<select name="input_cpt[show_in_nav_menus]" id="input_cpt_show_in_nav_menus">
									<?php echo_boolean_options($cpt['show_in_nav_menus'], 0); ?>
								</select> <?php _e('(Default: false - $public)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('show_in_menu', 'cptg') ?></th>
							<td>
								<select name="input_cpt[show_in_menu][show_in_menu]" id="input_cpt_show_in_menu">
									<?php echo_boolean_options($cpt['show_in_menu']['show_in_menu'], 0); ?>
								</select> <?php _e('(Default: false - $show_ui)', 'cptg') ?> / 
								<?php _e('string', 'cptg') ?>
								<input type="text" name="input_cpt[show_in_menu][string]" value="<?php if (isset($cpt['show_in_menu']['string'])) { echo esc_attr($cpt['show_in_menu']['string']); } ?>">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('show_in_admin_bar', 'cptg') ?></th>
							<td>
								<select name="input_cpt[show_in_admin_bar]" id="input_cpt_show_in_admin_bar">
									<?php echo_boolean_options($cpt['show_in_admin_bar'], 0); ?>
								</select> <?php _e('(Default: false - $show_ui_menu)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('menu_position', 'cptg') ?></th>
							<td>
								<input class='selector' type="text" name="input_cpt[menu_position]" size="5" value="<?php if (isset($cpt['menu_position'])) { echo esc_attr($cpt['menu_position']); } ?>"> <?php _e('(Default: null - defaults to below Comments)', 'cptg'); ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('menu_icon', 'cptg') ?></th>
							<td>
								<input type="text" name="input_cpt[menu_icon]" value="<?php if (isset($cpt['menu_position'])) { echo esc_attr($cpt['menu_icon']); } ?>"> <?php _e('(Default: null - defaults to the posts icon)', 'cptg') ?>
								<a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">[1]</a>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('hierarchical', 'cptg') ?></th>
							<td>
								<select name="input_cpt[hierarchical]">
									<?php echo_boolean_options($cpt['hierarchical'], 0); ?>
								</select> <?php _e('(Default: false)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('rewrite', 'cptg') ?></th>
							<td>
								<select name="input_cpt[rewrite][rewrite]">
									<?php echo_boolean_options($cpt['rewrite']['rewrite'], 1); ?>
								</select> <?php _e('(Default: true)', 'cptg') ?>
								<ul>
									<li>
										<?php _e('slug', 'cptg') ?>
										<input type="text" name="input_cpt[rewrite][slug]" value="<?php if (isset($cpt['rewrite']['slug'])) { echo esc_attr($cpt['rewrite']['slug']); } ?>"> <?php _e('(Default: $post_type)', 'cptg') ?>
									</li>
									<li>
										<?php _e('with_front', 'cptg') ?>
										<select name="input_cpt[rewrite][with_front]">
											<?php echo_boolean_options($cpt['rewrite']['with_front'], 1); ?>
										</select> <?php _e('(Default: true)', 'cptg') ?>
									</li>
									<li>
										<?php _e('feeds', 'cptg') ?>
										<select name="input_cpt[rewrite][feeds]" id="input_cpt_rewrite_feeds">
											<?php echo_boolean_options($cpt['rewrite']['feeds'], 0); ?>
										</select> <?php _e('(Default: false - $has_archive)', 'cptg') ?>
									</li>
									<li>
										<?php _e('pages', 'cptg') ?>
										<select name="input_cpt[rewrite][pages]">
											<?php echo_boolean_options($cpt['rewrite']['pages'], 1); ?>
										</select> <?php _e('(Default: true)', 'cptg') ?>
									</li>
								</ul>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('query_var', 'cptg') ?></th>
							<td>
								<select name="input_cpt[query_var][query_var]">
									<?php echo_boolean_options($cpt['query_var']['query_var'], 1); ?>
								</select> <?php _e('(Default: true)', 'cptg') ?> / 
								<?php _e('string', 'cptg') ?>
								<input type="text" name="input_cpt[query_var][string]" value="<?php if (isset($cpt['query_var']['string'])) { echo esc_attr($cpt['query_var']['string']); } ?>">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('can_export', 'cptg') ?></th>
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
	
	</div>

	<p class="submit">
		<input type="submit" class="button-primary" name="cpt_submit" value="<?php echo $submit_title; ?>">
	</p>

</form>
