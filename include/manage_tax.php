<div class="wrap">

<?php screen_icon( 'plugins' ); ?>

<h2>
	<?php _e('Custom Taxonomies', 'cptg'); ?>
	<a href="<?php echo admin_url('admin.php?page=regist_tax'); ?>" class="add-new-h2"><?php _e('Add New', 'cptg'); ?></a>
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

<p><?php _e('If you delete Custom Taxonomy, Contents will not delete which belong to that.', 'cptg') ?></p>

<table width="100%" class="widefat">
	<thead>
		<tr>
			<th><?php _e('Taxonomy', 'cptg');?></th>
			<th><?php _e('Label', 'cptg');?></th>
			<th><?php _e('Attached Post Types', 'cptg');?></th>
			<th><?php _e('Hierarchical', 'cptg');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e('Taxonomy', 'cptg');?></th>
			<th><?php _e('Label', 'cptg');?></th>
			<th><?php _e('Attached Post Types', 'cptg');?></th>
			<th><?php _e('Hierarchical', 'cptg');?></th>
		</tr>
	</tfoot>
	
	<?php $cptg_taxs = get_option('cptg_taxs'); ?>
	
	<?php if (is_array($cptg_taxs)) : ?>
	
	<?php
		$counter = 0;
	?>
	
	<?php foreach ($cptg_taxs as $cptg_tax) : ?>
	
		<?php
			$del_url = admin_url( 'admin.php?page=manage_tax' ) . '&action=del_tax&num=' .$counter;
			$del_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($del_url, 'nonce_del_tax') : $del_url;
		
			$edit_url = admin_url( 'admin.php?page=regist_tax' ) . '&action=edit_tax&num=' .$counter;
			$edit_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($edit_url, 'nonce_regist_tax') : $edit_url;
		
			$rewrite_slug = ( $cptg_tax["rewrite_slug"] ) ? $cptg_tax["rewrite_slug"] : $cptg_tax["name"];
		?>
		<tr>
			<td valign="top">
				<strong><a class="row-title" href="<?php echo $edit_url; ?>" title="<?php _e('Edit this item'); ?>"><?php echo stripslashes($cptg_tax["taxonomy"]); ?></a></strong>
				<div class="row-actions">
					<span class="edit"><a href="<?php echo $edit_url; ?>" title="<?php _e('Edit this item'); ?>"><?php _e('Edit', 'cptg'); ?></a> | </span>
					<span class="trash"><a href="<?php echo $del_url; ?>" title="<?php _e('Move this item to the Trash'); ?>"><?php _e('Delete', 'cptg'); ?></a></span>
				</div>
			</td>
			<td valign="top"><?php echo stripslashes($cptg_tax["label"]); ?></td>
			<td valign="top">
			<?php
			if ( isset( $cptg_tax["cpt_name"] ) ) {
				echo stripslashes($cptg_tax["cpt_name"]);
			} elseif ( is_array( $cptg_tax["post_types"] ) ) {
				foreach ($cptg_tax["post_types"] as $cpt_post_types) {
					echo $cpt_post_types .'<br />';
				}
			}
			?>
			</td>
			<td valign="top"><?php echo disp_cptg_boolean($cptg_tax["hierarchical"]); ?></td>
		</tr>
	
	<?php
		$counter++;
	?>
	
	<?php endforeach; ?>

</table>
		
	<?php endif; ?>

</div>
