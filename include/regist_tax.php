<?php

if ( isset($_GET['action']) && $_GET['action'] == 'edit_tax' ) {
	
	check_admin_referer('nonce_regist_tax');

	// get edit key
	$key = $_GET['key'];
	$tax = get_option( $key );
	
	// load tax to edit
	$tax_taxonomy		= $tax["taxonomy"];
	$tax_label			= $tax["label"];
	$tax_hierarchical	= $tax["hierarchical"];
	$tax_show_ui		= $tax["show_ui"];
	$tax_query_var		= $tax["query_var"];
	$tax_rewrite		= $tax["rewrite"];
	$tax_rewrite_slug	= $tax["rewrite_slug"];
	
	$tax_labels			= $tax["labels"];		// Array
	$tax_post_types		= $tax["post_types"];	// Array
	
	$page_title = __('Edit Custom Taxonomy', 'cptg');
	$submit_title = __('Update', 'cptg');
	
} else {

	$page_title = __('Add New Custom Taxonomy', 'cptg');
	$submit_title = __('Add New', 'cptg');
}

//flush rewrite rules
flush_rewrite_rules();

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
		<input type="hidden" name="key" value="<?php echo $key; ?>" />
	<?php endif; ?>
	
	
	<div class="metabox-holder cptg-metabox">
	
		<div class="postbox">
			<!--<div class="handlediv" title="クリックで切替"></div>-->
			<h3 class="hndle"><span><?php _e('Basic', 'cptg'); ?></span></h3>
			<div class="inside">
				<table class="form-table">
				
					<tr valign="top">
						<th scope="row">
							<strong><?php _e('Taxonomy', 'cptg') ?></strong> <span style="color:red;">*</span>
							<br /><?php _e('($taxonomy)', 'cptg') ?>
						</th>
						<td>
							<input type="text" id="tax_name" name="input_tax[taxonomy]" value="<?php if (isset($tax_taxonomy)) echo esc_attr($tax_taxonomy); ?>" maxlength="32" onblur="this.value=this.value.toLowerCase()" />
							<p><?php _e('max. 32 characters, can not contain capital letters or spaces', 'cptg'); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Label', 'cptg') ?></th>
						<td>
							<input type="text" name="input_tax[label]" value="<?php if (isset($tax_label)) { echo esc_attr($tax_label); } ?>" />
							<?php _e('(Default: $taxonomy)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><strong><?php _e('Attached Post Types', 'cptg') ?></strong> <span style="color:red;">*</span></th>
						<td>
							<?php
							$args = array(
								'public' => true
							);
							$output = 'objects'; // or objects
							$post_types = get_post_types( $args, $output );
							foreach ($post_types  as $post_type ) {
								if ( $post_type->name != 'attachment' ) {
								?>
								<label><input type="checkbox" name="tax_post_types[]" value="<?php echo $post_type->name; ?>" <?php if (isset($tax_post_types) && is_array($tax_post_types)) { if (in_array($post_type->name, $tax_post_types)) { echo 'checked="checked"'; } } ?> />&nbsp;<?php echo $post_type->label; ?></label><br />
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
			<h3 class="hndle"><span><?php _e('Label Options', 'cptg'); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('singular name', 'cptg') ?></th>
						<td>
							<input type="text" name="tax_labels[singular_label]" value="<?php if (isset($tax_labels["singular_label"])) { echo esc_attr($tax_labels["singular_label"]); } ?>" />
							<?php _e('(Default: $taxonomy)', 'cptg') ?>
						</td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e('search items', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[search_items]" value="<?php if (isset($tax_labels["search_items"])) { echo esc_attr($tax_labels["search_items"]); } ?>" />
						<?php _e('(Default: Search Tags)', 'cptg') ?>
					</td>
					</tr>
			
					<tr valign="top">
					<th scope="row"><?php _e('popular items', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[popular_items]" value="<?php if (isset($tax_labels["popular_items"])) { echo esc_attr($tax_labels["popular_items"]); } ?>" />
						<?php _e('(Default: Popular Tags)', 'cptg') ?>
					</td>
					</tr>
			
					<tr valign="top">
					<th scope="row"><?php _e('all items', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[all_items]" value="<?php if (isset($tax_labels["all_items"])) { echo esc_attr($tax_labels["all_items"]); } ?>" />
						<?php _e('(Default: All Tags)', 'cptg') ?>
					</td>
					</tr>
			
					<tr valign="top">
					<th scope="row"><?php _e('parent item', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[parent_item]" value="<?php if (isset($tax_labels["parent_item"])) { echo esc_attr($tax_labels["parent_item"]); } ?>" />
						<?php _e('(Default: Parent Category)', 'cptg') ?>
					</td>
					</tr>
			
					<tr valign="top">
					<th scope="row"><?php _e('parent item colon', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[parent_item_colon]" value="<?php if (isset($tax_labels["parent_item_colon"])) { echo esc_attr($tax_labels["parent_item_colon"]); } ?>" />
						<?php _e('(Default: Parent Category)', 'cptg') ?>
					</td>
					</tr>
			
					<tr valign="top">
					<th scope="row"><?php _e('edit tag', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[edit_item]" value="<?php if (isset($tax_labels["edit_item"])) { echo esc_attr($tax_labels["edit_item"]); } ?>" />
						<?php _e('(Default: Edit Tag)', 'cptg') ?>
					</td>
					</tr>
			
					<tr valign="top">
					<th scope="row"><?php _e('update item', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[update_item]" value="<?php if (isset($tax_labels["update_item"])) { echo esc_attr($tax_labels["update_item"]); } ?>" />
						<?php _e('(Default: Update Tag)', 'cptg') ?>
					</td>
					</tr>
			
					<tr valign="top">
					<th scope="row"><?php _e('add new item', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[add_new_item]" value="<?php if (isset($tax_labels["add_new_item"])) { echo esc_attr($tax_labels["add_new_item"]); } ?>" />
						<?php _e('(Default: Add New Tag)', 'cptg') ?>
					</td>
					</tr>
			
					<tr valign="top">
					<th scope="row"><?php _e('new item name', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[new_item_name]" value="<?php if (isset($tax_labels["new_item_name"])) { echo esc_attr($tax_labels["new_item_name"]); } ?>" />
						<?php _e('(Default: New Tag Name)', 'cptg') ?>
					</td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e('separate items with commas', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[separate_items_with_commas]" value="<?php if (isset($tax_labels["separate_items_with_commas"])) { echo esc_attr($tax_labels["separate_items_with_commas"]); } ?>" />
						<?php _e('(Default: Separate tags with commas)', 'cptg') ?>
					</td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e('add or remove items', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[add_or_remove_items]" value="<?php if (isset($tax_labels["add_or_remove_items"])) { echo esc_attr($tax_labels["add_or_remove_items"]); } ?>" />
						<?php _e('(Default: Add or remove tags)', 'cptg') ?>
					</td>
					</tr>
			
					<tr valign="top">
					<th scope="row"><?php _e('choose from most used', 'cptg') ?></th>
					<td>
						<input type="text" name="tax_labels[choose_from_most_used]" value="<?php if (isset($tax_labels["choose_from_most_used"])) { echo esc_attr($tax_labels["choose_from_most_used"]); } ?>" />
						<?php _e('(Default: Choose from the most used tags)', 'cptg') ?>
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
						<th scope="row"><?php _e('Public', 'cptg') ?></th>
						<td>
							<select name="input_tax[public]">
								<?php echo_boolean_options($tax_public, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Show UI', 'cptg') ?></th>
						<td>
							<select name="input_tax[show_ui]">
								<?php echo_boolean_options($tax_show_ui, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Hierarchical', 'cptg') ?></th>
						<td>
							<select name="input_tax[hierarchical]">
								<?php echo_boolean_options($tax_hierarchical, 0); ?>
							</select> <?php _e('(Default: false)', 'cptg') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Rewrite', 'cptg') ?></th>
						<td>
							<select name="input_tax[rewrite]">
								<?php echo_boolean_options($tax_rewrite, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
							<p>
								<?php _e('Custom Rewrite Slug', 'cptg') ?>
								<input type="text" name="input_tax[rewrite_slug]" value="<?php if (isset($tax_rewrite_slug)) { echo esc_attr($tax_rewrite_slug); } ?>" /> <?php _e('(Default: $taxonomy)', 'cptg') ?>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Query Var') ?></th>
						<td>
							<select name="input_tax[query_var]">
								<?php echo_boolean_options($tax_query_var, 1); ?>
							</select> <?php _e('(Default: true)', 'cptg') ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
		
	</div>

	<p class="submit">
		<input type="submit" class="button-primary" name="tax_submit" value="<?php echo $submit_title; ?>" />
	</p>
	
</form>