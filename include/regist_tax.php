<?php

if ( isset($_GET['action']) && $_GET['action'] == 'edit_tax' ) {
	check_admin_referer('nonce_regist_tax');

	// get edit key
	$key = $_GET['key'];
	$tax = get_option( $key );
	
	$page_title = __('Edit Custom Taxonomy', 'cptg');
	$submit_title = __('Update', 'cptg');
} else {
	$page_title = __('Add New Custom Taxonomy', 'cptg');
	$submit_title = __('Add New', 'cptg');
}

?>

<div class="wrap">

<!-- error -->
<div class="error error-cptg" id="error1" style="display: none;">
	<p><?php _e('Taxonomy is required.', 'cptg'); ?></p>
</div>

<div class="error error-cptg" id="error2" style="display: none;">
	<p><?php _e('You must allocate at least 1 of Custom Post Type to Custom Taxonomy.', 'cptg'); ?></p>
</div>

<?php screen_icon( 'plugins' ); ?>

<h2><?php echo $page_title; ?></h2>
	
<form id="cptg_tax_form" method="post">

	<!-- wp_nonce_field -->
	<?php if ( function_exists( 'wp_nonce_field' ) ) wp_nonce_field( 'nonce_regist_tax' ); ?>
	
	<!-- edit flg -->
	<?php if ( isset($_GET['action']) && $_GET['action'] == 'edit_tax' ) : ?>
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
								<?php _e('Taxonomy', 'cptg') ?> <span style="color:red;">*</span>
								<br><?php _e('($taxonomy)', 'cptg') ?>
							</th>
							<td>
								<input type="text" id="tax_name" name="input_tax[taxonomy]" value="<?php if (isset($tax['taxonomy'])) echo esc_attr($tax['taxonomy']); ?>" maxlength="32" onblur="this.value=this.value.toLowerCase()">
								<p><?php _e('max. 32 characters, can not contain capital letters or spaces', 'cptg'); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<?php _e('Label', 'cptg') ?>
								<br><?php _e('($name)', 'cptg') ?>
							</th>
							<td>
								<input type="text" name="input_tax[labels][name]" value="<?php if (isset($tax['labels']['name'])) { echo esc_attr($tax['labels']['name']); } ?>">
								<?php _e('(Default: $taxonomy)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><strong><?php _e('Attached Post Types', 'cptg') ?></strong> <span style="color:red;">*</span></th>
							<td>
								<?php
								$args = array(
									'show_ui' => true,
									'show_in_menu' => true,
								);
								$output = 'objects'; // or objects
								$post_types = get_post_types( $args, $output );
								foreach ($post_types  as $post_type ) {
									if ( $post_type->name != 'attachment' ) {
									?>
									<label><input type="checkbox" name="input_tax[post_types][]" class="input_tax_post_types" value="<?php echo $post_type->name; ?>" <?php if (isset($tax['post_types']) && is_array($tax['post_types'])) { if (in_array($post_type->name, $tax['post_types'])) { echo 'checked="checked"'; } } ?>>&nbsp;<?php echo $post_type->label; ?></label><br>
									<?php
									}
								}
								?>
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
								<input type="text" name="input_tax[labels][singular_name]" value="<?php if (isset($tax['labels']['singular_name'])) { echo esc_attr($tax['labels']['singular_name']); } ?>">
								<?php _e('(Default: $name)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('menu_name', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][menu_name]" value="<?php if (isset($tax['labels']['menu_name'])) { echo esc_attr($tax['labels']['menu_name']); } ?>">
								<?php _e('(Default: $name)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('all_items', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][all_items]" value="<?php if (isset($tax['labels']['all_items'])) { echo esc_attr($tax['labels']['all_items']); } ?>">
								(Default: <?php _e('All Tags') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('edit_item', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][edit_item]" value="<?php if (isset($tax['labels']['edit_item'])) { echo esc_attr($tax['labels']['edit_item']); } ?>">
								(Default: <?php _e('Edit Tag') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('view_item', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][view_item]" value="<?php if (isset($tax['labels']['view_item'])) { echo esc_attr($tax['labels']['view_item']); } ?>">
								(Default: <?php _e('View Tag') ?>)
							</td>
						</tr>						
						<tr valign="top">
							<th scope="row"><?php _e('update_item', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][update_item]" value="<?php if (isset($tax['labels']['update_item'])) { echo esc_attr($tax['labels']['update_item']); } ?>">
								(Default: <?php _e('Update Tag') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('add_new_item', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][add_new_item]" value="<?php if (isset($tax['labels']['add_new_item'])) { echo esc_attr($tax['labels']['add_new_item']); } ?>">
								(Default: <?php _e('Add New Tag') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('new_item_name', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][new_item_name]" value="<?php if (isset($tax['labels']['new_item_name'])) { echo esc_attr($tax['labels']['new_item_name']); } ?>">
								(Default: <?php _e('New Tag Name') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('parent_item', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][parent_item]" value="<?php if (isset($tax['labels']['parent_item'])) { echo esc_attr($tax['labels']['parent_item']); } ?>">
								(Default: <?php _e('Parent Category') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('parent_item_colon', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][parent_item_colon]" value="<?php if (isset($tax['labels']['parent_item_colon'])) { echo esc_attr($tax['labels']['parent_item_colon']); } ?>">
								(Default: <?php _e('Parent Category') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('search_items', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][search_items]" value="<?php if (isset($tax['labels']['search_items'])) { echo esc_attr($tax['labels']['search_items']); } ?>">
								(Default: <?php _e('Search Tags') ?>)
							</td>
						</tr>
				
						<tr valign="top">
							<th scope="row"><?php _e('popular_items', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][popular_items]" value="<?php if (isset($tax['labels']['popular_items'])) { echo esc_attr($tax['labels']['popular_items']); } ?>">
								(Default: <?php _e('Popular Tags') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('separate_items_with_commas', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][separate_items_with_commas]" value="<?php if (isset($tax['labels']['separate_items_with_commas'])) { echo esc_attr($tax['labels']['separate_items_with_commas']); } ?>">
								(Default: <?php _e('Separate tags with commas') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('add_or_remove_items', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][add_or_remove_items]" value="<?php if (isset($tax['labels']['add_or_remove_items'])) { echo esc_attr($tax['labels']['add_or_remove_items']); } ?>">
								(Default: <?php _e('Add or remove tags') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('choose_from_most_used', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][choose_from_most_used]" value="<?php if (isset($tax['labels']['choose_from_most_used'])) { echo esc_attr($tax['labels']['choose_from_most_used']); } ?>">
								(Default: <?php _e('Choose from the most used tags') ?>)
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('not_found', 'cptg') ?></th>
							<td>
								<input type="text" name="input_tax[labels][not_found]" value="<?php if (isset($tax['labels']['not_found'])) { echo esc_attr($tax['labels']['not_found']); } ?>">
								(Default: <?php _e('No tags found.') ?>)
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
							<th scope="row"><?php _e('public', 'cptg') ?></th>
							<td>
								<p><label><input type="checkbox" id="input_tax_public_check"><?php _e( 'Update related configurations below as well.', 'cptg' ) ?></label></p>
								<p><select name="input_tax[public]" id="input_tax_public">
									<?php echo_boolean_options($tax['public'], 1); ?>
								</select> <?php _e('(Default: true)', 'cptg') ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('show_ui', 'cptg') ?></th>
							<td>
								<select name="input_tax[show_ui]" id="input_tax_show_ui">
									<?php echo_boolean_options($tax['show_ui'], 1); ?>
								</select> <?php _e('(Default: true - $public)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('show_in_nav_menus', 'cptg') ?></th>
							<td>
								<select name="input_tax[show_in_nav_menus]" id="input_tax_show_in_nav_menus">
									<?php echo_boolean_options($tax['show_in_nav_menus'], 1); ?>
								</select> <?php _e('(Default: true - $public)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('show_tagcloud', 'cptg') ?></th>
							<td>
								<select name="input_tax[show_tagcloud]" id="input_tax_show_tagcloud">
									<?php echo_boolean_options($tax['show_tagcloud'], 1); ?>
								</select> <?php _e('(Default: true - $show_ui)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('meta_box_cb', 'cptg') ?></th>
							<td>
								<select name="input_tax[meta_box_cb]">
									<?php echo_boolean_null_false_options($tax['meta_box_cb'], 1); ?>
								</select> <?php _e('(Default: null)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('show_admin_column', 'cptg') ?></th>
							<td>
								<select name="input_tax[show_admin_column]">
									<?php echo_boolean_options($tax['show_admin_column'], 0); ?>
								</select> <?php _e('(Default: false)', 'cptg') ?>
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row"><?php _e('hierarchical', 'cptg') ?></th>
							<td>
								<select name="input_tax[hierarchical]">
									<?php echo_boolean_options($tax['hierarchical'], 0); ?>
								</select> <?php _e('(Default: false)', 'cptg') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('query_var') ?></th>
							<td>
								<select name="input_tax[query_var][query_var]">
									<?php echo_boolean_options($tax['query_var']['query_var'], 1); ?>
								</select> <?php _e('(Default: true)', 'cptg') ?> / 
								<?php _e('string', 'cptg') ?>
								<input type="text" name="input_tax[query_var][string]" value="<?php if (isset($tax['query_var']['string'])) { echo esc_attr($tax['query_var']['string']); } ?>">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('rewrite', 'cptg') ?></th>
							<td>
								<select name="input_tax[rewrite][rewrite]">
									<?php echo_boolean_options($tax['rewrite']['rewrite'], 1); ?>
								</select> <?php _e('(Default: true)', 'cptg') ?>
								<ul>
									<li>
										<?php _e('slug', 'cptg') ?>
										<input type="text" name="input_tax[rewrite][slug]" value="<?php if (isset($tax['rewrite']['slug'])) { echo esc_attr($tax['rewrite']['slug']); } ?>"> <?php _e('(Default: $taxonomy)', 'cptg') ?>
									</li>
									<li>
										<?php _e('with_front', 'cptg') ?>
										<select name="input_tax[rewrite][with_front]">
											<?php echo_boolean_options($tax['rewrite']['with_front'], 1); ?>
										</select> <?php _e('(Default: true)', 'cptg') ?>
									</li>
									<li>
										<?php _e('hierarchical', 'cptg') ?>
										<select name="input_tax[rewrite][hierarchical]">
											<?php echo_boolean_options($tax['rewrite']['hierarchical'], 0); ?>
										</select> <?php _e('(Default: false)', 'cptg') ?>
									</li>
								</ul>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('sort') ?></th>
							<td>
								<select name="input_tax[sort]">
									<?php echo_boolean_options($tax['sort'], 0); ?>
								</select> <?php _e('(Default: false)', 'cptg') ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			
		</div>

	</div>

	<p class="submit">
		<input type="submit" class="button-primary" name="tax_submit" value="<?php echo $submit_title; ?>">
	</p>
	
</form>

