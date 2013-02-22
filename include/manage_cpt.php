<div class="wrap">

<?php screen_icon( 'plugins' ); ?>

<h2>
	<?php _e('Custom Post Types', 'cptg'); ?>
	<a href="<?php echo admin_url('admin.php?page=regist_cpt'); ?>" class="add-new-h2"><?php _e('Add New', 'cptg'); ?></a>
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

<p><?php _e('If you delete Custom Post Type, Contents will not delete which belong to that.', 'cptg') ?></p>

<table width="100%" class="widefat">
	<thead>
		<tr>
			<th><?php _e('Post Type', 'cptg');?></th>
			<th><?php _e('Label', 'cptg');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e('Post Type', 'cptg');?></th>
			<th><?php _e('Label', 'cptg');?></th>
		</tr>
	</tfoot>
	
	<?php $cptg_cpts = get_option('cptg_cpts'); ?>
	
	<?php if (is_array($cptg_cpts)) : ?>
	
		<?php
			$counter = 0;
			$cpt_names = array();
		?>
		
		<?php foreach ($cptg_cpts as $cptg_cpt) : ?>
		
			<?php
				$del_url = admin_url( 'admin.php?page=manage_cpt' ) .'&action=del_cpt&num=' .$counter;
				$del_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($del_url, 'nonce_del_cpt') : $del_url;
				
				$edit_url = admin_url( 'admin.php?page=regist_cpt' ) .'&action=edit_cpt&num=' .$counter;
				$edit_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($edit_url, 'nonce_regist_cpt') : $edit_url;
		
				$rewrite_slug = ( $cptg_cpt["rewrite_slug"] ) ? $cptg_cpt["rewrite_slug"] : $cptg_cpt["post_type"];
			?>
			<tr>
				<td valign="top">
					<strong><a class="row-title" href="<?php echo $edit_url; ?>" title="<?php _e('Edit this item'); ?>"><?php echo stripslashes($cptg_cpt["post_type"]); ?></a></strong>
					<div class="row-actions">
						<span class="edit"><a href="<?php echo $edit_url; ?>" title="<?php _e('Edit this item'); ?>"><?php _e('Edit', 'cptg'); ?></a> | </span>
						<span class="trash"><a href="<?php echo $del_url; ?>" title="<?php _e('Move this item to the Trash'); ?>"><?php _e('Delete', 'cptg'); ?></a></span>
					</div>
				</td>
				<td valign="top"><?php echo stripslashes($cptg_cpt["label"]); ?></td>
<!--
				<td>
					<?php if (is_array($cptg_cpt["supports"])) : foreach ($cptg_cpt["supports"] as $cpt_supports) : ?>
						<?php _e($cpt_supports, 'cptg'); ?><br />
					<?php endforeach; endif; ?>
				</td>
-->
			</tr>
			<?php
				$counter++;
				$cpt_names[] = strtolower( $cptg_cpt["post_type"] );
			?>
		
		<?php endforeach; ?>
		
	<?php endif; ?>

</table>

</div>