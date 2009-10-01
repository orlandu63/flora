<?php
class Form extends STemplate {
	public static $input_format = '<input type="text" size="%3$d" value="%2$s" name="%1$s" maxlength="%3$d"/>';

	public function __construct($form, array $form_data, array $template_data = array()) {
		$this->file = 'forms/' . $form;
		$this->form = $this;
		$this->form_data = $form_data;
		$this->set($template_data);
	}

	public function input($name, $max_length) {
		return sprintf(self::$input_format, $name, $this->form_data[$name], $max_length);
	}
}