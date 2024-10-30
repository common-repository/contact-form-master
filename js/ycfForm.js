function YcfForm() {
    this.formId = '';
    this.formData = {};
}

YcfForm.prototype.setFormId= function (formId) {
    this.formId = formId;
};

YcfForm.prototype.getFormId = function () {
    return this.formId;
};

YcfForm.prototype.setFormData = function (formData) {
    var formId = this.getFormId();

    try {
        ycfFormData = eval(formData+formId)
    }
    catch(err) {
        var ycfFormData = {};
    }

    this.formData = ycfFormData;
};

YcfForm.prototype.getFormData = function () {
    return this.formData;
};

YcfForm.prototype.init = function (formid) {

    this.setFormId(formid);
    this.setFormData('YcfFormData');
    var that = this;

    var formData = this.getFormData();
    if(Object.keys(formData).length === 0) {
        console.log("Your Contact form Data is invalid");
        return true;
    }
    var firstEmail = jQuery('.ycf-contact-form').find('input[type="email"]').first().attr('name');
	var ycfValidateForm = {rules : {}, messages: {}};
	ycfValidateForm['rules'][firstEmail] = {
		required: true,
		email: true
    };
	jQuery.extend(jQuery.validator.messages, {
		email: formData.ycfValidateEmail
    });

    ycfValidateForm.submitHandler = function () {
        var settings = that.getFormData();

	    jQuery('.ycf-spinner').removeClass('ycf-hide');
        data = {
            'action': 'contactForm',
            'formData': jQuery('.ycf-contact-form').serialize(),
            'ajaxNonce': formData.ajaxNonce,
            'formSettings': settings
        };
        jQuery.post(formData.ycfWpAjaxUrl, data, function(response) {
	        jQuery('.ycf-contact-form').before('Thank you for contacting us!');
	        jQuery('.ycf-contact-form').hide();
	        jQuery('.ycf-spinner').addClass('ycf-hide');
            console.log(response);
        });
        // console.log(jQuery('.ycf-contact-form').serialize());
    };
    var formValidateObject = jQuery('.ycf-contact-form').validate(ycfValidateForm);

};
