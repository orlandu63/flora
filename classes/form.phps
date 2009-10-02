<?php
class Form extends STemplate {
	const POST = 'post';
	const SEARCH = 'search';

	const POST_THREAD = 1;
	const POST_TOPIC  = 2;

	public static $input_format = '<input type="text" size="%3$d" value="%2$s" name="%1$s" maxlength="%3$d"/>';
	public static $textarea_format = '<textarea name="%1$s" rows="%3$d" cols="%4$d">%2$s</textarea>';

	public function __construct($form, array $form_data, array $template_data = array()) {
		$this->file = 'forms/' . $form;
		$this->form = $this;
		$this->form_data = $form_data;
		$this->set($template_data);
	}
	
	public static function preparePostForm($type, array $form_data = array()) {
		if(empty($data)) {
			$form_data = array(
				'post' => InputValidation::filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT),
				'author' => User::$name,
				'title' => InputValidation::filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS),
				'body' => InputValidation::filter_input(INPUT_POST, 'body', FILTER_SANITIZE_SPECIAL_CHARS)
			);
		}
		$params = array();
		switch($type) {
			case self::POST_THREAD:
				$header = 'Reply';
				$params['post'] = $form_data['post'];
				$legend = 'Post Info';
				$submit_value = 'Post Reply';
				break;
			case self::POST_TOPIC:
				$header = 'Create a Topic';
				$legend = 'Topic Info';
				$submit_value = 'Make Topic';
				break;
		}
		return new self(self::POST,
			$form_data,
			array(
				'header' => $header,
				'params' => $params,
				'legend' => $legend,
				'type' => $type,
				'submit_value' => $submit_value
			)
		);
	}
	
	protected function format($format, $name, array $extra_arguments = array()) {
		$arguments = $extra_arguments;
		array_unshift($arguments, $name, $this->form_data[$name]);
		return vsprintf($format, $arguments);
	}

	public function input($name, $max_length) {
		return $this->format(self::$input_format, $name, array($max_length));
	}
	
	public function textarea($name, $rows, $cols) {
		return $this->format(self::$textarea_format, $name, array($rows, $cols));
	}
}