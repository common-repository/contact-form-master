<?php
require_once(YCF_CLASSES_FORM."YcfForm.php");
class YcfBuilder extends YcfForm {

	public function createFormAdminElement() {

		$formElements = $this->getFormElementsData();

		$content = '';
		$oderKeys = implode(',',array_keys($formElements));

		foreach($formElements as $key => $formElement) {
			$args['oderId'] = $key;
			$content .= YcfFunctions::createAdminViewHtml($formElement, $args);
		}
		$content .= '<input type="hidden" name="contact-fields-order" class="form-element-ordering" value="'.$oderKeys.'">';
		
		return $content;
	}

	public function getFormDefaultConfig() {

		return array();
	}

	public function getFormFields() {

		$formId = $this->getFormId();
		$formData = $this->getFormElementsData();
		$args = array();

		$contactForm = '';

		foreach ($formData as $index => $formInfo) {

			$attrs = array();

			if(!empty($formInfo['attrs'])) {
				$attrs = $formInfo['attrs'];
			}
			$attrStr = $this->createAttrStr($attrs);
			$args['attrs'] = $attrStr;

			switch($formInfo['type']) {

				case 'text':
				case 'email':
				case 'number':
				case 'url':
					$contactForm .= $this->createSimpleInput($formInfo, $index, $args);
					break;
				case 'textarea':
					$contactForm .= $this->createTextareaElement($formInfo, $index, $args);
					break;
				case 'select':
					$contactForm .= $this->createSelectBox($formInfo, $index, $args);
					break;
				case 'submit':
					$contactForm .= $this->createSubmitButton($formInfo, $index, $args);
					break;
				case 'ycfm-text':
				case 'ycfm-email':
				case 'ycfm-number':
				case 'ycfm-url':
					$formInfo['type'] = str_replace('ycfm-', '', $formInfo['type']);
					$contactForm .= $this->createMailchimpSimpleInput($formInfo, $index, $args);
					break;
				case 'ycfm-dropdown':
					$contactForm .= $this->createMailchimpSelectBox($formInfo, $index, $args);
					break;
				case 'ycfm-radio':
					$contactForm .= $this->createMailchimpRadioButtons($formInfo, $index, $args);
					break;
				case 'ycfm-date':
				case 'ycfm-birthday':
					$contactForm .= $this->createMailchimpDatesFields($formInfo, $index, $args);
					break;
				case 'ycfm-phone':
					$contactForm .= $this->createMailchimpPhoneField($formInfo, $index, $args);
					break;
				case 'ycfm-submit':
					$formInfo['type'] = str_replace('ycfm-', '', $formInfo['type']);
					$contactForm .= $this->createSubmitButton($formInfo, $index, $args);
					break;
			}
		}

		return $contactForm;
	}

	public function getOrderedDataFromOptions($formInfo) {

		$data = array();

		if(empty($formInfo['options'])) {
			return $data;
		}
		$options = stripslashes($formInfo['options']);
		$options = json_decode($options, true);
		if(empty($options)) {
			$options = json_decode($formInfo['options'], true);
			if(empty($options)) {
				return $data;
			}
		}

		$fieldOptions = $options['fieldsOptions'];
		$fieldOptions = json_decode($fieldOptions, true);

		$fieldsOrder = $options['fieldsOrder'];
		$fieldsOrder = json_decode($fieldsOrder, true);

		if(empty($fieldOptions) || empty($fieldsOrder)) {
			return $data;
		}

		foreach($fieldsOrder as $orderId) {
			foreach($fieldOptions as $field) {

				if($field['orderId'] == $orderId) {
					$value= $field['value'];
					$data[$value] = $field['label'];
				}
			}
		}

		return $data;
	}

	public function createSelectBox($elementData, $index, $args) {

		$name = $elementData['name'];

		$data = static::getOrderedDataFromOptions($elementData);
		if(empty($data)) {
			return '';
		}
		$isRequired = $this->isRequired($elementData);
		$asterisk = $this->getAsterisk($isRequired);

		$element = '<div class="ycf-form-element" ycf-data-order="'.$index.'">
						<div class="ycf-form-label-wrapper">
							<span class="ycf-form-label">'.$elementData['label'].$asterisk.'</span>
						</div>
						<div class="ycf-form-element-wrapper">
							'. YcfFunctions::createSelectBox($data, '', array('name'=> $name)) .'
						</div>
					</div>';

		return $element;
	}

