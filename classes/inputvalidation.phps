<?php
abstract class InputValidation {
	const VALIDATE_AUTHOR = 1;
	const VALIDATE_BODY = 2;
	const VALIDATE_TITLE = 4;
	
	protected static $length_exception_format =
		'<strong>%s</strong> must be between %d and %d characters long: its current length is %d character(s).';
	protected static $length_exception_addendum =
		'Note that "&lt;," "&gt;" and "&amp;" are actually 4, 4 and 5 characters in web form, respectively.';
	
	public static function validateInt($name, $min = false, $max = false, $input = INPUT_GET) {
		$options = array();
		if($min !== false) {
			$options['min_range'] = $min;
		}
		if($max !== false) {
			$options['max_range'] = $max;
		}
		$options = array('options' => $options);
		$int = ($input ?
			filter_input($input, $name, FILTER_VALIDATE_INT, $options) :
			filter_var($name, FILTER_VALIDATE_INT, $options)
		);
		return (int)$int;
	}
	
	public static function validateLength($name, $data, $max_length, $min_length = 1) {
		$length = strlen($data);
		if($length > $max_length || $length < $min_length) {
			throw new LengthException(
				sprintf(self::$length_exception_format, ucfirst($name), $min_length, $max_length, $length) .
				($length > $max_length ? '<br/>' . self::$length_exception_addendum : '')
			);
		}
	}
	
	public static function validateAuthor() {
		$author = trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS));
		self::validateLength('name', $author, User::MAX_AUTHOR_LENGTH, 0);
		return $author;
	}
	
	public static function validateBody() {
		$body = filter_input(INPUT_POST, 'body');
		//validate before and after so that this weak server wont have to parse a huge piece of text
		$validateLength = function() use($body) {
			$self = __CLASS__;
			$self::validateLength('body', $body, Posts::MAX_BODY_LENGTH, 1);
		};
		$validateLength();
		$parser = Markdown::getInstance();
		$body = $parser->transform($body);
		$validateLength();
		return $body;
	}
	
	public static function validateTitle() {
		$title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS));
		self::validateLength('title', $title, Topics::MAX_TITLE_LENGTH);
		return $title;
	}
}