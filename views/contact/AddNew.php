<div class="ycf-left-section">
	<div class="ycf-form-wrapper">
		<?php
		$formData = $formBuilderObj->createFormAdminElement();
		?>
		<textarea name="hidden-form-content" style="display: none;"><?php  echo $hiddenInputContent;?></textarea>
	</div>
</div>
<div class="">
	<h3>Form Options</h3>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#home">Home</a></li>
		<li><a href="#menu1">Submit Options</a></li>
		<li><a href="#menu2">Design</a></li>
		<!--			<li><a href="#menu3">Menu 3</a></li>-->
	</ul>
	<div class="tab-content ycf-tab-content">
		<div id="home" class="tab-pane fade in active">
			<?php
			if(file_exists(YCF_CONTACT_VIEWS.'ysfFormFields.php')) {
				require_once(YCF_CONTACT_VIEWS.'ysfFormFields.php');
			}
			?>
		</div>
		<div id="menu1" class="tab-pane fade">
			<?php
			if(file_exists(YCF_CONTACT_VIEWS.'ycfFormOptions.php')) {
				require_once(YCF_CONTACT_VIEWS.'ycfFormOptions.php');
			}
			?>
		</div>
		<div id="menu2" class="tab-pane fade">
			<?php
			if(file_exists(YCF_CONTACT_VIEWS.'ysfFormDesign.php')) {
				require_once(YCF_CONTACT_VIEWS.'ysfFormDesign.php');
			}
			?>
		</div>
		<div id="menu3" class="tab-pane fade">
			<h3>Menu 3</h3>
		</div>
	</div>
</div>