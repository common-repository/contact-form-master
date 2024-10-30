<div class="ycf-bootstrap-wrapper">
	<div class="ycf-upgrade-wrapper">
		<?php if(YCF_PKG == YCF_FREE): ?>
			<input type="button" class="ycf-main-update-to-pro ycf-main-update-main-view" value="Upgrade to PRO version" onclick="window.open('<?php echo YCF_PRO_URL; ?>')">
		<?php endif; ?>
	</div>
	<div class="claer"></div>
	<h2>Add New Contact form Type</h2>
	<div class="product-banner" onclick="location.href = '<?php echo admin_url();?>admin.php?page=addNewForm&type=contact'">
		<div class="ycf-types ycf-contact-form">
		</div>
	</div>
	<?php if(YCF_PKG == YCF_FREE): ?>
		<div class="product-banner" onclick="window.open('<?php echo YCF_PRO_URL; ?>')">
			<div class="yrm-types ycf-mailchimp-pro">
			</div>
		</div>
	<?php endif; ?>
	<?php if(YCF_PKG >= YCF_SILVER): ?>
		<div class="product-banner" onclick="location.href = '<?php echo admin_url();?>admin.php?page=addNewForm&type=mailchimp'">
			<div class="ycf-types ycf-mailchimp">
			</div>
		</div>
	<?php endif; ?>
</div>