	public function createMailchimpSelectBox($elementData, $index, $args) {

		$name = $elementData['name'];

		$data = static::getOrderedDataFromOptions($elementData);
		if(empty($data)) {
			return '';
		}
		$isRequired = $this->isRequired($elementData);
		$asterisk = $this->getAsterisk($isRequired);

		$element = '<div class="ycf-form-element" ycf-data-order="'.$index.'">
						<div class="ycf-form-label-wrapper">
							<span class="ycf-form-label">'.$elementData['label'].$asterisk.'</span>
						</div>
						<div class="ycf-form-element-wrapper">
							'. YcfFunctions::createSelectBox($data, '', array('name'=> $name, 'data-error-message-class' => $name.'-error-message')) .'
						</div>
						<div class="'.$name.'-error-message ycf-validate-message"></div>
					</div>';

		return $element;
	}

	public function createMailchimpRadioButtons($elementData, $index, $args) {

		$name = $elementData['name'];

		$data = static::getOrderedDataFromOptions($elementData);
		if(empty($data)) {
			return '';
		}
		$isRequired = $this->isRequired($elementData);
		$asterisk = $this->getAsterisk($isRequired);

		$element = '<div class="ycf-form-element" ycf-data-order="'.$index.'">
						<div class="ycf-form-label-wrapper">
							<span class="ycf-form-label">'.$elementData['label'].$asterisk.'</span>
						</div>
						<div class="ycf-form-element-wrapper">
							'. YcfFunctions::createRadioButtons($data, '', array('name'=> $name, 'data-error-message-class' => $name.'-error-message')) .'
						</div>
						<div class="'.$name.'-error-message ycf-validate-message"></div>
					</div>';

		return $element;
	}

	public function createMailchimpPhoneField($elementData, $index, $args) {

		if(empty($elementData['options'])) {
			return '';
		}
		$options = json_decode($elementData['options'], true);
		if(empty($options) || empty($options['format'])) {
			return '';
		}
		$attrStr = $args['attrs'];
		$isRequired = $this->isRequired($elementData);
		$asterisk = $this->getAsterisk($isRequired);
		$name = $elementData['name'];
		ob_start();
		?>
		<div class="ycf-form-element" ycf-data-order="<?php echo $index; ?>">
			<div class="ycf-form-label-wrapper">
				<span class="ycf-form-label"><?php echo $elementData['label'].$asterisk; ?></span>
			</div>
			<div class="ycf-form-element-wrapper">
				<?php if($options['format'] == 'US'): ?>
					<div class="phonefield phonefield-us">
						(<span class="phonearea">
							<input type="number" name="<?php echo $name.'[area]' ?>" data-class-name="phonePart" maxlength="3" size="3" value="<?php echo @$elementData['value']; ?>" data-error-message-class="<?php echo $name; ?>-error-message">
						</span>)
						<span class="phonedetail1">
							<input type="number" name="<?php echo $name.'[detail1]'; ?>" maxlength="3" size="3" value="<?php echo @$elementData['value']; ?>" data-error-message-class="<?php echo $name; ?>-error-message">
						</span>
						-
						<span class="phonedetail2">
							<input type="number" name="<?php echo $name.'[detail2]'; ?>" maxlength="4" size="4" value="<?php echo @$elementData['value']; ?>" data-error-message-class="<?php echo $name; ?>-error-message">
						</span>
					</div>
				<?php else: ?>
					<input type="number" <?php echo $attrStr; ?> name="<?php echo $name; ?>" value="<?php echo @$elementData['value']; ?>" data-error-message-class="<?php echo $name; ?>-error-message">
				<?php endif; ?>
			</div>
			<div class="<?php echo $name; ?>-error-message ycf-validate-message"></div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();

		return $content;
	}

