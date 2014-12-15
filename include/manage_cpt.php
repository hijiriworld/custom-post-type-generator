<?php

global $wpdb;

/************************************************************************************************

	Custom Post Types list

************************************************************************************************/

$results = $pre_result = array();

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

// sort from cptg_order
$cptg_order = get_option('cptg_order');
if ( $cptg_order ) {
	$order = $cptg_order['cptg'];
	foreach( $order as $num ) {
		foreach( $pre_results as $pre_result ) {
			if ( $num == $pre_result->option_id ) {
				$results[] = $pre_result;
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

/************************************************************************************************

	in your Theme list

************************************************************************************************/

$cptg_cpt_names = $theme_cpts = array();

$args = array(
	'public'   => true
);
$output = 'object';
$all_cpts = get_post_types( $args, $output );

foreach( $results as $result ) {
	$cpt = unserialize( $result->option_value );
	$cptg_cpt_names[] = $cpt['post_type'];
}

foreach( $all_cpts as $cpt ) {
	if ( !in_array( strtolower( $cpt->name ), array( 'post', 'page', 'attachment', 'acf' ) ) ) {
		if ( !in_array( strtolower( $cpt->name ), $cptg_cpt_names ) ) {
			$theme_cpts[] = $cpt;
		}
	}
}

?>

<div class="wrap">

<?php screen_icon( 'plugins' ); ?>

<h2>
	<?php _e('Custom Post Types', 'cptg'); ?>
	<a href="<?php echo admin_url('admin.php?page=cptg-regist-cpt'); ?>" class="add-new-h2"><?php _e('Add New', 'cptg'); ?></a>
</h2>

<?php if ( isset($_GET['msg'] )) : ?>
<div id="message" class="updated below-h2">
	<?php if ( $_GET['msg'] == 'add') : ?>
		<p><?php _e('Custom Post Type is new added.', 'cptg'); ?></p>
	<?php elseif ( $_GET['msg'] == 'edit') : ?>
		<p><?php _e('Custom Post Type id edited.', 'cptg'); ?></p>
	<?php elseif ( $_GET['msg'] == 'del') : ?>
		<p><?php _e('Custom Post Type id deleted.', 'cptg'); ?></p>
	<?php endif; ?>
</div>
<?php endif; ?>

<table width="100%" class="widefat">
	<thead>
		<tr>
			<th width="50%"><?php _e('Post Type', 'cptg');?></th>
			<th width="50%"><?php _e('Label', 'cptg');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th width="50%"><?php _e('Post Type', 'cptg');?></th>
			<th width="50%"><?php _e('Label', 'cptg');?></th>
		</tr>
	</tfoot>
	
	<tbody id="cptg-list">
	<?php if ( count( $results ) ) : ?>
		<?php foreach ( $results as $result ) : ?>
			<?php
				$cpt = unserialize( $result->option_value );
				
				$del_url = admin_url( 'admin.php?page=cptg-manage-cpt' ) .'&action=del_cpt&key=' .$result->option_name;
				$del_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($del_url, 'nonce_del_cpt') : $del_url;
				
				$edit_url = admin_url( 'admin.php?page=cptg-regist-cpt' ) .'&action=edit_cpt&key=' .$result->option_name;
				$edit_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($edit_url, 'nonce_regist_cpt') : $edit_url;
			?>
			<tr id="cptg-<?php echo $result->option_id; ?>">
				<td valign="top">
					<strong><a class="row-title" href="<?php echo $edit_url; ?>" title="<?php _e('Edit this item'); ?>"><?php echo stripslashes($cpt['post_type']); ?></a></strong>
					<div class="row-actions">
						<span class="edit"><a href="<?php echo $edit_url; ?>" title="<?php _e('Edit this item'); ?>"><?php _e('Edit', 'cptg'); ?></a> | </span>
						<span class="trash"><a href="<?php echo $del_url; ?>" title="<?php _e('Move this item to the Trash'); ?>"><?php _e('Delete', 'cptg'); ?></a></span>
					</div>
				</td>
				<td valign="top"><?php echo stripslashes($cpt['label']); ?></td>
			</tr>
		<?php endforeach; ?>
	<?php else : ?>
		<tr class="no-items"><td class="colspanchange" colspan="3"><?php _e('No Custom Post Type found.', 'cptg') ?></td></tr>
	<?php endif; ?>
	</tbody>
</table>

<p><?php _e('If you delete Custom Post Type(s), Contents will not delete which belong to that.', 'cptg') ?><br /><?php _e('You can change Order using a Drag and Drop Sortable JavaScript.', 'cptg') ?></p>

<?php if ( count( $theme_cpts ) ) : ?>

<br />

<p><strong><?php _e('Other Custom Post Types', 'cptg') ?></strong></p>

<p><?php _e('The Custom Post Types below are registered in your Theme or WordPress.', 'cptg') ?></p>

<table width="100%" class="widefat">
	<thead>
		<tr>
			<th width="50%"><?php _e('Post Type', 'cptg');?></th>
			<th width="50%"><?php _e('Label', 'cptg');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th width="50%"><?php _e('Post Type', 'cptg');?></th>
			<th width="50%"><?php _e('Label', 'cptg');?></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach( $theme_cpts as $cpt ) : ?>
		<tr>
			<td valign="top">
				<strong><?php echo $cpt->name; ?></strong>
				<div class="row-actions">&nbsp;</div>
			</td>
			<td valign="top"><?php echo $cpt->label; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php endif; ?>

</div>