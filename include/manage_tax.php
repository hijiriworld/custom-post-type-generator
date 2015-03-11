<?php

global $wpdb;

//	Taxonomies in Custom Post Type Generator

$results = array();

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
		 
$results = $wpdb->get_results($sql);

// Taxonomies in Your Theme or in WordPress

$cptg_tax_names = $theme_taxs = array();

foreach( $results as $result ) {
	$tax = unserialize( $result->option_value );
	$cptg_tax_names[] = $tax["taxonomy"];
}

$builtin_taxs = get_taxonomies( array(
	'_builtin' => true,
	), 'object'
);
$no_builtin_taxs = get_taxonomies( array(
	'_builtin' => false,
	), 'object'
);
foreach( $no_builtin_taxs as $tax ) {
	if ( !in_array( strtolower( $tax->name ), $cptg_tax_names ) ) {
		$theme_taxs[] = $tax;
	}
}
?>

<div class="wrap">

<?php screen_icon( 'plugins' ); ?>

<h2>
	<?php _e('Custom Taxonomies', 'cptg'); ?>
	<a href="<?php echo admin_url('admin.php?page=cptg-regist-tax'); ?>" class="add-new-h2"><?php _e('Add New', 'cptg'); ?></a>
</h2>

<?php if ( isset($_GET['msg'] )) : ?>
<div id="message" class="updated below-h2">
	<?php if ( $_GET['msg'] == 'add') : ?>
		<p><?php _e('Custom Taxonomy is new added.', 'cptg'); ?></p>
	<?php elseif ( $_GET['msg'] == 'edit') : ?>
		<p><?php _e('Custom Taxonomy id edited.', 'cptg'); ?></p>
	<?php elseif ( $_GET['msg'] == 'del') : ?>
		<p><?php _e('Custom Taxonomy id deleted.', 'cptg'); ?></p>
	<?php endif; ?>
</div>
<?php endif; ?>

<table width="100%" class="widefat">
	<thead>
		<tr>
			<th width="25%"><?php _e('Taxonomy', 'cptg') ?></th>
			<th width="25%"><?php _e('Label', 'cptg') ?></th>
			<th width="25%"><?php _e('Attached Post Types', 'cptg') ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th width="25%"><?php _e('Taxonomy', 'cptg') ?></th>
			<th width="25%"><?php _e('Label', 'cptg') ?></th>
			<th width="25%"><?php _e('Attached Post Types', 'cptg') ?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php if ( count( $results ) ) : ?>
		<?php foreach ( $results as $key => $result ) : ?>
			<?php
				$tax = unserialize( $result->option_value );
				
				$del_url = admin_url( 'admin.php?page=cptg-manage-tax' ) . '&action=del_tax&key=' .$result->option_name;
				$del_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($del_url, 'nonce_del_tax') : $del_url;
			
				$edit_url = admin_url( 'admin.php?page=cptg-regist-tax' ) . '&action=edit_tax&key=' .$result->option_name;
				$edit_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($edit_url, 'nonce_regist_tax') : $edit_url;
			?>
			<tr <?php if ( $key%2 == 0 ) echo 'class="alternate"' ?>>
				<td valign="top">
					<strong><a class="row-title" href="<?php echo $edit_url; ?>" title="<?php _e('Edit this item'); ?>"><?php echo stripslashes($tax["taxonomy"]); ?></a></strong>
					<div class="row-actions">
						<span class="edit"><a href="<?php echo $edit_url; ?>" title="<?php _e('Edit this item'); ?>"><?php _e('Edit', 'cptg'); ?></a> | </span>
						<span class="trash"><a href="<?php echo $del_url; ?>" title="<?php _e('Move this item to the Trash'); ?>"><?php _e('Delete', 'cptg'); ?></a></span>
					</div>
				</td>
				<td valign="top"><?php echo stripslashes($tax['labels']['name']); ?></td>
				<td valign="top">
				<?php
				if ( isset( $tax['cpt_name'] ) ) {
					echo stripslashes($tax['cpt_name']);
				} elseif ( is_array( $tax['post_types'] ) ) {
					foreach ($tax['post_types'] as $post_type) {
						echo $post_type.'<br>';
					}
				}
				?>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php else : ?>
		<tr class="no-items"><td class="colspanchange" colspan="3"><?php _e('No Taxonomy found.', 'cptg') ?></td></tr>
	<?php endif; ?>
	</tbody>
</table>

<p><?php _e('If you delete Custom Taxonomy(s), Contents will not delete which belong to that.', 'cptg') ?></p>

<?php if ( count( $theme_taxs ) || count( $builtin_taxs ) ) : ?>

<br>

<h3><?php _e('Other Custom Taxonomies', 'cptg') ?></h3>

<p><?php _e('The Taxonomies below are registered in your Theme or WordPress.', 'cptg') ?></p>

	<?php if ( count( $theme_taxs ) ) : ?>
	
	<p><strong><?php _e('in Your Theme', 'cptg') ?></strong></p>
	
	<table width="100%" class="widefat">
		<thead>
			<tr>
				<th width="25%"><?php _e('Taxonomy', 'cptg') ?></th>
				<th width="25%"><?php _e('Label', 'cptg') ?></th>
				<th width="25%"><?php _e('Attached Post Types', 'cptg') ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th width="25%"><?php _e('Taxonomy', 'cptg') ?></th>
				<th width="25%"><?php _e('Label', 'cptg') ?></th>
				<th width="25%"><?php _e('Attached Post Types', 'cptg') ?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach( $theme_taxs as $key => $tax ) : ?>
			<tr <?php if ( $key%2 == 0 ) echo 'class="alternate"' ?>>
				<td valign="top">
					<strong><?php echo $tax->name; ?></strong>
				</td>
				<td valign="top"><?php echo $tax->label; ?></td>
				<td valign="top">
					<?php
					foreach( $tax->object_type as $object_type ) {
						echo $object_type.'<br>';
					}
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	
	<?php endif; ?>

	<?php if ( count( $builtin_taxs ) ) : ?>
	
	<p><strong><?php _e('builtin', 'cptg') ?></strong></p>
	
	<table width="100%" class="widefat">
		<thead>
			<tr>
				<th width="25%"><?php _e('Taxonomy', 'cptg') ?></th>
				<th width="25%"><?php _e('Label', 'cptg') ?></th>
				<th width="25%"><?php _e('Attached Post Types', 'cptg') ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th width="25%"><?php _e('Taxonomy', 'cptg') ?></th>
				<th width="25%"><?php _e('Label', 'cptg') ?></th>
				<th width="25%"><?php _e('Attached Post Types', 'cptg') ?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach( $builtin_taxs as $key => $tax ) : ?>
			<tr <?php if ( $key%2 == 0 ) echo 'class="alternate"' ?>>
				<td valign="top">
					<strong><?php echo $tax->name; ?></strong>
				</td>
				<td valign="top"><?php echo $tax->label; ?></td>
				<td valign="top">
					<?php
					foreach( $tax->object_type as $object_type ) {
						echo $object_type.'<br>';
					}
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	
	<?php endif; ?>

<?php endif; ?>

</div>