	public function createMailchimpDatesFields($elementData, $index, $args) {

		if(empty($elementData['options'])) {
			return '';
		}
		$options = json_decode($elementData['options'], true);
		if(empty($options)) {
			return '';
		}
		$name = $elementData['name'];
		$formats = explode('/', $options['format']);
		$isRequired = $this->isRequired($elementData);
		$asterisk = $this->getAsterisk($isRequired);
		if(empty($formats)) {
			return '';
		}
		ob_start();
		?>
		<div class="ycf-form-element" ycf-data-order="<?php echo $index; ?>">
			<div class="ycf-form-label-wrapper">
				<span class="ycf-form-label"><?php echo $elementData['label'].$asterisk; ?></span>
			</div>
			<div class="ycf-form-element-wrapper">
				<?php foreach($formats as $format): ?>
					<?php if($format == 'DD'): ?>
						<div class="subfield dayfield ycf-subfield ycf-form-element-wrapper">
							<input class="datepart ycf-datepart ycf-input ycf-inputs-simple" type="text" pattern="[0-9]*"  value="" placeholder="DD" size="2" maxlength="2" data-error-message-class="<?php echo $name; ?>-error-message" name="<?php echo $name; ?>[day]" id="mce-<?php echo $name; ?>-day">
						</div>
					<?php endif; ?>
					<?php if($format == 'MM'): ?>
						<div class="subfield dayfield ycf-subfield ycf-form-element-wrapper">
							<input class="datepart ycf-datepart ycf-input ycf-inputs-simple" type="text" pattern="[0-9]*"  value="" placeholder="MM" size="2" maxlength="2" data-error-message-class="<?php echo $name; ?>-error-message" name="<?php echo $name; ?>[month]" id="mce-<?php echo $name; ?>-month">
						</div>
					<?php endif; ?>
					<?php if($format == 'YYYY'): ?>
						<div class="subfield dayfield ycf-subfield ycf-form-element-wrapper">
							<input class="datepart ycf-datepart ycf-input ycf-inputs-simple" type="text" pattern="[0-9]*"  value="" placeholder="YYYY" size="2" maxlength="4" data-error-message-class="<?php echo $name; ?>-error-message" name="<?php echo $name; ?>[year]" id="mce-<?php echo $name; ?>-year">
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
				<div class="<?php echo $name; ?>-error-message ycf-validate-message"></div>
			</div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();

		return $content;
	}

	public function createSimpleInput($elementData, $index, $args) {

		$attrStr = $args['attrs'];
		$isRequired = $this->isRequired($elementData);
		$asterisk = $this->getAsterisk($isRequired);
		ob_start();
		?>
		<div class="ycf-form-element" ycf-data-order="<?php echo $index; ?>">
			<div class="ycf-form-label-wrapper">
				<span class="ycf-form-label"><?php echo $elementData['label'].$asterisk; ?></span>
			</div>
			<div class="ycf-form-element-wrapper">
				<input type="<?php echo $elementData['type']; ?>" <?php echo $attrStr; ?> name="<?php echo $elementData['name']; ?>" value="<?php echo @$elementData['value']; ?>">
			</div>
		</div>
		<?php
		$element = ob_get_contents();
		ob_get_clean();

		return $element;
	}

	public function createMailchimpSimpleInput($elementData, $index, $args) {

		$attrStr = $args['attrs'];
		$isRequired = $this->isRequired($elementData);
		$asterisk = $this->getAsterisk($isRequired);
		$name = $elementData['name'];
		ob_start();
		?>
		<div class="ycf-form-element" ycf-data-order="<?php echo $index; ?>">
			<div class="ycf-form-label-wrapper">
				<span class="ycf-form-label"><?php echo $elementData['label'].$asterisk; ?></span>
			</div>
			<div class="ycf-form-element-wrapper">
				<input type="<?php echo $elementData['type']; ?>" <?php echo $attrStr; ?> name="<?php echo $name; ?>" value="<?php echo @$elementData['value']; ?>" data-error-message-class="<?php echo $name; ?>-error-message">
			</div>
			<div class="<?php echo $name; ?>-error-message ycf-validate-message"></div>
		</div>
		<?php
		$element = ob_get_contents();
		ob_get_clean();

		return $element;
	}

	public function createTextareaElement($elementData, $index, $args) {

		$isRequired = $this->isRequired($elementData);
		$asterisk = $this->getAsterisk($isRequired);
		ob_start();
		?>
		<div class="ycf-form-element"  ycf-data-order="<?php echo $index; ?>">
			<div class="ycf-form-label-wrapper">
				<span class="ycf-form-label"><?php echo $elementData['label'].$asterisk; ?></span>
			</div>
			<div class="ycf-form-element-wrapper">
				<textarea name="<?php echo $elementData['name']; ?>" <?php echo $args['attrs']; ?>></textarea>
			</div>
		</div>
		<?php
		$element = ob_get_contents();
		ob_get_clean();

		return $element;
	}

	public function createSubmitButton($elementData, $index, $args) {

		ob_start();
		?>
		<div class="ycf-submit">
			<input type="<?php echo $elementData['type']; ?>" <?php echo $args['attrs']; ?> value="<?php echo $elementData['value']; ?>">
			<img src="<?php echo YCF_IMG_URL; ?>loading.gif" class="ycf-hide ycf-spinner">
		</div>
		<?php
		$element = ob_get_contents();
		ob_get_clean();

		return $element;
	}

	private function createAttrStr($attrs) {

		$attrsStr = '';
		foreach ($attrs as $styleKey => $styleValue) {

			$attrsStr .= $styleKey.'="'.$styleValue.'"; ';
		}

		return $attrsStr;
	}
}