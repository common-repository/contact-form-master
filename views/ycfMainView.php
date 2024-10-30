<?php
$results = YcfForm::getAllData();
?>
<div class="wrap">
	<h2 class="add-new-buttons">Read Mores <a href="<?php echo admin_url();?>admin.php?page=addType" class="add-new-h2">Add New</a></h2>
</div>
<div class="ycf-upgrade-wrapper">
	<?php if(YCF_PKG == YCF_FREE): ?>
		<input type="button" class="ycf-main-update-to-pro ycf-main-update-main-view" value="Upgrade to PRO version" onclick="window.open('<?php echo YCF_PRO_URL; ?>')">
	<?php endif; ?>
</div>
<div class="ycf-table-wrapper">
	<table class="table table-bordered ycf-table">
		<tr>
			<td>Id</td>
			<td>Title</td> 
			<td>Shortcode</td>
			<td><?php _e('Options', YCF_LANG); ?></td>
		</tr>
		
		<?php if(empty($results)) { ?>
			<tr>
				<td colspan="4">No Form Data</td>
			</tr>
		<?php } 
		else { 
			foreach ($results as $result) { ?>
				<?php $type = $result['type']; if($type == 1) { $type = 'contact'; }?>
				<tr>
				<td><?php echo $result['form_id']; ?></td>
				<td><?php echo $result['title']; ?></td>
				<td><input type="text" onfocus="this.select();" readonly="readonly" value='[ycf_form id="<?php echo $result['form_id'];?>"]' ></td>
				<td>
					<a href="<?php echo admin_url()."admin.php?page=addNewForm&type=".$type."&formId=".$result['form_id'].""?>">Edit</a>
					<a class="ycf-delete-form" data-id="<?php echo $result['form_id'];?>" data-type="<?php echo $type; ?>" href="javascript:void(0)">Delete</a>
				</td>
				</tr>
		<?php } ?>

		<?php } ?>
 
		<tr>
			<td>Id</td>
			<td>Title</td> 
			<td>Shortcode</td>
			<td>Options</td>
		</tr>
	</table>
</div>

