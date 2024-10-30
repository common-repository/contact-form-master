<?php
	if(!isset($formId)) {
		$formId = 0;
	}
	$type = 'contact';
	if(!empty($_GET['type'])) {
		$type = esc_attr($_GET['type']);
	}
?>
<form method="POST" action="<?php echo admin_url();?>admin-post.php?action=ycf_save_data" id="contact-form-save">
<?php
if (function_exists('wp_nonce_field')) {
	wp_nonce_field('ycf_nonce_check');
}
?>
<?php if(isset($_GET['saved'])) {?>
<div id="default-message" class="updated notice notice-success is-dismissible">
	<p><?php _e('Form updated', YCF_TEXT_DOMAIN)?>.</p>
	<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
</div>
<?php }?>
<div class="ycf-wrapper">
	<div class="ycf-header">
		<h3 class="ycf-edit-title"><?php _e('Create Your Form', YCF_TEXT_DOMAIN)?></h3>
        <div class="ycf-submit-wrapper">
	        <?php if(YCF_PKG == YCF_FREE): ?>
		        <input type="button" class="ycf-main-update-to-pro" value="Upgrade to PRO version" onclick="window.open('<?php echo YCF_PRO_URL; ?>')">
	        <?php endif; ?>
            <input type="submit" class="button-primary"  value="<?php echo 'Save Changes'; ?>">
        </div>
	</div>

	<input type="hidden" name="ycf-form-type" id="ycf-form-type" value="<?php echo $type;?>">
	<input type="hidden" name="ycf-form-id" id="ycf-form-id" value="<?php echo $formId;?>">
    <input type="text" name="ycf-form-title" placeholder="Enter title here" class="form-control" value="<?php echo esc_html($formTitle);?>">

	<?php require_once(YCF_VIEWS.'/'.$type.'/'.'AddNew.php'); ?>
</div>
</form>