function YcfMailchimp() {

	this.validateObj = {};
	this.fomrId = 0;
}

YcfMailchimp.prototype.setFormId = function(formId) {

	this.formId = formId;
};

YcfMailchimp.prototype.getFormId = function() {

	return this.formId;
};

YcfMailchimp.prototype.setValdateObj = function (validateObj) {

	this.validateObj = validateObj;
};

YcfMailchimp.prototype.getValdateObj = function() {

	return this.validateObj;
};

YcfMailchimp.prototype.validateForm = function() {

	var that = this;
	var formId = this.getFormId();
	var mailchimpForm = jQuery('.ycf-form-'+formId);
	var validateObj = this.getValdateObj();
	jQuery.validator.setDefaults({
		errorPlacement: function(error, element) {
			var errorWrapperClassName = jQuery(element).attr('data-error-message-class');
			console.log(errorWrapperClassName);
			jQuery('.'+errorWrapperClassName).html(error);
		}
	});
	validateObj.submitHandler = function() {
		that.submission(mailchimpForm);
	};
	mailchimpForm.validate(validateObj);
};

YcfMailchimp.prototype.submission = function(mailchimpForm) {

	var serializedForm = mailchimpForm.serialize();
	var formId = mailchimpForm.attr('data-id');
	formId = parseInt(formId);
	var nonce = YcfMailchimpArgs.nonce;
	var ajaxUrl = YcfMailchimpArgs.ajaxUrl;

	var data = {
		action: 'ycf_mailchimp_submission',
		nonce: nonce,
		beforeSend: function() {
			mailchimpForm.find('.ycf-spinner').removeClass('ycf-hide');
			mailchimpForm.find('#mc-embedded-subscribe').prop('disabled', true);
		},
		formId: formId,
		serializedForm: serializedForm
	};
	jQuery.post(ajaxUrl, data, function(result, d) {
		jQuery('.ycf-alert').addClass('ycf-hide');

		if(result == 400) {
			jQuery('.ycf-alert-danger').removeClass('ycf-hide');
		}
		else {
			jQuery('.ycf-alert-success').removeClass('ycf-hide');
		}

		mailchimpForm.find('#mc-embedded-subscribe').prop('disabled', false);
		mailchimpForm.find('.ycf-spinner').addClass('ycf-hide');
		console.log(result);
	});
};